<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('supplier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => PurchaseOrder::count(),
            'draft' => PurchaseOrder::where('status', 'draft')->count(),
            'ordered' => PurchaseOrder::where('status', 'ordered')->count(),
            'received' => PurchaseOrder::where('status', 'received')->count(),
        ];

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Purchase Orders', 'url' => route('purchase-orders.index')],
        ];

        return view('pages.purchase-orders.index', compact('orders', 'stats', 'breadcrumbs'));
    }

    public function create()
    {
        // Get suppliers (active ones)
        $suppliers = Supplier::where('is_active', 1)->get();
        
        // Get active products with their categories
        $products = Product::where('status', 'active')->with('category')->get();
        
        // Generate PO number
        $poNumber = PurchaseOrder::generatePONumber();

        // Check if we have data
        if ($suppliers->isEmpty()) {
            session()->flash('warning', 'No active suppliers found. Please add suppliers first.');
        }
        
        if ($products->isEmpty()) {
            session()->flash('warning', 'No active products found. Please add products first.');
        }

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Purchase Orders', 'url' => route('purchase-orders.index')],
            ['title' => 'Create PO', 'url' => route('purchase-orders.create')],
        ];

        return view('pages.purchase-orders.create', compact('suppliers', 'products', 'poNumber', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $items = [];

            foreach ($request->items as $item) {
                $total = $item['quantity'] * $item['unit_cost'];
                $subtotal += $total;
                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $total,
                ];
            }

            $tax = 0;
            $discount = 0;
            $total = $subtotal + $tax - $discount;

            $order = PurchaseOrder::create([
                'po_number' => $validated['po_number'] ?? PurchaseOrder::generatePONumber(),
                'supplier_id' => $validated['supplier_id'],
                'user_id' => auth()->id(),
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'status' => 'draft',
                'notes' => $validated['notes'],
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            return redirect()->route('purchase-orders.show', $order)
                ->with('success', 'Purchase order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PO creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create purchase order: ' . $e->getMessage())->withInput();
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items.product', 'user');

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Purchase Orders', 'url' => route('purchase-orders.index')],
            ['title' => $purchaseOrder->po_number, 'url' => route('purchase-orders.show', $purchaseOrder)],
        ];

        return view('pages.purchase-orders.show', compact('purchaseOrder', 'breadcrumbs'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'ordered'])) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only draft or ordered orders can be edited.');
        }

        $suppliers = Supplier::where('is_active', 1)->get();
        $products = Product::where('status', 'active')->with('category')->get();

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Purchase Orders', 'url' => route('purchase-orders.index')],
            ['title' => $purchaseOrder->po_number, 'url' => route('purchase-orders.show', $purchaseOrder)],
            ['title' => 'Edit', 'url' => route('purchase-orders.edit', $purchaseOrder)],
        ];

        return view('pages.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products', 'breadcrumbs'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'ordered'])) {
            return back()->with('error', 'This order cannot be edited.');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $items = [];

            foreach ($request->items as $item) {
                $total = $item['quantity'] * $item['unit_cost'];
                $subtotal += $total;
                $items[$item['product_id']] = [
                    'quantity_ordered' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $total,
                ];
            }

            $tax = 0;
            $discount = 0;
            $total = $subtotal + $tax - $discount;

            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'notes' => $validated['notes'],
            ]);

            // Delete old items
            $purchaseOrder->items()->delete();

            // Create new items
            foreach ($items as $productId => $itemData) {
                $purchaseOrder->items()->create([
                    'product_id' => $productId,
                    'quantity_ordered' => $itemData['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit_cost' => $itemData['unit_cost'],
                    'total' => $itemData['total'],
                ]);
            }

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PO update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update purchase order.')->withInput();
        }
    }

    public function markOrdered(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be marked as ordered.');
        }

        $purchaseOrder->update(['status' => 'ordered']);

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Order marked as ordered.');
    }

    public function receiveForm(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['ordered', 'partial'])) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only ordered or partially received orders can be received.');
        }

        $purchaseOrder->load('items.product');

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Purchase Orders', 'url' => route('purchase-orders.index')],
            ['title' => $purchaseOrder->po_number, 'url' => route('purchase-orders.show', $purchaseOrder)],
            ['title' => 'Receive Stock', 'url' => route('purchase-orders.receive-form', $purchaseOrder)],
        ];

        return view('pages.purchase-orders.receive', compact('purchaseOrder', 'breadcrumbs'));
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['ordered', 'partial'])) {
            return back()->with('error', 'This order cannot receive stock.');
        }

        $validated = $request->validate([
            'received' => 'required|array',
            'received.*' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $purchaseOrder->receive($validated['received']);
            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Stock received successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PO receive failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to receive stock.')->withInput();
        }
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be deleted.');
        }

        try {
            $purchaseOrder->delete();
            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase order deleted.');
        } catch (\Exception $e) {
            Log::error('PO deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete purchase order.');
        }
    }
}