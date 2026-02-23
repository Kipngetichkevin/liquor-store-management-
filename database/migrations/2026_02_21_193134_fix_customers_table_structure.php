<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Add customer_code if missing
            if (!Schema::hasColumn('customers', 'customer_code')) {
                $table->string('customer_code')->unique()->after('id');
            }
            
            // Add all other missing columns
            if (!Schema::hasColumn('customers', 'name')) {
                $table->string('name')->after('customer_code');
            }
            
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email')->nullable()->unique()->after('name');
            }
            
            if (!Schema::hasColumn('customers', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('customers', 'phone_2')) {
                $table->string('phone_2')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('customers', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('phone_2');
            }
            
            if (!Schema::hasColumn('customers', 'id_number')) {
                $table->string('id_number')->nullable()->after('birth_date');
            }
            
            if (!Schema::hasColumn('customers', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('id_number');
            }
            
            if (!Schema::hasColumn('customers', 'address')) {
                $table->string('address')->nullable()->after('gender');
            }
            
            if (!Schema::hasColumn('customers', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('customers', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            
            if (!Schema::hasColumn('customers', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('state');
            }
            
            if (!Schema::hasColumn('customers', 'country')) {
                $table->string('country')->default('Kenya')->after('postal_code');
            }
            
            if (!Schema::hasColumn('customers', 'loyalty_points')) {
                $table->integer('loyalty_points')->default(0)->after('country');
            }
            
            if (!Schema::hasColumn('customers', 'tier')) {
                $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze')->after('loyalty_points');
            }
            
            if (!Schema::hasColumn('customers', 'total_spent')) {
                $table->decimal('total_spent', 12, 2)->default(0)->after('tier');
            }
            
            if (!Schema::hasColumn('customers', 'total_visits')) {
                $table->integer('total_visits')->default(0)->after('total_spent');
            }
            
            if (!Schema::hasColumn('customers', 'last_visit')) {
                $table->date('last_visit')->nullable()->after('total_visits');
            }
            
            if (!Schema::hasColumn('customers', 'member_since')) {
                $table->date('member_since')->nullable()->after('last_visit');
            }
            
            if (!Schema::hasColumn('customers', 'sms_opt_in')) {
                $table->boolean('sms_opt_in')->default(false)->after('member_since');
            }
            
            if (!Schema::hasColumn('customers', 'email_opt_in')) {
                $table->boolean('email_opt_in')->default(false)->after('sms_opt_in');
            }
            
            if (!Schema::hasColumn('customers', 'notes')) {
                $table->text('notes')->nullable()->after('email_opt_in');
            }
            
            if (!Schema::hasColumn('customers', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('customers', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
            
            if (!Schema::hasColumn('customers', 'deleted_at')) {
                $table->softDeletes();
            }
            
            // Add indexes if they don't exist
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('customers');
            
            if (!array_key_exists('customers_customer_code_email_phone_index', $indexes)) {
                $table->index(['customer_code', 'email', 'phone']);
            }
            
            if (!array_key_exists('customers_tier_index', $indexes)) {
                $table->index('tier');
            }
            
            if (!array_key_exists('customers_total_spent_index', $indexes)) {
                $table->index('total_spent');
            }
        });
    }

    public function down(): void
    {
        // It's safer not to drop columns in down() to prevent data loss
        // But if you need to revert, you can add drop column statements here
    }
};