<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $hasStatusColumn = Schema::hasColumn('suppliers', 'status');

        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($hasStatusColumn && $request->filled('status')) {
            $query->where('status', $request->status);
        }

        $suppliers = $query->paginate(15);

        $total = Supplier::count();

        if ($hasStatusColumn) {
            $active = Supplier::where('status', 'active')->count();
            $inactive = Supplier::where('status', 'inactive')->count();
        } else {
            if (Schema::hasColumn('suppliers', 'is_active')) {
                $active = Supplier::where('is_active', 1)->count();
                $inactive = Supplier::where('is_active', 0)->count();
            } else {
                $active = $total;
                $inactive = 0;
            }
        }

        $stats = [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
        ];

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Suppliers', 'url' => route('suppliers.index')],
        ];

        return view('pages.suppliers.index', compact('suppliers', 'stats', 'breadcrumbs'));
    }

    public function create()
    {
        $supplierCode = 'SUP-' . date('ym') . '-0001';
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Suppliers', 'url' => route('suppliers.index')],
            ['title' => 'Add Supplier', 'url' => route('suppliers.create')],
        ];
        return view('pages.suppliers.create', compact('supplierCode', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $validated['supplier_code'] = Supplier::generateSupplierCode();
        $validated['status'] = $request->status ?? 'active';

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        try {
            Supplier::create($validated);
            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier created successfully.');
        } catch (\Exception $e) {
            Log::error('Supplier creation failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to create supplier. Please try again.');
        }
    }

    public function show(Supplier $supplier)
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Suppliers', 'url' => route('suppliers.index')],
            ['title' => $supplier->name, 'url' => route('suppliers.show', $supplier)],
        ];
        return view('pages.suppliers.show', compact('supplier', 'breadcrumbs'));
    }

    public function edit(Supplier $supplier)
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Suppliers', 'url' => route('suppliers.index')],
            ['title' => 'Edit Supplier', 'url' => route('suppliers.edit', $supplier)],
        ];
        return view('pages.suppliers.edit', compact('supplier', 'breadcrumbs'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $validated['status'] = $request->status ?? $supplier->status;

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        try {
            $supplier->update($validated);
            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier updated successfully.');
        } catch (\Exception $e) {
            Log::error('Supplier update failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update supplier. Please try again.');
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            if (method_exists($supplier, 'products') && $supplier->products()->count() > 0) {
                return back()->with('error', 'Cannot delete supplier because it has associated products.');
            }
            $supplier->delete();
            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Supplier deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete supplier. Please try again.');
        }
    }
}
