<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('phone_2')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('id_number')->nullable()->comment('National ID/Passport');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            
            // Address
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Kenya');
            
            // Loyalty fields
            $table->integer('loyalty_points')->default(0);
            $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->integer('total_visits')->default(0);
            $table->date('last_visit')->nullable();
            $table->date('member_since')->nullable();
            
            // Preferences
            $table->boolean('sms_opt_in')->default(false);
            $table->boolean('email_opt_in')->default(false);
            $table->text('notes')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_code', 'email', 'phone']);
            $table->index('tier');
            $table->index('total_spent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};