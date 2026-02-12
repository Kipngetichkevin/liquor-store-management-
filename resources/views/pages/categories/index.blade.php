@extends('layouts.app')

@section('title', 'Categories - Liquor Management System')

@section('page-title', 'Categories')
@section('page-subtitle', 'Manage your product categories')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 sm:mb-0">Category List</h3>
        <a href="{{ route('categories.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center shadow">
            <i class="fas fa-plus mr-2"></i> Add Category
        </a>
    </div>
    <div class="overflow-x-auto">
        @if($categories->count() > 0)
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $category->name }}</td>
                        <td class="px-6 py-4">
                            @if($category->status == 'active')
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('categories.edit', $category) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Delete this category?')">
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
            <div class="mt-6">{{ $categories->links() }}</div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-tags text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No categories found</h4>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Create your first category.</p>
                <a href="{{ route('categories.create') }}" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow">
                    <i class="fas fa-plus mr-2"></i> Add Category
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
