<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Traits\RoleCheckTrait;

class CustomerController extends Controller
{
    use RoleCheckTrait;

    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager', 'cashier']);
        if ($check !== true) return $check;

        try {
            $hasLastVisit = Schema::hasColumn('customers', 'last_visit');
            $hasTier = Schema::hasColumn('customers', 'tier');
            $hasLoyaltyPoints = Schema::hasColumn('customers', 'loyalty_points');
            $hasTotalSpent = Schema::hasColumn('customers', 'total_spent');
            
            $query = Customer::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('customer_code', 'like', "%{$search}%")
                      ->orWhere('id_number', 'like', "%{$search}%");
                });
            }

            if ($hasTier && $request->filled('tier')) {
                $query->where('tier', $request->tier);
            }

            if ($hasLastVisit && $request->filled('active') && $request->active === 'active') {
                $query->where('last_visit', '>=', now()->subMonths(6));
            }

            $sortField = $request->get('sort', 'created_at');
            $sortDirection = $request->get('direction', 'desc');
            
            $allowedSortFields = ['created_at', 'name'];
            if ($hasTotalSpent) $allowedSortFields[] = 'total_spent';
            if ($hasLoyaltyPoints) $allowedSortFields[] = 'loyalty_points';
            
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }
            
            $query->orderBy($sortField, $sortDirection);

            $customers = $query->paginate(15);

            $stats = [
                'total'         => Customer::count(),
                'active'        => $hasLastVisit ? Customer::where('last_visit', '>=', now()->subMonths(6))->count() : 0,
                'bronze'        => $hasTier ? Customer::where('tier', 'bronze')->count() : 0,
                'silver'        => $hasTier ? Customer::where('tier', 'silver')->count() : 0,
                'gold'          => $hasTier ? Customer::where('tier', 'gold')->count() : 0,
                'platinum'      => $hasTier ? Customer::where('tier', 'platinum')->count() : 0,
                'total_points'  => $hasLoyaltyPoints ? Customer::sum('loyalty_points') : 0,
                'total_spent'   => $hasTotalSpent ? Customer::sum('total_spent') : 0,
            ];

            $breadcrumbs = [
                ['title' => 'Dashboard', 'url' => route('dashboard')],
                ['title' => 'Customers', 'url' => route('customers.index')],
            ];

            return view('pages.customers.index', compact('customers', 'stats', 'breadcrumbs'));

        } catch (\Exception $e) {
            Log::error('Customer index error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
        }
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $check = $this->checkRole(['admin', 'manager', 'cashier']);
        if ($check !== true) return $check;

        $customerCode = Customer::generateCustomerCode();

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Customers', 'url' => route('customers.index')],
            ['title' => 'Add Customer', 'url' => route('customers.create')],
        ];

        return view('pages.customers.create', compact('customerCode', 'breadcrumbs'));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager', 'cashier']);
        if ($check !== true) return $check;

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email|unique:customers,email',
            'phone'       => 'nullable|string|max:20',
            'phone_2'     => 'nullable|string|max:20',
            'birth_date'  => 'nullable|date',
            'id_number'   => 'nullable|string|max:50',
            'gender'      => 'nullable|in:male,female,other',
            'address'     => 'nullable|string',
            'city'        => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country'     => 'nullable|string|max:100',
            'sms_opt_in'  => 'nullable|boolean',
            'email_opt_in'=> 'nullable|boolean',
            'notes'       => 'nullable|string',
        ]);

        $validated['customer_code'] = Customer::generateCustomerCode();
        $validated['member_since']  = now();
        $validated['sms_opt_in']    = $request->boolean('sms_opt_in');
        $validated['email_opt_in']  = $request->boolean('email_opt_in');
        $validated['loyalty_points'] = 0;
        $validated['total_spent']    = 0;
        $validated['total_visits']   = 0;
        $validated['tier']           = 'bronze';
        $validated['country']        = $validated['country'] ?? 'Kenya';

        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
        }

        try {
            $customer = Customer::create($validated);

            // Log activity
            auth()->user()->logActivity('create', 'customers', 'Created customer: ' . $customer->name);

            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            Log::error('Customer creation failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to create customer. Please try again.');
        }
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        $check = $this->checkRole(['admin', 'manager', 'cashier']);
        if ($check !== true) return $check;

        try {
            $customer->load(['sales' => function ($query) {
                $query->with('items.product')
                      ->orderBy('created_at', 'desc')
                      ->limit(20);
            }]);

            $hasLastVisit     = Schema::hasColumn('customers', 'last_visit');
            $hasTotalSpent    = Schema::hasColumn('customers', 'total_spent');
            $hasLoyaltyPoints = Schema::hasColumn('customers', 'loyalty_points');

            $stats = [
                'total_sales'     => $customer->sales()->count(),
                'total_spent'     => $hasTotalSpent ? $customer->total_spent : 0,
                'average_sale'    => $customer->sales()->avg('total_amount') ?? 0,
                'last_sale'       => $hasLastVisit ? $customer->last_visit : null,
                'points_earned'   => $hasLoyaltyPoints ? $customer->loyalty_points : 0,
                'tier_discount'   => method_exists($customer, 'getDiscountPercentage') ? $customer->getDiscountPercentage() : 0,
            ];

            $breadcrumbs = [
                ['title' => 'Dashboard', 'url' => route('dashboard')],
                ['title' => 'Customers', 'url' => route('customers.index')],
                ['title' => $customer->name, 'url' => route('customers.show', $customer)],
            ];

            return view('pages.customers.show', compact('customer', 'stats', 'breadcrumbs'));
        } catch (\Exception $e) {
            Log::error('Customer show error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load customer details.');
        }
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        $check = $this->checkRole(['admin', 'manager', 'cashier']);
        if ($check !== true) return $check;

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Customers', 'url' => route('customers.index')],
            ['title' => 'Edit Customer', 'url' => route('customers.edit', $customer)],
        ];

        return view('pages.customers.edit', compact('customer', 'breadcrumbs'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $check = $this->checkRole(['admin', 'manager', 'cashier']);
        if ($check !== true) return $check;

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone'       => 'nullable|string|max:20',
            'phone_2'     => 'nullable|string|max:20',
            'birth_date'  => 'nullable|date',
            'id_number'   => 'nullable|string|max:50',
            'gender'      => 'nullable|in:male,female,other',
            'address'     => 'nullable|string',
            'city'        => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country'     => 'nullable|string|max:100',
            'sms_opt_in'  => 'nullable|boolean',
            'email_opt_in'=> 'nullable|boolean',
            'notes'       => 'nullable|string',
        ]);

        $validated['sms_opt_in']   = $request->boolean('sms_opt_in');
        $validated['email_opt_in'] = $request->boolean('email_opt_in');

        if (auth()->check()) {
            $validated['updated_by'] = auth()->id();
        }

        try {
            $customer->update($validated);

            // Log activity
            auth()->user()->logActivity('update', 'customers', 'Updated customer: ' . $customer->name);

            return redirect()->route('customers.index')
                ->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            Log::error('Customer update failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update customer. Please try again.');
        }
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        $check = $this->checkRole(['admin']);
        if ($check !== true) return $check;

        if ($customer->sales()->count() > 0) {
            return back()->with('error', 'Cannot delete customer because they have sales history.');
        }

        try {
            $name = $customer->name;
            $customer->delete();

            // Log activity
            auth()->user()->logActivity('delete', 'customers', 'Deleted customer: ' . $name);

            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Customer deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete customer.');
        }
    }

    /**
     * Display customer loyalty dashboard.
     */
    public function loyalty()
    {
        $check = $this->checkRole(['admin', 'manager']);
        if ($check !== true) return $check;

        try {
            $hasTotalSpent    = Schema::hasColumn('customers', 'total_spent');
            $hasLoyaltyPoints = Schema::hasColumn('customers', 'loyalty_points');
            $hasTier          = Schema::hasColumn('customers', 'tier');

            $topCustomers = $hasTotalSpent
                ? Customer::orderBy('total_spent', 'desc')->limit(10)->get()
                : Customer::latest()->limit(10)->get();

            $recentJoins = Customer::latest()->limit(10)->get();

            $tierStats = [
                'bronze'   => $hasTier ? Customer::where('tier', 'bronze')->count() : 0,
                'silver'   => $hasTier ? Customer::where('tier', 'silver')->count() : 0,
                'gold'     => $hasTier ? Customer::where('tier', 'gold')->count() : 0,
                'platinum' => $hasTier ? Customer::where('tier', 'platinum')->count() : 0,
            ];

            $totalPoints = $hasLoyaltyPoints ? Customer::sum('loyalty_points') : 0;
            $totalSpent  = $hasTotalSpent ? Customer::sum('total_spent') : 0;

            $breadcrumbs = [
                ['title' => 'Dashboard', 'url' => route('dashboard')],
                ['title' => 'Customers', 'url' => route('customers.index')],
                ['title' => 'Loyalty Dashboard', 'url' => route('customers.loyalty')],
            ];

            return view('pages.customers.loyalty', compact(
                'topCustomers',
                'recentJoins',
                'tierStats',
                'totalPoints',
                'totalSpent',
                'breadcrumbs'
            ));
        } catch (\Exception $e) {
            Log::error('Loyalty dashboard error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load loyalty dashboard.');
        }
    }

    /**
     * API endpoint to search customers (for POS).
     */
    public function search(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager', 'cashier']);
        if ($check !== true) return $check;

        try {
            $query = $request->get('q', '');

            $customers = Customer::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('customer_code', 'like', "%{$query}%")
                ->orderBy('name')
                ->limit(20)
                ->get(['id', 'customer_code', 'name', 'email', 'phone', 'tier', 'loyalty_points']);

            return response()->json($customers);
        } catch (\Exception $e) {
            Log::error('Customer search error: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }
}