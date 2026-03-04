<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'mobile_number')) {
                $table->string('mobile_number', 20)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('sales', 'payment_reference')) {
                $table->string('payment_reference', 50)->nullable()->after('mobile_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'mobile_number')) {
                $table->dropColumn('mobile_number');
            }
            if (Schema::hasColumn('sales', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }
        });
    }
};