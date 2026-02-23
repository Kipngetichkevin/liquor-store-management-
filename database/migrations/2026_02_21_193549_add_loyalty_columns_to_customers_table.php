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
            
            // Add phone_2
            if (!Schema::hasColumn('customers', 'phone_2')) {
                $table->string('phone_2')->nullable()->after('phone');
            }
            
            // Add birth_date
            if (!Schema::hasColumn('customers', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('phone_2');
            }
            
            // Add id_number
            if (!Schema::hasColumn('customers', 'id_number')) {
                $table->string('id_number')->nullable()->after('birth_date');
            }
            
            // Add gender
            if (!Schema::hasColumn('customers', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('id_number');
            }
            
            // Add city
            if (!Schema::hasColumn('customers', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            
            // Add state
            if (!Schema::hasColumn('customers', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            
            // Add postal_code
            if (!Schema::hasColumn('customers', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('state');
            }
            
            // Add country
            if (!Schema::hasColumn('customers', 'country')) {
                $table->string('country')->default('Kenya')->after('postal_code');
            }
            
            // Add loyalty_points
            if (!Schema::hasColumn('customers', 'loyalty_points')) {
                $table->integer('loyalty_points')->default(0)->after('country');
            }
            
            // Add tier
            if (!Schema::hasColumn('customers', 'tier')) {
                $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze')->after('loyalty_points');
            }
            
            // Add total_spent - FIXED LINE 69
            if (!Schema::hasColumn('customers', 'total_spent')) {
                $table->decimal('total_spent', 12, 2)->default(0)->after('tier');
            }
            
            // Add total_visits
            if (!Schema::hasColumn('customers', 'total_visits')) {
                $table->integer('total_visits')->default(0)->after('total_spent');
            }
            
            // Add last_visit
            if (!Schema::hasColumn('customers', 'last_visit')) {
                $table->date('last_visit')->nullable()->after('total_visits');
            }
            
            // Add member_since
            if (!Schema::hasColumn('customers', 'member_since')) {
                $table->date('member_since')->nullable()->after('last_visit');
            }
            
            // Add sms_opt_in
            if (!Schema::hasColumn('customers', 'sms_opt_in')) {
                $table->boolean('sms_opt_in')->default(false)->after('member_since');
            }
            
            // Add email_opt_in
            if (!Schema::hasColumn('customers', 'email_opt_in')) {
                $table->boolean('email_opt_in')->default(false)->after('sms_opt_in');
            }
            
            // Add notes
            if (!Schema::hasColumn('customers', 'notes')) {
                $table->text('notes')->nullable()->after('email_opt_in');
            }
            
            // Add created_by
            if (!Schema::hasColumn('customers', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            }
            
            // Add updated_by
            if (!Schema::hasColumn('customers', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columns = [
                'customer_code', 'phone_2', 'birth_date', 'id_number', 'gender',
                'city', 'state', 'postal_code', 'country', 'loyalty_points',
                'tier', 'total_spent', 'total_visits', 'last_visit', 'member_since',
                'sms_opt_in', 'email_opt_in', 'notes', 'created_by', 'updated_by'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};