<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    /**
     * Display a listing of sales with filters.
     */
    public function index(Request $request)
    {
        $query = Sale::with('user', 'items.product');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by invoice number
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate totals for filtered results
        $totalSales = $query->count();
        $totalRevenue = $query->sum('total_amount');
        $totalTax = $query->sum('tax_amount');

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Sales Reports', 'url' => route('sales.index')],
        ];

        return view('pages.sales.index', compact(
            'sales',
            'totalSales',
            'totalRevenue',
            'totalTax',
            'breadcrumbs'
        ));
    }

    /**
     * Display the specified sale.
     */
    public function show(Sale $sale)
    {
        $sale->load('items.product', 'user');

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Sales Reports', 'url' => route('sales.index')],
            ['title' => 'Sale ' . $sale->invoice_number, 'url' => route('sales.show', $sale)],
        ];

        return view('pages.sales.show', compact('sale', 'breadcrumbs'));
    }

    /**
     * Daily sales report.
     */
    public function daily(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $sales = Sale::with('user')
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total_amount'),
            'total_tax' => $sales->sum('tax_amount'),
            'cash' => $sales->where('payment_method', 'cash')->sum('total_amount'),
            'card' => $sales->where('payment_method', 'card')->sum('total_amount'),
            'mobile_money' => $sales->where('payment_method', 'mobile_money')->sum('total_amount'),
            'credit' => $sales->where('payment_method', 'credit')->sum('total_amount'),
        ];

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Sales Reports', 'url' => route('sales.index')],
            ['title' => 'Daily Report', 'url' => route('sales.daily')],
        ];

        return view('pages.sales.daily', compact('sales', 'summary', 'date', 'breadcrumbs'));
    }

    /**
     * Monthly sales report.
     */
    public function monthly(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $sales = Sale::with('user')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->get();

        // Daily breakdown for the month
        $dailyBreakdown = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $summary = [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total_amount'),
            'total_tax' => $sales->sum('tax_amount'),
            'average_per_sale' => $sales->count() > 0 ? $sales->sum('total_amount') / $sales->count() : 0,
        ];

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Sales Reports', 'url' => route('sales.index')],
            ['title' => 'Monthly Report', 'url' => route('sales.monthly')],
        ];

        return view('pages.sales.monthly', compact('sales', 'summary', 'dailyBreakdown', 'month', 'year', 'breadcrumbs'));
    }

    /**
     * Void a sale (admin only).
     */
    public function void(Sale $sale)
    {
        if ($sale->status === 'cancelled') {
            return back()->with('error', 'Sale is already cancelled.');
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->updateStock(
                        $item->quantity,
                        'return',
                        $sale->invoice_number,
                        'Sale voided'
                    );
                }
            }

            $sale->update(['status' => 'cancelled']);

            DB::commit();

            return back()->with('success', 'Sale voided successfully. Stock restored.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Void sale failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to void sale.');
        }
    }

    /**
     * Print receipt.
     */
    public function print(Sale $sale)
    {
        $sale->load('items.product', 'user');
        return view('pages.sales.print', compact('sale'));
    }
}