<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use App\Traits\RoleCheckTrait;

class SaleController extends Controller
{
    use RoleCheckTrait;

    /**
     * Display a listing of sales with filters.
     */
    public function index(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager']);
        if ($check !== true) return $check;

        $query = Sale::with('user', 'items.product');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(15);

        $totalSales   = $query->count();
        $totalRevenue = $query->sum('total_amount');
        $totalTax     = $query->sum('tax_amount');

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
        $check = $this->checkRole(['admin', 'manager']);
        if ($check !== true) return $check;

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
        $check = $this->checkRole(['admin', 'manager']);
        if ($check !== true) return $check;

        $date = $request->get('date', date('Y-m-d'));

        $sales = Sale::with('user')
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_sales'   => $sales->count(),
            'total_revenue' => $sales->sum('total_amount'),
            'total_tax'     => $sales->sum('tax_amount'),
            'cash'          => $sales->where('payment_method', 'cash')->sum('total_amount'),
            'card'          => $sales->where('payment_method', 'card')->sum('total_amount'),
            'mobile_money'  => $sales->where('payment_method', 'mobile_money')->sum('total_amount'),
            'credit'        => $sales->where('payment_method', 'credit')->sum('total_amount'),
        ];

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Sales Reports', 'url' => route('sales.index')],
            ['title' => 'Daily Report', 'url' => route('sales.daily')],
        ];

        return view('pages.sales.daily', compact('sales', 'summary', 'date', 'breadcrumbs'));
    }

    /**
     * Weekly sales report with detailed breakdown.
     */
    public function weekly(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager']);
        if ($check !== true) return $check;

        $startDate = $request->get('start_date', date('Y-m-d', strtotime('monday this week')));
        $endDate   = $request->get('end_date', date('Y-m-d', strtotime('sunday this week')));

        $items = SaleItem::with(['sale', 'product'])
            ->whereHas('sale', function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate)
                      ->where('status', 'completed');
            })
            ->orderBy('sale_id')
            ->get();

        $dailySummary = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('SUM(tax_amount) as tax'),
                DB::raw('SUM(total_amount - tax_amount) as profit_before_tax')
            )
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = $items->sum(fn($item) => $item->subtotal);
        $totalTax     = $items->sum(function ($item) {
            $saleTotal = $item->sale->total_amount;
            $saleTax   = $item->sale->tax_amount;
            return $saleTotal > 0 ? ($item->subtotal / $saleTotal) * $saleTax : 0;
        });
        $totalProfit  = $items->sum(function ($item) {
            $sellingPrice = $item->unit_price;
            $costPrice    = $item->product->cost_price ?? 0;
            return ($sellingPrice - $costPrice) * $item->quantity;
        });

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Sales Reports', 'url' => route('sales.index')],
            ['title' => 'Weekly Report', 'url' => route('sales.weekly')],
        ];

        return view('pages.sales.weekly', compact(
            'items',
            'dailySummary',
            'totalRevenue',
            'totalTax',
            'totalProfit',
            'startDate',
            'endDate',
            'breadcrumbs'
        ));
    }

    /**
     * Export weekly report as CSV.
     */
    public function exportWeeklyCsv(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager']);
        if ($check !== true) return $check;

        $startDate = $request->get('start_date', date('Y-m-d', strtotime('monday this week')));
        $endDate   = $request->get('end_date', date('Y-m-d', strtotime('sunday this week')));

        $items = SaleItem::with(['sale', 'product'])
            ->whereHas('sale', function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate)
                      ->where('status', 'completed');
            })
            ->orderBy('sale_id')
            ->get();

        $headers = [
            'Date', 'Invoice #', 'Product', 'Quantity',
            'Selling Price', 'Cost Price', 'Profit per Unit',
            'Total Profit', 'Tax', 'Total (Profit + Tax)', 'Payment Method'
        ];

        $data = [];
        foreach ($items as $item) {
            $sellingPrice = $item->unit_price;
            $costPrice    = $item->product->cost_price ?? 0;
            $profitPerUnit = $sellingPrice - $costPrice;
            $totalProfit   = $profitPerUnit * $item->quantity;

            $saleTotal = $item->sale->total_amount;
            $saleTax   = $item->sale->tax_amount;
            $itemTax   = $saleTotal > 0 ? ($item->subtotal / $saleTotal) * $saleTax : 0;

            $data[] = [
                date('Y-m-d', strtotime($item->sale->created_at)),
                $item->sale->invoice_number,
                $item->product->name ?? 'Unknown',
                $item->quantity,
                number_format($sellingPrice, 2),
                number_format($costPrice, 2),
                number_format($profitPerUnit, 2),
                number_format($totalProfit, 2),
                number_format($itemTax, 2),
                number_format($totalProfit + $itemTax, 2),
                ucfirst($item->sale->payment_method),
            ];
        }

        $totalProfit = $items->sum(fn($item) => ($item->unit_price - ($item->product->cost_price ?? 0)) * $item->quantity);
        $totalTax    = $items->sum(function ($item) {
            $saleTotal = $item->sale->total_amount;
            $saleTax   = $item->sale->tax_amount;
            return $saleTotal > 0 ? ($item->subtotal / $saleTotal) * $saleTax : 0;
        });

        $data[] = ['', '', '', '', '', '', 'TOTAL:',
                   number_format($totalProfit, 2),
                   number_format($totalTax, 2),
                   number_format($totalProfit + $totalTax, 2), ''];

        $filename = "weekly_sales_" . date('Ymd', strtotime($startDate)) . "_to_" . date('Ymd', strtotime($endDate)) . ".csv";
        $handle = fopen('php://temp', 'w');
        fputs($handle, "\xEF\xBB\xBF"); // BOM for Excel
        fputcsv($handle, $headers);
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        // Log activity (optional)
        auth()->user()->logActivity('export', 'sales', "Exported weekly sales report {$startDate} to {$endDate}");

        return Response::make($content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Monthly sales report with daily profit breakdown.
     */
    public function monthly(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager']);
        if ($check !== true) return $check;

        $month = (int) $request->get('month', date('n'));
        $year  = (int) $request->get('year', date('Y'));

        $sales = Sale::with('user', 'items.product')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $dailyBreakdown = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('SUM(tax_amount) as tax'),
                DB::raw('SUM(total_amount - tax_amount) as profit_before_tax')
            )
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = $sales->sum('total_amount');
        $totalTax     = $sales->sum('tax_amount');
        $totalProfit  = $dailyBreakdown->sum('profit_before_tax');

        $summary = [
            'total_sales'      => $sales->count(),
            'total_revenue'    => $totalRevenue,
            'total_tax'        => $totalTax,
            'total_profit'     => $totalProfit,
            'average_per_sale' => $sales->count() > 0 ? $totalRevenue / $sales->count() : 0,
        ];

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Sales Reports', 'url' => route('sales.index')],
            ['title' => 'Monthly Report', 'url' => route('sales.monthly')],
        ];

        return view('pages.sales.monthly', compact(
            'sales',
            'summary',
            'dailyBreakdown',
            'month',
            'year',
            'breadcrumbs'
        ));
    }

    /**
     * Export monthly report as CSV.
     */
    public function exportMonthlyCsv(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager']);
        if ($check !== true) return $check;

        $month = (int) $request->get('month', date('n'));
        $year  = (int) $request->get('year', date('Y'));

        $dailyBreakdown = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('SUM(tax_amount) as tax'),
                DB::raw('SUM(total_amount - tax_amount) as profit_before_tax')
            )
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $cumulativeProfit = 0;
        $data = [];
        foreach ($dailyBreakdown as $day) {
            $cumulativeProfit += $day->profit_before_tax;
            $data[] = [
                date('Y-m-d', strtotime($day->date)),
                date('l', strtotime($day->date)),
                $day->transactions,
                number_format($day->revenue, 2),
                number_format($day->tax, 2),
                number_format($day->profit_before_tax, 2),
                number_format($cumulativeProfit, 2),
            ];
        }

        $totalRevenue = $dailyBreakdown->sum('revenue');
        $totalTax     = $dailyBreakdown->sum('tax');
        $totalProfit  = $dailyBreakdown->sum('profit_before_tax');

        $headers = [
            'Date', 'Day', 'Transactions', 'Revenue (KSh)',
            'Tax (KSh)', 'Profit (KSh)', 'Cumulative Profit (KSh)'
        ];

        $filename = "monthly_sales_" . date('F_Y', mktime(0, 0, 0, $month, 1, $year)) . ".csv";
        $handle = fopen('php://temp', 'w');
        fputs($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ["Monthly Sales Report - " . date('F Y', mktime(0, 0, 0, $month, 1, $year))]);
        fputcsv($handle, []);
        fputcsv($handle, $headers);
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fputcsv($handle, []);
        fputcsv($handle, ['SUMMARY']);
        fputcsv($handle, ['Total Revenue:', 'KSh ' . number_format($totalRevenue, 2)]);
        fputcsv($handle, ['Total Tax:', 'KSh ' . number_format($totalTax, 2)]);
        fputcsv($handle, ['Total Profit:', 'KSh ' . number_format($totalProfit, 2)]);

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        // Log activity
        auth()->user()->logActivity('export', 'sales', "Exported monthly sales report for " . date('F Y', mktime(0, 0, 0, $month, 1, $year)));

        return Response::make($content, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Void a sale (admin only).
     */
    public function void(Sale $sale)
    {
        $check = $this->checkRole(['admin']);
        if ($check !== true) return $check;

        if ($sale->status === 'cancelled') {
            return back()->with('error', 'Sale is already cancelled.');
        }

        DB::beginTransaction();
        try {
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

            // Log activity
            auth()->user()->logActivity('void', 'sales', "Voided sale #{$sale->invoice_number}");

            return back()->with('success', 'Sale voided successfully. Stock restored.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Void sale failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to void sale.');
        }
    }

    /**
     * Print receipt.
     */
    public function print(Sale $sale)
    {
        $check = $this->checkRole(['admin', 'manager']);
        if ($check !== true) return $check;

        $sale->load('items.product', 'user');
        return view('pages.sales.print', compact('sale'));
    }
}