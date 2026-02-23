<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PosController extends Controller
{
    public function index(Request $request)
    {
        // Get selected customer from session
        $selectedCustomer = session('pos_customer');
        
        $products = Product::with('category')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $cart = session()->get('pos_cart', []);

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Point of Sale', 'url' => route('pos.index')],
        ];

        return view('pages.pos.index', compact('products', 'cart', 'breadcrumbs', 'selectedCustomer'));
    }

    /**
     * Search for customers (AJAX)
     */
    public function searchCustomers(Request $request)
    {
        $query = $request->get('q', '');
        
        $customers = Customer::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('customer_code', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'customer_code', 'name', 'email', 'phone', 'tier', 'loyalty_points']);

        // Add tier badge HTML for each customer
        foreach ($customers as $customer) {
            $customer->tier_badge = $customer->tier_badge;
            $customer->discount = method_exists($customer, 'getDiscountPercentage') ? $customer->getDiscountPercentage() : 0;
        }

        return response()->json($customers);
    }

    /**
     * Select a customer for the current POS session
     */
    public function selectCustomer(Request $request)
    {
        $request->validate(['customer_id' => 'required|exists:customers,id']);
        
        $customer = Customer::find($request->customer_id);
        session(['pos_customer' => $customer]);
        
        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'code' => $customer->customer_code,
                'tier' => $customer->tier,
                'tier_badge' => $customer->tier_badge,
                'points' => $customer->loyalty_points,
                'discount' => $customer->getDiscountPercentage()
            ]
        ]);
    }

    /**
     * Clear selected customer
     */
    public function clearCustomer()
    {
        session()->forget('pos_customer');
        return response()->json(['success' => true]);
    }

    /**
     * Quick add customer from POS
     */
    public function quickAddCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:customers,email',
        ]);

        $validated['customer_code'] = Customer::generateCustomerCode();
        $validated['member_since'] = now();
        $validated['loyalty_points'] = 0;
        $validated['total_spent'] = 0;
        $validated['total_visits'] = 0;
        $validated['tier'] = 'bronze';
        $validated['country'] = 'Kenya';
        $validated['sms_opt_in'] = false;
        $validated['email_opt_in'] = false;

        try {
            $customer = Customer::create($validated);
            session(['pos_customer' => $customer]);
            
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'code' => $customer->customer_code,
                    'tier' => $customer->tier,
                    'tier_badge' => $customer->tier_badge,
                    'points' => $customer->loyalty_points,
                    'discount' => $customer->getDiscountPercentage()
                ],
                'message' => 'Customer added successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Quick add customer failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add customer'
            ], 500);
        }
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->stock_quantity
            ], 422);
        }

        $cart = session()->get('pos_cart', []);

        if (isset($cart[$product->id])) {
            $newQty = $cart[$product->id]['quantity'] + $request->quantity;
            if ($product->stock_quantity < $newQty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . $product->stock_quantity
                ], 422);
            }
            $cart[$product->id]['quantity'] = $newQty;
            $cart[$product->id]['subtotal'] = $newQty * $cart[$product->id]['unit_price'];
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit_price' => $product->price,
                'quantity' => $request->quantity,
                'subtotal' => $request->quantity * $product->price,
            ];
        }

        session()->put('pos_cart', $cart);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => $this->calculateCartTotal($cart),
        ]);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session()->get('pos_cart', []);

        if ($request->quantity == 0) {
            unset($cart[$request->product_id]);
        } else {
            $product = Product::findOrFail($request->product_id);
            if ($product->stock_quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . $product->stock_quantity
                ], 422);
            }
            $cart[$request->product_id]['quantity'] = $request->quantity;
            $cart[$request->product_id]['subtotal'] = $request->quantity * $cart[$request->product_id]['unit_price'];
        }

        session()->put('pos_cart', $cart);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => $this->calculateCartTotal($cart),
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate(['product_id' => 'required']);

        $cart = session()->get('pos_cart', []);
        unset($cart[$request->product_id]);
        session()->put('pos_cart', $cart);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => $this->calculateCartTotal($cart),
        ]);
    }

    public function clearCart()
    {
        session()->forget('pos_cart');
        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,mobile_money,credit',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $cart = session()->get('pos_cart', []);
        $selectedCustomer = session('pos_customer');

        if (empty($cart)) {
            return back()->with('error', 'Cart is empty.');
        }

        // Calculate total from cart (VAT inclusive)
        $totalInclusive = $this->calculateCartTotal($cart);

        // Apply customer discount if applicable
        $discountAmount = 0;
        if ($selectedCustomer && method_exists($selectedCustomer, 'getDiscountPercentage')) {
            $discountPercentage = $selectedCustomer->getDiscountPercentage();
            $discountAmount = round($totalInclusive * $discountPercentage / 100, 2);
            $totalInclusive = $totalInclusive - $discountAmount;
        }

        // Extract tax from total
        $taxRate = config('pos.tax_rate', 16);
        $subtotal = round($totalInclusive / (1 + ($taxRate / 100)), 2);
        $tax = $totalInclusive - $subtotal;

        if ($request->amount_paid < $totalInclusive) {
            return back()->with('error', 'Amount paid is less than total.')->withInput();
        }

        $change = $request->amount_paid - $totalInclusive;

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'invoice_number' => Sale::generateInvoiceNumber(),
                'user_id' => auth()->id(),
                'customer_id' => $selectedCustomer ? $selectedCustomer->id : null,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalInclusive,
                'amount_paid' => $request->amount_paid,
                'change' => $change,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            foreach ($cart as $item) {
                // Calculate item subtotal excluding tax
                $itemSubtotal = round($item['unit_price'] / (1 + ($taxRate / 100)), 2);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $itemSubtotal,
                    'subtotal' => $itemSubtotal * $item['quantity'],
                    'total_price' => $item['subtotal'],
                ]);

                $product = Product::find($item['id']);
                $product->updateStock(
                    -$item['quantity'],
                    'sale',
                    $sale->invoice_number,
                    'Sold via POS'
                );
            }

            // Update customer data if customer selected
            if ($selectedCustomer) {
                $selectedCustomer->total_spent += $totalInclusive;
                $selectedCustomer->total_visits += 1;
                $selectedCustomer->last_visit = now();
                $selectedCustomer->addLoyaltyPoints($totalInclusive);
                $selectedCustomer->updateTier();
                $selectedCustomer->save();
            }

            DB::commit();
            
            // Clear cart and customer session
            session()->forget('pos_cart');
            session()->forget('pos_customer');

            return redirect()->route('pos.receipt', $sale)
                ->with('success', 'Sale completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to process sale.')->withInput();
        }
    }

    public function receipt(Sale $sale)
    {
        $sale->load('items.product', 'user', 'customer');

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'POS', 'url' => route('pos.index')],
            ['title' => 'Receipt', 'url' => route('pos.receipt', $sale)],
        ];

        return view('pages.pos.receipt', compact('sale', 'breadcrumbs'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $products = Product::with('category')
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'sku', 'price', 'stock_quantity']);

        return response()->json($products);
    }

    private function calculateCartTotal(array $cart): float
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['subtotal'];
        }
        return round($total, 2);
    }
}