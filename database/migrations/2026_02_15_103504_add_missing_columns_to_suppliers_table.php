<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Add missing columns one by one
            $table->string('company_name')->nullable()->after('name');
            $table->string('phone_2')->nullable()->after('phone');
            $table->string('website')->nullable()->after('phone_2');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('postal_code')->nullable()->after('country');
            $table->string('supplier_type')->default('wholesale')->after('tax_number');
            $table->decimal('credit_limit', 12, 2)->default(0)->after('supplier_type');
            $table->integer('payment_terms_days')->default(30)->after('credit_limit');
            $table->text('notes')->nullable()->after('payment_terms_days');
            $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $columns = [
                'company_name',
                'phone_2',
                'website',
                'city',
                'state',
                'country',
                'postal_code',
                'supplier_type',
                'credit_limit',
                'payment_terms_days',
                'notes',
                'created_by',
                'updated_by'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('suppliers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};