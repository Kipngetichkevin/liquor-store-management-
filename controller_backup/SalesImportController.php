<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use OpenSpout\Reader\Common\Creator\ReaderEntityFactory;

class SalesImportController extends Controller
{
    /**
     * Show the import form.
     */
    public function showForm()
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Import Sales Data', 'url' => route('sales.import.form')],
        ];

        return view('pages.sales.import', compact('breadcrumbs'));
    }

    /**
     * Upload multiple stock sheets (Excel files with daily tabs)
     */
    public function uploadStock(Request $request)
    {
        $request->validate([
            'stock_files' => 'required|array|min:1',
            'stock_files.*' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $allStockData = [];
            $filenames = [];

            foreach ($request->file('stock_files') as $file) {
                $filename = $file->getClientOriginalName();
                $filenames[] = $filename;
                
                // Extract month/year from filename
                $period = $this->extractPeriodFromFilename($filename);
                
                // Process each file
                $stockData = $this->processExcelFile($file, $period);
                $allStockData = array_merge($allStockData, $stockData);
            }

            session(['stock_data' => $allStockData]);
            session(['stock_uploaded' => true]);
            session(['stock_count' => count($request->file('stock_files'))]);
            session(['stock_filenames' => $filenames]);

            return redirect()->route('sales.import.form')
                ->with('success', count($request->file('stock_files')) . ' stock sheets uploaded successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error processing stock sheets: ' . $e->getMessage());
        }
    }

    /**
     * Upload multiple cost sheets (CSV files)
     */
    public function uploadCost(Request $request)
    {
        $request->validate([
            'cost_files' => 'required|array|min:1',
            'cost_files.*' => 'required|file|mimes:csv,txt'
        ]);

        try {
            $allCostData = [];
            $filenames = [];

            foreach ($request->file('cost_files') as $file) {
                $filename = $file->getClientOriginalName();
                $filenames[] = $filename;
                
                // Extract month/year from filename
                $period = $this->extractPeriodFromFilename($filename);
                
                // Process each file
                $costData = $this->processCostSheet($file, $period);
                $allCostData = array_merge($allCostData, $costData);
            }

            session(['cost_data' => $allCostData]);
            session(['cost_uploaded' => true]);
            session(['cost_count' => count($request->file('cost_files'))]);
            session(['cost_filenames' => $filenames]);

            return redirect()->route('sales.import.form')
                ->with('success', count($request->file('cost_files')) . ' cost sheets uploaded successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error processing cost sheets: ' . $e->getMessage());
        }
    }

    /**
     * Extract period (month/year) from filename
     */
    private function extractPeriodFromFilename($filename)
    {
        $filename = strtolower($filename);
        
        // Look for month names
        $months = [
            'january' => '01', 'february' => '02', 'march' => '03', 'april' => '04',
            'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08',
            'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12'
        ];

        foreach ($months as $monthName => $monthNum) {
            if (strpos($filename, $monthName) !== false) {
                // Look for year (e.g., 2026)
                preg_match('/20\d{2}/', $filename, $yearMatches);
                $year = $yearMatches[0] ?? date('Y');
                return "$year-$monthNum";
            }
        }

        // If no month found, use current
        return date('Y-m');
    }

    /**
     * Process the Excel file with multiple tabs (one per day)
     */
    private function processExcelFile($file, $period)
    {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($file->getRealPath());

        $allDailyData = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            $sheetName = $sheet->getName();
            $date = $this->parseSheetNameToDate($sheetName, $period);
            if (!$date) {
                continue;
            }

            $rowCount = 0;
            $headers = [];
            $sheetData = [];

            foreach ($sheet->getRowIterator() as $row) {
                $rowCount++;
                $cells = $row->getCells();
                $rowArray = [];

                foreach ($cells as $cell) {
                    $rowArray[] = $cell->getValue();
                }

                if ($rowCount === 1) {
                    $headers = array_map(function($header) {
                        return strtolower(trim(str_replace([' ', '_', '-'], '', $header)));
                    }, $rowArray);

                    $mapping = $this->detectSalesColumns($headers);

                    if (!$mapping['product'] || !$mapping['sell'] || !$mapping['price']) {
                        \Log::warning('Missing required columns in sheet: ' . $sheetName, [
                            'headers' => $headers,
                            'mapping' => $mapping
                        ]);
                        break;
                    }
                    continue;
                }

                // Skip empty rows
                if (empty(array_filter($rowArray))) {
                    continue;
                }

                $productName = trim($rowArray[$mapping['product']] ?? '');
                if (empty($productName)) {
                    continue;
                }

                $sheetData[] = [
                    'date' => $date,
                    'period' => $period,
                    'product_name' => $productName,
                    'opening' => isset($mapping['opening']) ? floatval($rowArray[$mapping['opening']] ?? 0) : 0,
                    'add' => isset($mapping['add']) ? floatval($rowArray[$mapping['add']] ?? 0) : 0,
                    'closing' => isset($mapping['closing']) ? floatval($rowArray[$mapping['closing']] ?? 0) : 0,
                    'sell' => floatval($rowArray[$mapping['sell']] ?? 0),
                    'price' => floatval(preg_replace('/[^0-9.-]/', '', $rowArray[$mapping['price']] ?? 0)),
                    'amount' => isset($mapping['amount']) ? floatval(preg_replace('/[^0-9.-]/', '', $rowArray[$mapping['amount']] ?? 0)) : 0,
                ];
            }

            if (!empty($sheetData)) {
                $allDailyData = array_merge($allDailyData, $sheetData);
            }
        }

        $reader->close();

        if (empty($allDailyData)) {
            throw new \Exception("No valid data found in file: {$file->getClientOriginalName()}");
        }

        return $allDailyData;
    }

    /**
     * Parse sheet name to date with period context
     */
    private function parseSheetNameToDate($sheetName, $period)
    {
        $sheetName = trim($sheetName);

        // Try to parse day from sheet name (if it's just a number 1-31)
        if (is_numeric($sheetName) && $sheetName >= 1 && $sheetName <= 31) {
            $day = str_pad($sheetName, 2, '0', STR_PAD_LEFT);
            return $period . '-' . $day;
        }

        // Try common date formats
        $formats = [
            'd M' => 'd M',
            'd M Y' => 'd M Y',
            'd-m' => 'd-m',
            'd/m' => 'd/m',
            'Y-m-d' => 'Y-m-d',
        ];

        foreach ($formats as $format => $pattern) {
            $dateTime = \DateTime::createFromFormat($format, $sheetName);
            if ($dateTime) {
                return $dateTime->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * Process cost sheet (CSV) with period
     */
    private function processCostSheet($file, $period)
    {
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        $headers = array_map(function($header) {
            return strtolower(trim(str_replace([' ', '_', '-'], '', $header)));
        }, $headers);

        $productCol = null;
        $priceCol = null;

        foreach ($headers as $index => $header) {
            if (strpos($header, 'items') !== false || strpos($header, 'product') !== false) {
                $productCol = $index;
            }
            if (strpos($header, 'buying') !== false || strpos($header, 'cost') !== false || strpos($header, 'price') !== false) {
                $priceCol = $index;
            }
        }

        if ($productCol === null || $priceCol === null) {
            fclose($handle);
            throw new \Exception("Cost sheet must have ITEMS and BUYING PRICE columns in file: {$file->getClientOriginalName()}");
        }

        $costData = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) continue;

            $productName = trim($row[$productCol] ?? '');
            if (empty($productName)) continue;

            $costData[] = [
                'period' => $period,
                'product_name' => $productName,
                'buying_price' => floatval(preg_replace('/[^0-9.-]/', '', $row[$priceCol] ?? 0)),
            ];
        }

        fclose($handle);
        return $costData;
    }

    /**
     * Detect columns in sales sheet - FLEXIBLE VERSION
     */
    private function detectSalesColumns($headers)
    {
        $mapping = [
            'product' => null,
            'opening' => null,
            'add' => null,
            'closing' => null,
            'sell' => null,
            'price' => null,
            'amount' => null,
        ];

        foreach ($headers as $index => $header) {
            $headerLower = strtolower($header);
            
            // Product name detection
            if (strpos($headerLower, 'items') !== false || 
                strpos($headerLower, 'product') !== false || 
                strpos($headerLower, 'name') !== false ||
                strpos($headerLower, 'description') !== false) {
                $mapping['product'] = $index;
            }
            
            // Opening stock detection
            if (strpos($headerLower, 'opening') !== false || 
                strpos($headerLower, 'start') !== false ||
                strpos($headerLower, 'begin') !== false) {
                $mapping['opening'] = $index;
            }
            
            // Additions detection
            if (strpos($headerLower, 'add') !== false || 
                strpos($headerLower, 'received') !== false ||
                strpos($headerLower, 'in') !== false) {
                $mapping['add'] = $index;
            }
            
            // Closing stock detection
            if (strpos($headerLower, 'closing') !== false || 
                strpos($headerLower, 'end') !== false ||
                strpos($headerLower, 'remaining') !== false) {
                $mapping['closing'] = $index;
            }
            
            // Quantity sold detection
            if (strpos($headerLower, 'sell') !== false || 
                strpos($headerLower, 'sold') !== false ||
                strpos($headerLower, 'qty') !== false ||
                strpos($headerLower, 'quantity') !== false) {
                $mapping['sell'] = $index;
            }
            
            // Price detection
            if (strpos($headerLower, 'price') !== false || 
                strpos($headerLower, 'rate') !== false ||
                strpos($headerLower, 'unit') !== false) {
                $mapping['price'] = $index;
            }
            
            // Amount detection
            if (strpos($headerLower, 'amount') !== false || 
                strpos($headerLower, 'total') !== false ||
                strpos($headerLower, 'value') !== false ||
                strpos($headerLower, 'khs') !== false) {
                $mapping['amount'] = $index;
            }
        }

        return $mapping;
    }

    /**
     * Analyze combined data with period filtering
     */
    public function analyze(Request $request)
    {
        $stockData = session('stock_data');
        $costData = session('cost_data');

        if (!$stockData || !$costData) {
            return redirect()->route('sales.import.form')
                ->with('error', 'Please upload both stock and cost sheets first.');
        }

        $period = $request->get('period', 'month');

        try {
            // Filter data based on period
            $filteredStock = $this->filterByPeriod($stockData, $period);
            $filteredCost = $this->filterByPeriod($costData, $period);

            // Analyze filtered data
            $results = $this->analyzeData($filteredStock, $filteredCost, $period);

            // Clear session data after analysis
            session()->forget(['stock_data', 'cost_data', 'stock_uploaded', 'cost_uploaded', 'stock_count', 'cost_count', 'stock_filenames', 'cost_filenames']);
            session()->flash('import_results', $results);

            return redirect()->route('sales.import.form')
                ->with('success', ucfirst($period) . 'ly report generated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error analyzing data: ' . $e->getMessage());
        }
    }

    /**
     * Filter data by period
     */
    private function filterByPeriod($data, $period)
    {
        if (empty($data)) return $data;

        $now = new \DateTime();
        
        switch ($period) {
            case 'month':
                $start = $now->modify('first day of this month')->format('Y-m');
                return array_filter($data, function($item) use ($start) {
                    $itemPeriod = $item['period'] ?? substr($item['date'] ?? '', 0, 7);
                    return strpos($itemPeriod, $start) === 0;
                });
                
            case 'quarter':
                $currentMonth = $now->format('n');
                $quarter = ceil($currentMonth / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $start = $now->format('Y') . '-' . str_pad($startMonth, 2, '0', STR_PAD_LEFT);
                $endMonth = $quarter * 3;
                $end = $now->format('Y') . '-' . str_pad($endMonth, 2, '0', STR_PAD_LEFT);
                
                return array_filter($data, function($item) use ($start, $end) {
                    $itemPeriod = $item['period'] ?? substr($item['date'] ?? '', 0, 7);
                    return $itemPeriod >= $start && $itemPeriod <= $end;
                });
                
            case 'year':
                $year = $now->format('Y');
                return array_filter($data, function($item) use ($year) {
                    $itemPeriod = $item['period'] ?? $item['date'] ?? '';
                    return strpos($itemPeriod, $year) === 0;
                });
                
            default:
                return $data;
        }
    }

    /**
     * Analyze combined data from both sheets
     */
    private function analyzeData($stockData, $costData, $period)
    {
        // Build cost lookup by period and product
        $costLookup = [];
        foreach ($costData as $cost) {
            $costLookup[$cost['period']][$cost['product_name']] = $cost['buying_price'];
        }

        $results = [
            'period' => $period,
            'total_days' => 0,
            'total_products' => 0,
            'total_revenue' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'total_tax' => 0,
            'monthly_summary' => [],
            'product_summary' => [],
            'daily_summary' => [],
        ];

        $byMonth = [];
        $byProduct = [];
        $uniqueDates = [];

        foreach ($stockData as $entry) {
            $date = $entry['date'];
            $month = substr($date, 0, 7);
            $product = $entry['product_name'];
            
            // Get buying price for this period
            $buyingPrice = 0;
            if (isset($costLookup[$entry['period']][$product])) {
                $buyingPrice = $costLookup[$entry['period']][$product];
            } elseif (!empty($costLookup)) {
                // Try first available period
                $firstPeriod = array_key_first($costLookup);
                if ($firstPeriod && isset($costLookup[$firstPeriod][$product])) {
                    $buyingPrice = $costLookup[$firstPeriod][$product];
                }
            }

            $revenue = $entry['sell'] * $entry['price'];
            $cost = $entry['sell'] * $buyingPrice;
            $profit = $revenue - $cost;
            $tax = $revenue - ($revenue / 1.16);

            $uniqueDates[$date] = true;

            // Monthly summary
            if (!isset($byMonth[$month])) {
                $byMonth[$month] = [
                    'month' => $month,
                    'display_month' => date('F Y', strtotime($month . '-01')),
                    'revenue' => 0,
                    'cost' => 0,
                    'profit' => 0,
                    'tax' => 0,
                    'transactions' => 0,
                    'days' => 0,
                ];
            }

            $byMonth[$month]['revenue'] += $revenue;
            $byMonth[$month]['cost'] += $cost;
            $byMonth[$month]['profit'] += $profit;
            $byMonth[$month]['tax'] += $tax;
            $byMonth[$month]['transactions'] += ($entry['sell'] > 0 ? 1 : 0);

            // Product summary
            if (!isset($byProduct[$product])) {
                $byProduct[$product] = [
                    'product' => $product,
                    'sell' => 0,
                    'revenue' => 0,
                    'cost' => 0,
                    'profit' => 0,
                    'tax' => 0,
                    'has_cost' => $buyingPrice > 0,
                ];
            }

            $byProduct[$product]['sell'] += $entry['sell'];
            $byProduct[$product]['revenue'] += $revenue;
            $byProduct[$product]['cost'] += $cost;
            $byProduct[$product]['profit'] += $profit;
            $byProduct[$product]['tax'] += $tax;

            // Daily summary (keep last 30 days for performance)
            if (count($results['daily_summary']) < 30) {
                $results['daily_summary'][] = [
                    'date' => $date,
                    'display_date' => date('d M Y', strtotime($date)),
                    'revenue' => $revenue,
                    'profit' => $profit,
                ];
            }

            // Totals
            $results['total_revenue'] += $revenue;
            $results['total_cost'] += $cost;
            $results['total_profit'] += $profit;
            $results['total_tax'] += $tax;
        }

        // Calculate days per month
        foreach ($byMonth as &$month) {
            $month['days'] = count(array_filter($uniqueDates, function($date) use ($month) {
                return strpos($date, $month['month']) === 0;
            }));
        }

        $results['total_days'] = count($uniqueDates);
        $results['total_products'] = count($byProduct);
        $results['monthly_summary'] = array_values($byMonth);
        $results['product_summary'] = array_values($byProduct);

        // Sort monthly by date
        usort($results['monthly_summary'], function($a, $b) {
            return strcmp($a['month'], $b['month']);
        });

        // Sort products by revenue
        usort($results['product_summary'], function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        // Sort daily by date (newest first)
        usort($results['daily_summary'], function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return $results;
    }
}