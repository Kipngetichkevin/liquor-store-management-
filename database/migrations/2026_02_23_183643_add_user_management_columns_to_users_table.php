<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role column if missing
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('cashier')->after('email');
            }
            
            // Add is_active column if missing
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            
            // Add last_login_at column if missing
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
            
            // Add last_login_ip column if missing
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
            
            // Add phone column if missing
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('last_login_ip');
            }
            
            // Add employee_id column if missing
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->nullable()->unique()->after('phone');
            }
            
            // Add created_by column if missing
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('employee_id');
            }
            
            // Add updated_by column if missing
            if (!Schema::hasColumn('users', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['role', 'is_active', 'last_login_at', 'last_login_ip', 'phone', 'employee_id', 'created_by', 'updated_by'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};