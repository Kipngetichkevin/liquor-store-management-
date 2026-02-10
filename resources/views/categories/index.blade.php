<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Categories</h1>
            <a href="{{ route('categories.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Category</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $category->name }}</td>
                        <td class="px-6 py-4">
                            @if($category->is_active)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Active</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <!-- View Button -->
                            <a href="{{ route('categories.show', $category->id) }}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                            
                            <!-- Edit Button -->
                            <a href="{{ route('categories.edit', $category->id) }}" class="text-green-600 hover:text-green-800 mr-3">Edit</a>
                            
                            <!-- Delete Button -->
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800"
                                        onclick="return confirm('Delete this category?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center">No categories found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>