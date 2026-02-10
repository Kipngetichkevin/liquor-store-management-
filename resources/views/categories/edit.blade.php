<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category - Liquor Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Category</h1>
            <div class="flex space-x-2">
                <a href="{{ route('categories.show', $category->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    View
                </a>
                <a href="{{ route('categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Back to Categories
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Category Name *</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $category->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., Whiskey, Vodka, Wine"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="slug" class="block text-gray-700 font-medium mb-2">Slug</label>
                    <input type="text" 
                           id="slug" 
                           value="{{ $category->slug }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100"
                           readonly disabled>
                    <p class="text-gray-500 text-sm mt-1">URL-friendly version (auto-generated from name)</p>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Optional description">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-gray-700">Category is Active</span>
                    </label>
                    <p class="text-gray-500 text-sm mt-1">Inactive categories won't appear in product selection.</p>
                </div>

                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        Created: {{ $category->created_at->format('M d, Y') }}
                        @if($category->created_at != $category->updated_at)
                            <br>Last Updated: {{ $category->updated_at->format('M d, Y') }}
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Update Category
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-update slug display when name changes
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            const slug = name.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            document.getElementById('slug').value = slug;
        });
    </script>
</body>
</html>