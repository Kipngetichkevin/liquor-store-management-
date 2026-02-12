<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Make sku nullable if it exists
            if (Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->change();
            }
            
            // Make barcode nullable if it exists
            if (Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->change();
            }
            
            // Make brand nullable if it exists
            if (Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert to NOT NULL (you may lose data if nulls exist)
            $table->string('sku')->nullable(false)->change();
            $table->string('barcode')->nullable(false)->change();
            $table->string('brand')->nullable(false)->change();
        });
    }
};