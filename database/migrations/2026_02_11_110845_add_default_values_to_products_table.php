<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Set default for 'quantity' column if it exists
            if (Schema::hasColumn('products', 'quantity')) {
                $table->integer('quantity')->default(0)->change();
            }

            // Set default for 'stock_quantity' column if it exists
            if (Schema::hasColumn('products', 'stock_quantity')) {
                $table->integer('stock_quantity')->default(0)->change();
            }

            // Ensure 'min_stock_level' has a default
            if (Schema::hasColumn('products', 'min_stock_level')) {
                $table->integer('min_stock_level')->default(10)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove defaults (revert to no default)
            if (Schema::hasColumn('products', 'quantity')) {
                $table->integer('quantity')->default(null)->change();
            }
            if (Schema::hasColumn('products', 'stock_quantity')) {
                $table->integer('stock_quantity')->default(null)->change();
            }
            if (Schema::hasColumn('products', 'min_stock_level')) {
                $table->integer('min_stock_level')->default(null)->change();
            }
        });
    }
};