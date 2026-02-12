<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add alcohol_percentage if missing
            if (!Schema::hasColumn('products', 'alcohol_percentage')) {
                $table->decimal('alcohol_percentage', 5, 2)->nullable()->after('status');
            }

            // Add volume_ml if missing (this is the one causing the error)
            if (!Schema::hasColumn('products', 'volume_ml')) {
                $table->integer('volume_ml')->nullable()->after('alcohol_percentage');
            }

            // Add brand if missing
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('volume_ml');
            }

            // Ensure sku is nullable (if not already)
            if (Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->change();
            }

            // Add stock_quantity if missing (from previous migration)
            if (!Schema::hasColumn('products', 'stock_quantity')) {
                $table->integer('stock_quantity')->default(0)->after('brand');
            }

            // Add min_stock_level if missing
            if (!Schema::hasColumn('products', 'min_stock_level')) {
                $table->integer('min_stock_level')->default(10)->after('stock_quantity');
            }

            // Add cost_price if missing
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->nullable()->after('price');
            }

            // Add barcode if missing
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('sku');
            }

            // Add image if missing (for product photos)
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('barcode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'alcohol_percentage',
                'volume_ml',
                'brand',
                'stock_quantity',
                'min_stock_level',
                'cost_price',
                'barcode',
                'image'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};