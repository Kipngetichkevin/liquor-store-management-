@extends('layouts.app')

@section('title', 'Add Category - Liquor Store Management')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Category</h1>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Categories
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- Category Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="e.g., Whiskey, Vodka, Wine" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                The slug will be automatically generated from the name.
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Optional description for the category.
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <!-- Status -->
                                <div class="mb-4">
                                    <label class="form-label d-block">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="is_active" name="is_active" value="1" checked>
                                        <label class="form-check-label" for="is_active">
                                            Category is Active
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Inactive categories won't appear in product selection.
                                    </small>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Save Category
                                    </button>
                                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection