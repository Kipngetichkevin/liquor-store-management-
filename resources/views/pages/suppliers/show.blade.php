@extends('layouts.app')

@section('title', $supplier->name . ' - Supplier Details')

@section('page-title', $supplier->name)
@section('page-subtitle', 'Supplier details and information')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Supplier Details Card -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">{{ $supplier->name }}</h2>
                    @if($supplier->contact_person)
                        <p class="text-gray-600 dark:text-gray-400">Contact: {{ $supplier->contact_person }}</p>
                    @endif
                </div>
                <div>
                    {!! $supplier->status_badge !!}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Supplier Code</p>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $supplier->supplier_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $supplier->email ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $supplier->phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tax Number</p>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $supplier->tax_number ?? '—' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $supplier->address ?? '—' }}</p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('suppliers.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                <a href="{{ route('suppliers.edit', $supplier) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition shadow">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Delete this supplier?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
