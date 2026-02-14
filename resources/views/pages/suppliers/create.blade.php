@extends('layouts.app')

@section('title', 'Add Supplier - Liquor Management System')

@section('page-title', 'Add Supplier')
@section('page-subtitle', 'Add a new supplier')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Supplier Code (auto-generated, read-only) -->
                <div>
                    <label for="supplier_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier Code</label>
                    <input type="text" name="supplier_code" id="supplier_code" value="{{ $supplierCode ?? 'SUP-'.date('ym').'-0001' }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm bg-gray-100 dark:bg-gray-600" readonly>
                </div>

                <!-- Supplier Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required autofocus>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Person -->
                <div>
                    <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Person</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                    <textarea name="address" id="address" rows="2" 
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
                </div>

                <!-- Tax Number -->
                <div>
                    <label for="tax_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tax Number / VAT</label>
                    <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Status (hidden, default active) -->
                <input type="hidden" name="status" value="active">

                <!-- is_active (hidden, default 1) -->
                <input type="hidden" name="is_active" value="1">

            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('suppliers.index') }}" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-save mr-2"></i> Save Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
