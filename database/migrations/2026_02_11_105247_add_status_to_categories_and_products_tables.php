<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add status to categories table if not exists
        if (!Schema::hasColumn('categories', 'status')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('description');
            });
        }

        // Add status to products table if not exists
        if (!Schema::hasColumn('products', 'status')) {
            Schema::table('products', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('description');
            });
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};