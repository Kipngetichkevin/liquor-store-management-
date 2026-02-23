@extends('layouts.app')

@section('title', 'Import Sales Data')

@section('page-title', 'Import Sales Data')
@section('page-subtitle', 'Upload stock sheets and supplier cost sheets')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Upload Forms -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Stock Sheets Upload (Multiple Files) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">📊 Upload Stock Sheets</h3>
            
            <form action="{{ route('sales.import.stock') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Excel Files (with daily tabs)
                    </label>
                    <input type="file" name="stock_files[]" accept=".xlsx,.xls" multiple required
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Select multiple Excel files (one per month). Files should be named like:
                    </p>
                    <p class="text-xs text-blue-500 mt-1">
                        📁 january_2026.xlsx, february_2026.xlsx, march_2026.xlsx
                    </p>
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-upload mr-2"></i> Upload Stock Files
                </button>
            </form>

            @if(session('stock_uploaded'))
                <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-lg">
                    ✅ {{ session('stock_count') }} stock files uploaded successfully!
                </div>
            @endif
        </div>

        <!-- Cost Sheets Upload (Multiple Files) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">💰 Upload Cost Sheets</h3>
            
            <form action="{{ route('sales.import.cost') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Supplier Cost Sheets (CSV)
                    </label>
                    <input type="file" name="cost_files[]" accept=".csv,.txt" multiple required
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Select multiple CSV files (one per month). Should contain: ITEMS, BUYING PRICE
                    </p>
                    <p class="text-xs text-green-500 mt-1">
                        Example: january_costs.csv, february_costs.csv
                    </p>
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-upload mr-2"></i> Upload Cost Files
                </button>
            </form>

            @if(session('cost_uploaded'))
                <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-lg">
                    ✅ {{ session('cost_count') }} cost files uploaded successfully!
                </div>
            @endif
        </div>

        <!-- Period Selection -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">📅 Analysis Period</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Range</label>
                <select id="period-select" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="month">Single Month</option>
                    <option value="quarter">Quarter (3 months)</option>
                    <option value="half">Half Year (6 months)</option>
                    <option value="year" selected>Full Year (12 months)</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            <div id="custom-range" class="hidden space-y-3 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From</label>
                    <input type="month" id="from-month" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To</label>
                    <input type="month" id="to-month" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div id="uploaded-files" class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">📂 Uploaded Files</p>
                @if(session('stock_filenames'))
                    <div class="mb-2">
                        <p class="text-xs text-blue-600 dark:text-blue-400">Stock Files:</p>
                        <ul class="text-xs text-gray-600 dark:text-gray-400 list-disc list-inside">
                            @foreach(session('stock_filenames') as $file)
                                <li>{{ $file }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(session('cost_filenames'))
                    <div>
                        <p class="text-xs text-green-600 dark:text-green-400">Cost Files:</p>
                        <ul class="text-xs text-gray-600 dark:text-gray-400 list-disc list-inside">
                            @foreach(session('cost_filenames') as $file)
                                <li>{{ $file }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(!session('stock_filenames') && !session('cost_filenames'))
                    <p class="text-xs text-gray-500 dark:text-gray-400">No files uploaded yet</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Analyze Button -->
    @if(session('stock_uploaded') && session('cost_uploaded'))
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">🎯 Ready to Analyze!</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    {{ session('stock_count') }} stock files and {{ session('cost_count') }} cost files uploaded.
                    Click below to generate your report.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('sales.import.analyze') }}?period=month" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                        <i class="fas fa-calendar-day mr-2"></i> This Month
                    </a>
                    <a href="{{ route('sales.import.analyze') }}?period=quarter" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow">
                        <i class="fas fa-calendar-alt mr-2"></i> Quarter
                    </a>
                    <a href="{{ route('sales.import.analyze') }}?period=year" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition shadow">
                        <i class="fas fa-calendar mr-2"></i> Full Year
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Instructions -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">📋 How It Works</h4>
        <ul class="text-xs text-blue-600 dark:text-blue-400 list-disc list-inside space-y-1">
            <li>Step 1: Upload multiple Stock Excel files (one per month) – all with daily tabs</li>
            <li>Step 2: Upload multiple Cost CSV files (one per month) – with supplier prices</li>
            <li>Step 3: Select analysis period and click generate</li>
            <li>The system will match files by month name and calculate profits</li>
            <li>Get year-to-date reports, quarterly trends, and monthly comparisons</li>
        </ul>
    </div>

    <!-- Analysis Results -->
    @if(session('import_results'))
        @include('pages.sales.import-results')
    @endif
</div>

@push('scripts')
<script>
    // Show/hide custom range based on selection
    document.getElementById('period-select')?.addEventListener('change', function() {
        const customRange = document.getElementById('custom-range');
        if (this.value === 'custom') {
            customRange.classList.remove('hidden');
        } else {
            customRange.classList.add('hidden');
        }
    });

    // Set default month inputs to current
    const today = new Date();
    const currentMonth = today.toISOString().slice(0, 7);
    const fromMonth = document.getElementById('from-month');
    const toMonth = document.getElementById('to-month');
    if (fromMonth) fromMonth.value = currentMonth;
    if (toMonth) toMonth.value = currentMonth;
</script>
@endpush
@endsection