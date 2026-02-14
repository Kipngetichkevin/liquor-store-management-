@extends('layouts.app')

@section('title', 'Edit Supplier - Liquor Management System')

@section('page-title', 'Edit Supplier')
@section('page-subtitle', 'Update supplier information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supplier Code (readonly) -->
                <div class="col-span-2 md:col-span-1">
                    <label for="supplier_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier Code</label>
                    <input type="text" name="supplier_code" id="supplier_code" value="{{ old('supplier_code', $supplier->supplier_code) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm bg-gray-100 dark:bg-gray-600"
                           readonly>
                </div>

                <!-- Supplier Name -->
                <div class="col-span-2 md:col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Supplier Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $supplier->name) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required autofocus>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company Name -->
                <div class="col-span-2 md:col-span-1">
                    <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name</label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $supplier->company_name) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Email -->
                <div class="col-span-2 md:col-span-1">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $supplier->email) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="col-span-2 md:col-span-1">
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $supplier->phone) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Phone 2 -->
                <div class="col-span-2 md:col-span-1">
                    <label for="phone_2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alternate Phone</label>
                    <input type="text" name="phone_2" id="phone_2" value="{{ old('phone_2', $supplier->phone_2) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Contact Person -->
                <div class="col-span-2 md:col-span-1">
                    <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Person</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Website -->
                <div class="col-span-2 md:col-span-1">
                    <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Website</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $supplier->website) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="https://...">
                </div>

                <!-- Address -->
                <div class="col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                    <textarea name="address" id="address" rows="2" 
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address', $supplier->address) }}</textarea>
                </div>

                <!-- City -->
                <div class="col-span-2 md:col-span-1">
                    <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $supplier->city) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- State/Province -->
                <div class="col-span-2 md:col-span-1">
                    <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State/Province</label>
                    <input type="text" name="state" id="state" value="{{ old('state', $supplier->state) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Country -->
                <div class="col-span-2 md:col-span-1">
                    <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                    <input type="text" name="country" id="country" value="{{ old('country', $supplier->country) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Postal Code -->
                <div class="col-span-2 md:col-span-1">
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Postal Code</label>
                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $supplier->postal_code) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Tax Number -->
                <div class="col-span-2 md:col-span-1">
                    <label for="tax_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tax Number / VAT</label>
                    <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number', $supplier->tax_number) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Supplier Type -->
                <div class="col-span-2 md:col-span-1">
                    <label for="supplier_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier Type <span class="text-red-500">*</span></label>
                    <select name="supplier_type" id="supplier_type" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                        <option value="wholesale" {{ old('supplier_type', $supplier->supplier_type) == 'wholesale' ? 'selected' : '' }}>Wholesale</option>
                        <option value="retail" {{ old('supplier_type', $supplier->supplier_type) == 'retail' ? 'selected' : '' }}>Retail</option>
                        <option value="manufacturer" {{ old('supplier_type', $supplier->supplier_type) == 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                        <option value="distributor" {{ old('supplier_type', $supplier->supplier_type) == 'distributor' ? 'selected' : '' }}>Distributor</option>
                    </select>
                    @error('supplier_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Credit Limit -->
                <div class="col-span-2 md:col-span-1">
                    <label for="credit_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Credit Limit (KSh)</label>
                    <input type="number" step="0.01" min="0" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', $supplier->credit_limit) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Payment Terms (days) -->
                <div class="col-span-2 md:col-span-1">
                    <label for="payment_terms_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Terms (days)</label>
                    <input type="number" min="0" name="payment_terms_days" id="payment_terms_days" value="{{ old('payment_terms_days', $supplier->payment_terms_days) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Status -->
                <div class="col-span-2 md:col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" id="status" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="active" {{ old('status', $supplier->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $supplier->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Notes -->
                <div class="col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $supplier->notes) }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('suppliers.index') }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-save mr-2"></i> Update Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
