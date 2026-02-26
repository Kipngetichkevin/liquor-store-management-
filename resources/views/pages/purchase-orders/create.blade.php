@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('page-title', 'Create Purchase Order')
@section('page-subtitle', 'Order stock from suppliers')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('purchase-orders.store') }}" method="POST" id="po-form">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- PO Number (readonly, auto-generated) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PO Number</label>
                    <input type="text" value="{{ $poNumber }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm bg-gray-100 dark:bg-gray-600" readonly>
                </div>

                <!-- Supplier -->
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier <span class="text-red-500">*</span></label>
                    <select name="supplier_id" id="supplier_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Order Date -->
                <div>
                    <label for="order_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order Date <span class="text-red-500">*</span></label>
                    <input type="date" name="order_date" id="order_date" value="{{ old('order_date', date('Y-m-d')) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('order_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expected Date -->
                <div>
                    <label for="expected_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Date</label>
                    <input type="date" name="expected_date" id="expected_date" value="{{ old('expected_date') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Notes (full width) -->
                <div class="md:col-span-3">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Order Items Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Order Items</h3>
                    <button type="button" id="add-item" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center shadow-sm text-sm">
                        <i class="fas fa-plus mr-2"></i> Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="items-table">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Product</th>
                                <th class="px-6 py-3">Quantity</th>
                                <th class="px-6 py-3">Unit Cost</th>
                                <th class="px-6 py-3">Total</th>
                                <th class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            <!-- Items will be added here dynamically -->
                        </tbody>
                        <tfoot>
                            <tr class="font-semibold text-gray-900 dark:text-white">
                                <td colspan="3" class="px-6 py-3 text-right">Subtotal:</td>
                                <td class="px-6 py-3" id="subtotal">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <p class="text-xs text-red-500 dark:text-red-400 mt-2" id="items-error" style="display: none;">Please add at least one item.</p>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('purchase-orders.index') }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-save mr-2"></i> Create Purchase Order
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const products = @json($products);
    let itemIndex = 0;

    function addItemRow(productId = '', quantity = 1, unitCost = '') {
        const tbody = document.getElementById('items-body');
        const row = document.createElement('tr');
        row.className = 'border-b dark:border-gray-700';
        row.dataset.index = itemIndex;

        let options = '<option value="">Select Product</option>';
        products.forEach(p => {
            const selected = p.id == productId ? 'selected' : '';
            options += `<option value="${p.id}" ${selected} data-price="${p.price}">${p.name} (KSh ${p.price})</option>`;
        });

        row.innerHTML = `
            <td class="px-6 py-3">
                <select name="items[${itemIndex}][product_id]" class="item-product w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    ${options}
                </select>
            </td>
            <td class="px-6 py-3">
                <input type="number" name="items[${itemIndex}][quantity]" value="${quantity}" min="1" class="item-quantity w-24 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </td>
            <td class="px-6 py-3">
                <input type="number" step="0.01" name="items[${itemIndex}][unit_cost]" value="${unitCost}" min="0" class="item-cost w-32 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </td>
            <td class="px-6 py-3">
                <span class="item-total">0.00</span>
            </td>
            <td class="px-6 py-3">
                <button type="button" class="remove-item text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);
        attachRowEvents(row);
        itemIndex++;
    }

    function attachRowEvents(row) {
        const productSelect = row.querySelector('.item-product');
        const quantityInput = row.querySelector('.item-quantity');
        const costInput = row.querySelector('.item-cost');
        const removeBtn = row.querySelector('.remove-item');

        function updateTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const cost = parseFloat(costInput.value) || 0;
            const total = quantity * cost;
            row.querySelector('.item-total').textContent = total.toFixed(2);
            updateOverallTotal();
        }

        productSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const price = selected.dataset.price;
            if (price && !costInput.value) {
                costInput.value = price;
            }
            updateTotal();
        });

        quantityInput.addEventListener('input', updateTotal);
        costInput.addEventListener('input', updateTotal);
        removeBtn.addEventListener('click', function() {
            row.remove();
            updateOverallTotal();
        });

        updateTotal();
    }

    function updateOverallTotal() {
        let subtotal = 0;
        document.querySelectorAll('.item-total').forEach(span => {
            subtotal += parseFloat(span.textContent) || 0;
        });
        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    }

    document.getElementById('add-item').addEventListener('click', () => addItemRow());

    document.getElementById('po-form').addEventListener('submit', function(e) {
        const items = document.querySelectorAll('#items-body tr');
        if (items.length === 0) {
            e.preventDefault();
            document.getElementById('items-error').style.display = 'block';
        }
    });

    // Load existing items if any (for edit mode)
    @if(isset($purchaseOrder) && $purchaseOrder->items->count() > 0)
        @foreach($purchaseOrder->items as $item)
            addItemRow({{ $item->product_id }}, {{ $item->quantity_ordered }}, {{ $item->unit_cost }});
        @endforeach
    @endif
</script>
@endpush
@endsection
