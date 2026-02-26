@extends('layouts.app')

@section('title', 'Point of Sale - Liquor Management System')

@section('page-title', 'Point of Sale')
@section('page-subtitle', 'Process customer sales')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: Product Browser -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="product-search" placeholder="Search products by name, SKU, or barcode..." 
                       class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                       autofocus>
            </div>

            <!-- Products Container -->
            <div id="products-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-96 overflow-y-auto p-2">
                @foreach($products as $product)
                    <div class="product-card border rounded-lg p-4 hover:shadow-md transition {{ $product->stock_quantity <= 0 ? 'opacity-50 bg-gray-100 dark:bg-gray-700' : '' }}"
                         data-id="{{ $product->id }}"
                         data-name="{{ strtolower($product->name) }}"
                         data-sku="{{ strtolower($product->sku ?? '') }}"
                         data-price="{{ $product->price }}"
                         data-stock="{{ $product->stock_quantity }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-800 dark:text-white">{{ $product->name }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->sku ?? 'No SKU' }}</p>
                            </div>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">KSh {{ number_format($product->price, 2) }}</span>
                        </div>
                        <div class="mt-2 flex justify-between items-center">
                            <span class="text-sm {{ $product->stock_quantity <= 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                Stock: {{ $product->stock_quantity }}
                            </span>
                            @if($product->stock_quantity > 0)
                                <button class="add-to-cart-btn bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded-lg transition"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}">
                                    <i class="fas fa-cart-plus mr-1"></i> Add
                                </button>
                            @else
                                <span class="text-red-600 dark:text-red-400 text-sm">Out of stock</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- No Results Message -->
            <div id="no-results" class="text-center py-12 hidden">
                <i class="fas fa-search text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400 text-lg">No products found matching your search</p>
                <button id="clear-search-btn" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    Show All Products
                </button>
            </div>
        </div>
    </div>

    <!-- Right: Cart -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 sticky top-24">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Current Sale</h3>

            <div id="cart-items" class="space-y-3 max-h-96 overflow-y-auto mb-4">
                @forelse($cart as $item)
                    <div class="cart-item flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded-lg" data-id="{{ $item['id'] }}">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800 dark:text-white">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">KSh {{ number_format($item['unit_price'], 2) }} each</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800 dark:text-white item-subtotal">KSh {{ number_format($item['subtotal'], 2) }}</p>
                            <div class="flex items-center justify-end space-x-2 mt-1">
                                <input type="number" 
                                       class="cart-quantity-input w-16 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="{{ $item['quantity'] }}"
                                       min="1"
                                       data-id="{{ $item['id'] }}"
                                       style="text-align: center;">
                                <button class="cart-remove text-gray-500 hover:text-red-600" title="Remove item" data-id="{{ $item['id'] }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Cart is empty</p>
                @endforelse
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal (excl. tax)</span>
                    <span id="cart-subtotal" class="font-semibold text-gray-800 dark:text-white">
                        KSh {{ number_format(array_sum(array_column($cart, 'subtotal')) / (1 + (config('pos.tax_rate', 16)/100)), 2) }}
                    </span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600 dark:text-gray-400">Tax ({{ config('pos.tax_rate', 16) }}%)</span>
                    <span id="cart-tax" class="font-semibold text-gray-800 dark:text-white">
                        KSh {{ number_format(array_sum(array_column($cart, 'subtotal')) - (array_sum(array_column($cart, 'subtotal')) / (1 + (config('pos.tax_rate', 16)/100))), 2) }}
                    </span>
                </div>
                <div class="flex justify-between items-center text-lg font-bold">
                    <span class="text-gray-800 dark:text-white">Total</span>
                    <span id="cart-total" class="text-blue-600 dark:text-blue-400">
                        KSh {{ number_format(array_sum(array_column($cart, 'subtotal')), 2) }}
                    </span>
                </div>
            </div>

            <form action="{{ route('pos.checkout') }}" method="POST" id="checkout-form">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount Paid</label>
                    <input type="number" step="0.01" min="0" name="amount_paid" id="amount_paid" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex space-x-2">
                    <button type="button" id="clear-cart-btn" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition shadow">
                        Clear
                    </button>
                    <button type="submit" id="checkout-btn" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition shadow">
                        Complete Sale
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quantity Modal -->
<div id="quantity-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 transition-all duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modal-content">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-cart text-3xl text-blue-600 dark:text-blue-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2" id="modal-product-name">Product Name</h3>
            <p class="text-gray-600 dark:text-gray-400" id="modal-product-price">KSh 0.00</p>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 text-center">Enter Quantity</label>
            <div class="flex items-center justify-center space-x-3">
                <button type="button" id="modal-qty-down" class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center text-2xl font-bold text-gray-700 dark:text-gray-300 transition">
                    <i class="fas fa-minus"></i>
                </button>
                <input type="number" id="modal-quantity" value="" placeholder="0" min="0" 
                       class="w-24 h-12 text-center text-xl font-bold border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:border-blue-500 focus:ring-blue-500"
                       style="text-align: center;">
                <button type="button" id="modal-qty-up" class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center text-2xl font-bold text-gray-700 dark:text-gray-300 transition">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-2" id="modal-stock-info">Available stock: <span id="modal-stock">0</span></p>
            <p class="text-xs text-red-500 dark:text-red-400 text-center mt-1" id="modal-error-message"></p>
        </div>

        <div class="flex justify-end space-x-3">
            <button type="button" id="modal-cancel" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                Cancel
            </button>
            <button type="button" id="modal-confirm" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow flex items-center">
                <i class="fas fa-check mr-2"></i> Add to Cart
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const taxRate = {{ config('pos.tax_rate', 16) }};

    // DOM elements
    const searchInput = document.getElementById('product-search');
    const productsContainer = document.getElementById('products-container');
    const noResults = document.getElementById('no-results');
    const clearSearchBtn = document.getElementById('clear-search-btn');
    const productCards = document.querySelectorAll('.product-card');

    // Simple live search – filters products as you type
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;
        
        productCards.forEach(card => {
            const name = card.dataset.name || '';
            const sku = card.dataset.sku || '';
            
            // Check if search term matches name or SKU
            if (name.includes(searchTerm) || sku.includes(searchTerm)) {
                card.style.display = ''; // show
                visibleCount++;
            } else {
                card.style.display = 'none'; // hide
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0 && searchTerm !== '') {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    });

    // Clear search
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        productCards.forEach(card => card.style.display = '');
        noResults.classList.add('hidden');
    });

    // Modal elements
    const modal = document.getElementById('quantity-modal');
    const modalContent = document.getElementById('modal-content');
    const modalProductName = document.getElementById('modal-product-name');
    const modalProductPrice = document.getElementById('modal-product-price');
    const modalStock = document.getElementById('modal-stock');
    const modalQuantity = document.getElementById('modal-quantity');
    const modalErrorMessage = document.getElementById('modal-error-message');
    const modalConfirm = document.getElementById('modal-confirm');
    const modalCancel = document.getElementById('modal-cancel');
    const modalQtyUp = document.getElementById('modal-qty-up');
    const modalQtyDown = document.getElementById('modal-qty-down');

    let currentProductId = null;
    let currentProductStock = 0;

    // Show modal
    function showModal(productId, productName, productPrice, productStock) {
        currentProductId = productId;
        currentProductStock = productStock;
        
        modalProductName.textContent = productName;
        modalProductPrice.textContent = 'KSh ' + parseFloat(productPrice).toFixed(2);
        modalStock.textContent = productStock;
        modalQuantity.value = '';
        modalErrorMessage.textContent = '';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        
        setTimeout(() => modalQuantity.focus(), 100);
    }

    // Hide modal
    function hideModal() {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // Attach add-to-cart buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            let productId = this.dataset.id;
            let productName = this.dataset.name;
            let productPrice = this.dataset.price;
            let productCard = this.closest('.product-card');
            let productStock = productCard ? productCard.dataset.stock : 0;
            
            showModal(productId, productName, productPrice, productStock);
        });
    });

    // Modal quantity controls
    modalQtyUp.addEventListener('click', function() {
        let currentVal = parseInt(modalQuantity.value);
        if (isNaN(currentVal) || currentVal === '') {
            modalQuantity.value = 1;
        } else {
            let newVal = currentVal + 1;
            if (newVal <= currentProductStock) {
                modalQuantity.value = newVal;
                modalErrorMessage.textContent = '';
            } else {
                modalErrorMessage.textContent = 'Maximum available stock is ' + currentProductStock;
            }
        }
    });

    modalQtyDown.addEventListener('click', function() {
        let currentVal = parseInt(modalQuantity.value);
        if (isNaN(currentVal) || currentVal === '') {
            modalQuantity.value = 1;
        } else if (currentVal > 1) {
            modalQuantity.value = currentVal - 1;
            modalErrorMessage.textContent = '';
        }
    });

    modalQuantity.addEventListener('input', function() {
        let val = this.value.trim();
        
        if (val === '') {
            modalErrorMessage.textContent = '';
            return;
        }
        
        let numVal = parseInt(val);
        
        if (isNaN(numVal)) {
            this.value = '';
            modalErrorMessage.textContent = '';
            return;
        }
        
        if (val.length > 1 && val.startsWith('0')) {
            this.value = numVal.toString();
        }
        
        if (numVal > currentProductStock) {
            modalErrorMessage.textContent = 'Warning: Exceeds available stock (' + currentProductStock + ')';
        } else if (numVal < 1) {
            modalErrorMessage.textContent = 'Quantity must be at least 1';
        } else {
            modalErrorMessage.textContent = '';
        }
    });

    modalConfirm.addEventListener('click', function() {
        let quantityInput = modalQuantity.value.trim();
        
        if (quantityInput === '') {
            alert('Please enter a quantity.');
            modalQuantity.focus();
            return;
        }
        
        let quantity = parseInt(quantityInput);
        
        if (isNaN(quantity)) {
            alert('Please enter a valid number.');
            modalQuantity.value = '';
            modalQuantity.focus();
            return;
        }
        
        if (quantity < 1) {
            alert('Quantity must be at least 1.');
            modalQuantity.value = '';
            modalQuantity.focus();
            return;
        }
        
        if (quantity > currentProductStock) {
            alert('Cannot add more than available stock (' + currentProductStock + ').');
            modalQuantity.focus();
            return;
        }
        
        fetch('{{ route("pos.cart.add") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: currentProductId, quantity: quantity })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                hideModal();
                window.location.reload();
            } else {
                alert(data.message || 'Failed to add item.');
            }
        })
        .catch(err => alert('Network error.'));
    });

    modalCancel.addEventListener('click', hideModal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });

    // Cart quantity handlers
    function attachQuantityInputHandlers() {
        document.querySelectorAll('.cart-quantity-input').forEach(input => {
            let timeout;
            
            input.removeEventListener('input', handleInput);
            input.removeEventListener('keypress', handleKeyPress);
            
            function handleInput(e) {
                clearTimeout(timeout);
                let productId = this.dataset.id;
                let newQuantity = parseInt(this.value);
                
                if (isNaN(newQuantity) || newQuantity < 1) {
                    this.value = 1;
                    newQuantity = 1;
                }
                
                timeout = setTimeout(() => {
                    updateCartQuantity(productId, newQuantity);
                }, 500);
            }
            
            function handleKeyPress(e) {
                if (e.key === 'Enter') {
                    clearTimeout(timeout);
                    let productId = this.dataset.id;
                    let newQuantity = parseInt(this.value);
                    
                    if (isNaN(newQuantity) || newQuantity < 1) {
                        this.value = 1;
                        newQuantity = 1;
                    }
                    
                    updateCartQuantity(productId, newQuantity);
                }
            }
            
            input.addEventListener('input', handleInput);
            input.addEventListener('keypress', handleKeyPress);
        });
    }

    document.querySelectorAll('.cart-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            removeFromCart(this.dataset.id);
        });
    });

    function updateCartQuantity(productId, newQuantity) {
        fetch('{{ route("pos.cart.update") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: newQuantity })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to update quantity.');
            }
        })
        .catch(err => alert('Network error.'));
    }

    function removeFromCart(productId) {
        fetch('{{ route("pos.cart.remove") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to remove item.');
            }
        })
        .catch(err => alert('Network error.'));
    }

    document.getElementById('clear-cart-btn').addEventListener('click', function() {
        if (confirm('Clear the entire cart?')) {
            fetch('{{ route("pos.cart.clear") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }
    });

    function updateTaxAndTotal() {
        let subtotalElement = document.getElementById('cart-subtotal');
        let taxElement = document.getElementById('cart-tax');
        let totalElement = document.getElementById('cart-total');
        
        if (subtotalElement && taxElement && totalElement) {
            let totalText = totalElement.innerText;
            let totalInclusive = parseFloat(totalText.replace(/[^0-9.]/g, '')) || 0;
            
            let subtotal = totalInclusive / (1 + (taxRate / 100));
            let tax = totalInclusive - subtotal;
            
            subtotalElement.innerText = 'KSh ' + subtotal.toFixed(2);
            taxElement.innerText = 'KSh ' + tax.toFixed(2);
        }
    }

    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        let amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
        let totalText = document.getElementById('cart-total').innerText;
        let total = parseFloat(totalText.replace(/[^0-9.]/g, ''));
        
        if (amountPaid < total) {
            e.preventDefault();
            alert('Amount paid is less than total (' + taxRate + '% tax included).');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        updateTaxAndTotal();
        attachQuantityInputHandlers();
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                hideModal();
            }
        });
    });
</script>
@endpush
@endsection