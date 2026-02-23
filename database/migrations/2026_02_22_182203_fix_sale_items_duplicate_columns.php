<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if subtotal column exists and drop it if it does
        if (Schema::hasColumn('sale_items', 'subtotal')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->dropColumn('subtotal');
            });
        }
        
        // Add the column back properly
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('subtotal', 12, 2)->default(0)->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            if (Schema::hasColumn('sale_items', 'subtotal')) {
                $table->dropColumn('subtotal');
            }
        });
    }
};