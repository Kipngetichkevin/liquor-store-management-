<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\RoleCheckTrait;

class ReportController extends Controller
{
    use RoleCheckTrait;

    public function products(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager']);
        if ($check !== true) return $check;

        $start = $request->get('start', now()->subMonth()->toDateString());
        $end   = $request->get('end', now()->toDateString());

        $products = Product::select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_quantity'),
                DB::raw('COALESCE(SUM(sale_items.subtotal), 0) as total_revenue'),
                DB::raw('COALESCE(AVG(sale_items.unit_price - products.cost_price), 0) as avg_profit')
            )
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->leftJoin('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->orWhereNull('sales.id')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_quantity')
            ->paginate(20);

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Product Sales Report', 'url' => route('reports.products')],
        ];

        return view('pages.reports.products', compact('products', 'start', 'end', 'breadcrumbs'));
    }

    public function profit(Request $request)
    {
        $check = $this->checkRole(['admin', 'manager']);
        if ($check !== true) return $check;

        $group = $request->get('group', 'day');
        $start = $request->get('start', now()->subMonth()->toDateString());
        $end   = $request->get('end', now()->toDateString());

        $query = Sale::whereBetween('created_at', [$start, $end])
                     ->where('status', 'completed')
                     ->select(
                         DB::raw($group === 'day' ? 'DATE(created_at) as period' :
                                ($group === 'week' ? 'YEARWEEK(created_at) as period' : 'DATE_FORMAT(created_at, "%Y-%m") as period')),
                         DB::raw('COUNT(*) as total_sales'),
                         DB::raw('SUM(total_amount) as revenue'),
                         DB::raw('SUM(tax_amount) as tax'),
                         DB::raw('SUM(total_amount - tax_amount) as profit_before_tax')
                     )
                     ->groupBy('period')
                     ->orderBy('period');

        $data = $query->get();

        $totals = [
            'sales'   => $data->sum('total_sales'),
            'revenue' => $data->sum('revenue'),
            'tax'     => $data->sum('tax'),
            'profit'  => $data->sum('profit_before_tax'),
        ];

        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Profit Report', 'url' => route('reports.profit')],
        ];

        return view('pages.reports.profit', compact('data', 'totals', 'group', 'start', 'end', 'breadcrumbs'));
    }
}