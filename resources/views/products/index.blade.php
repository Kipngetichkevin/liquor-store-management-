@extends('layouts.app')

@section('title', 'Products - Liquor Management System')

@section('page-title', 'Products')
@section('page-subtitle', 'Manage your liquor inventory')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Sidebar Filters -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Filters</h3>
            
            <!-- Search with Suggestions -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Products</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="instant-search" value="{{ request('search') }}" 
                           class="w-full pl-10 pr-10 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                           placeholder="Type to search..." 
                           autocomplete="off">
                    <div id="search-loading" class="absolute right-3 top-2 hidden">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                    </div>
                </div>
                
                <!-- Suggestions Dropdown -->
                <div id="suggestions-container" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-xl hidden" style="width: calc(100% - 2rem);">
                    <div id="suggestions-list" class="max-h-60 overflow-y-auto py-2">
                        <!-- Suggestions will appear here -->
                    </div>
                </div>
                
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Click on suggestions to filter instantly</p>
            </div>

            <!-- Category Filter -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <select name="category" id="category-filter" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" id="status-filter" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Stock Filter -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock Level</label>
                <select name="stock" id="stock-filter" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>

            <!-- Sort -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort By</label>
                <select name="sort" id="sort-filter" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Price</option>
                    <option value="stock_quantity" {{ request('sort') == 'stock_quantity' ? 'selected' : '' }}>Stock</option>
                </select>
                
                <select name="direction" id="direction-filter" class="w-full mt-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="desc" {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="button" id="apply-filters" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition shadow">
                    Apply Filters
                </button>
                <a href="{{ route('products.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg text-center transition shadow">
                    Reset
                </a>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="lg:col-span-3">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Products</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalProducts }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <i class="fas fa-wine-bottle text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Low Stock</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $lowStockCount }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Out of Stock</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $outOfStockCount }}</p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <i class="fas fa-times-circle text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header with Add Button -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 sm:mb-0">Product List</h3>
                <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center shadow">
                    <i class="fas fa-plus mr-2"></i> Add Product
                </a>
            </div>

            <!-- Active Search Tags -->
            <div id="active-search-tags" class="flex flex-wrap gap-2 mb-4 hidden">
                <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">Active filters:</span>
            </div>

            <!-- Products Table -->
            <div class="overflow-x-auto">
                <div id="no-results" class="hidden text-center py-12">
                    <i class="fas fa-search text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">No products found matching your search</p>
                    <button id="clear-all-search" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                        Clear All Filters
                    </button>
                </div>

                <div id="products-container">
                    @if($products->count() > 0)
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="products-table">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Image</th>
                                    <th scope="col" class="px-6 py-3">Name/SKU</th>
                                    <th scope="col" class="px-6 py-3">Category</th>
                                    <th scope="col" class="px-6 py-3">Price</th>
                                    <th scope="col" class="px-6 py-3">Stock</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="products-body">
                                @foreach($products as $product)
                                    <tr class="product-row bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ strtolower($product->name) }}"
                                        data-sku="{{ strtolower($product->sku ?? '') }}"
                                        data-brand="{{ strtolower($product->brand ?? '') }}"
                                        data-category="{{ strtolower($product->category->name ?? '') }}"
                                        data-price="{{ $product->price }}"
                                        data-stock="{{ $product->stock_quantity }}"
                                        data-status="{{ $product->status }}">
                                        <td class="px-6 py-4">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-lg shadow-sm">
                                            @else
                                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center shadow-sm">
                                                    <i class="fas fa-wine-bottle text-gray-400 dark:text-gray-500"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            <div class="product-name">{{ $product->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 product-sku">{{ $product->sku ?? 'No SKU' }}</div>
                                        </td>
                                        <td class="px-6 py-4 product-category">{{ $product->category->name ?? 'Uncategorized' }}</td>
                                        <td class="px-6 py-4 product-price">
                                            <div class="font-medium">{{ number_format($product->price, 2) }}</div>
                                            @if($product->cost_price)
                                                <div class="text-xs text-gray-500">Cost: {{ number_format($product->cost_price, 2) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 product-stock">
                                            @if($product->stock_quantity <= 0)
                                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full dark:bg-red-900 dark:text-red-300">Out</span>
                                            @elseif($product->stock_quantity <= $product->min_stock_level)
                                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full dark:bg-yellow-900 dark:text-yellow-300">{{ $product->stock_quantity }}</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">{{ $product->stock_quantity }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 product-status">
                                            @if($product->status == 'active')
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('products.edit', $product) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Are you sure you want to delete this product?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-6" id="pagination-container">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="mb-4">
                                <i class="fas fa-wine-bottle text-6xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No products found</h4>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by adding your first product.</p>
                            <a href="{{ route('products.create') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow">
                                <i class="fas fa-plus mr-2"></i> Add Product
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Search elements
    const searchInput = document.getElementById('instant-search');
    const searchLoading = document.getElementById('search-loading');
    const suggestionsContainer = document.getElementById('suggestions-container');
    const suggestionsList = document.getElementById('suggestions-list');
    const activeSearchTags = document.getElementById('active-search-tags');
    const clearAllBtn = document.getElementById('clear-all-search');
    
    // Product rows
    const productRows = Array.from(document.querySelectorAll('.product-row'));
    const noResults = document.getElementById('no-results');
    const productsContainer = document.getElementById('products-container');
    const productsBody = document.getElementById('products-body');
    const paginationContainer = document.getElementById('pagination-container');
    
    // Filter buttons
    const categoryFilter = document.getElementById('category-filter');
    const statusFilter = document.getElementById('status-filter');
    const stockFilter = document.getElementById('stock-filter');
    const sortFilter = document.getElementById('sort-filter');
    const directionFilter = document.getElementById('direction-filter');
    const applyFiltersBtn = document.getElementById('apply-filters');

    // Store original products HTML
    const originalProductsHTML = productsBody ? productsBody.innerHTML : '';
    const originalPaginationHTML = paginationContainer ? paginationContainer.innerHTML : '';

    // Current active filters
    let activeFilters = {
        search: searchInput.value.toLowerCase().trim()
    };

    // Update active filters display
    function updateActiveFilters() {
        activeSearchTags.innerHTML = '<span class="text-sm text-gray-600 dark:text-gray-400 mr-2">Active filters:</span>';
        let hasFilters = false;
        
        if (activeFilters.search) {
            hasFilters = true;
            activeSearchTags.innerHTML += `
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                    Search: "${activeFilters.search}"
                    <button onclick="clearSearchFilter()" class="ml-2 hover:text-blue-600 dark:hover:text-blue-400">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            `;
        }
        
        if (categoryFilter.value) {
            hasFilters = true;
            activeSearchTags.innerHTML += `
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                    Category: ${categoryFilter.options[categoryFilter.selectedIndex].text}
                </span>
            `;
        }
        
        if (statusFilter.value) {
            hasFilters = true;
            activeSearchTags.innerHTML += `
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                    Status: ${statusFilter.value}
                </span>
            `;
        }
        
        if (stockFilter.value) {
            hasFilters = true;
            activeSearchTags.innerHTML += `
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                    Stock: ${stockFilter.value}
                </span>
            `;
        }
        
        if (hasFilters) {
            activeSearchTags.classList.remove('hidden');
        } else {
            activeSearchTags.classList.add('hidden');
        }
    }

    // Clear search filter
    window.clearSearchFilter = function() {
        searchInput.value = '';
        activeFilters.search = '';
        filterProducts();
        updateActiveFilters();
        hideSuggestions();
    };

    // Clear all filters
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            window.location.href = '{{ route("products.index") }}';
        });
    }

    // Generate suggestions based on current input
    function generateSuggestions() {
        const input = searchInput.value.toLowerCase().trim();
        if (input.length < 1) {
            hideSuggestions();
            return;
        }
        
        const suggestions = new Set();
        
        productRows.forEach(row => {
            const name = row.dataset.name || '';
            const sku = row.dataset.sku || '';
            const brand = row.dataset.brand || '';
            const category = row.dataset.category || '';
            
            // Check for matches in different fields
            if (name.includes(input)) {
                suggestions.add(name);
            }
            if (sku.includes(input)) {
                suggestions.add(sku);
            }
            if (brand.includes(input)) {
                suggestions.add(brand);
            }
            if (category.includes(input)) {
                suggestions.add(category);
            }
        });
        
        displaySuggestions(Array.from(suggestions).slice(0, 8));
    }

    // Display suggestions dropdown
    function displaySuggestions(suggestions) {
        if (suggestions.length === 0) {
            hideSuggestions();
            return;
        }
        
        let html = '';
        suggestions.forEach(suggestion => {
            html += `
                <div class="suggestion-item px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition"
                     onclick="selectSuggestion('${suggestion.replace(/'/g, "\\'")}')">
                    <div class="flex items-center">
                        <i class="fas fa-search text-gray-400 mr-3 text-sm"></i>
                        <span class="text-gray-700 dark:text-gray-300">${suggestion}</span>
                    </div>
                </div>
            `;
        });
        
        suggestionsList.innerHTML = html;
        suggestionsContainer.classList.remove('hidden');
    }

    // Hide suggestions
    function hideSuggestions() {
        suggestionsContainer.classList.add('hidden');
    }

    // Select a suggestion
    window.selectSuggestion = function(suggestion) {
        searchInput.value = suggestion;
        activeFilters.search = suggestion.toLowerCase();
        filterProducts();
        updateActiveFilters();
        hideSuggestions();
    };

    // Filter products based on search
    function filterProducts() {
        const searchTerm = activeFilters.search;
        let visibleCount = 0;
        
        productRows.forEach(row => {
            const name = row.dataset.name || '';
            const sku = row.dataset.sku || '';
            const brand = row.dataset.brand || '';
            const category = row.dataset.category || '';
            
            // Check if any field matches the search term
            if (searchTerm === '' || 
                name.includes(searchTerm) || 
                sku.includes(searchTerm) || 
                brand.includes(searchTerm) || 
                category.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0 && searchTerm !== '' && productRows.length > 0) {
            noResults.classList.remove('hidden');
            if (productsBody) productsBody.style.display = 'none';
            if (paginationContainer) paginationContainer.style.display = 'none';
        } else {
            noResults.classList.add('hidden');
            if (productsBody) productsBody.style.display = '';
            if (paginationContainer) paginationContainer.style.display = '';
        }
        
        searchLoading.classList.add('hidden');
    }

    // Search input handler
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        const value = this.value;
        searchLoading.classList.remove('hidden');
        clearTimeout(searchTimeout);
        
        // Show suggestions as user types
        searchTimeout = setTimeout(() => {
            generateSuggestions();
            searchLoading.classList.add('hidden');
        }, 200);
    });

    // Handle keypress for enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            activeFilters.search = this.value.toLowerCase().trim();
            filterProducts();
            updateActiveFilters();
            hideSuggestions();
        }
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            hideSuggestions();
        }
    });

    // Apply filters button
    applyFiltersBtn.addEventListener('click', function() {
        const params = new URLSearchParams();
        
        if (searchInput.value) params.append('search', searchInput.value);
        if (categoryFilter.value) params.append('category', categoryFilter.value);
        if (statusFilter.value) params.append('status', statusFilter.value);
        if (stockFilter.value) params.append('stock', stockFilter.value);
        if (sortFilter.value) params.append('sort', sortFilter.value);
        if (directionFilter.value) params.append('direction', directionFilter.value);
        
        window.location.href = '{{ route("products.index") }}?' + params.toString();
    });

    // Initialize
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) {
        searchInput.value = urlParams.get('search');
        activeFilters.search = urlParams.get('search').toLowerCase();
        filterProducts();
        updateActiveFilters();
    }
</script>
@endpush
@endsection