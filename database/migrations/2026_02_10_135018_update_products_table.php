<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Check if products table exists and update it
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                // Check and add any missing columns
                if (!Schema::hasColumn('products', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }
                
                // Add other columns if they don't exist
                // We'll just do a safe check for each column
                $columnsToCheck = [
                    'category_id' => function() use ($table) {
                        $table->foreignId('category_id')->nullable()->change();
                    },
                    'sku' => function() use ($table) {
                        if (!Schema::hasColumn('products', 'sku')) {
                            $table->string('sku')->unique()->nullable();
                        }
                    },
                    'barcode' => function() use ($table) {
                        if (!Schema::hasColumn('products', 'barcode')) {
                            $table->string('barcode')->unique()->nullable();
                        }
                    },
                    'price' => function() use ($table) {
                        if (!Schema::hasColumn('products', 'price')) {
                            $table->decimal('price', 12, 2);
                        }
                    },
                    'cost_price' => function() use ($table) {
                        if (!Schema::hasColumn('products', 'cost_price')) {
                            $table->decimal('cost_price', 12, 2);
                        }
                    },
                    'quantity' => function() use ($table) {
                        if (!Schema::hasColumn('products', 'quantity')) {
                            $table->integer('quantity')->default(0);
                        }
                    },
                    'min_stock_level' => function() use ($table) {
                        if (!Schema::hasColumn('products', 'min_stock_level')) {
                            $table->integer('min_stock_level')->default(10);
                        }
                    }
                ];
                
                // Execute only if column doesn't exist
                foreach ($columnsToCheck as $column => $closure) {
                    if (!Schema::hasColumn('products', $column)) {
                        $closure();
                    }
                }
            });
        }
    }

    public function down()
    {
        // We don't want to drop columns in rollback to avoid data loss
        Schema::table('products', function (Blueprint $table) {
            // We can optionally drop columns we added, but let's keep it safe
        });
    }
};