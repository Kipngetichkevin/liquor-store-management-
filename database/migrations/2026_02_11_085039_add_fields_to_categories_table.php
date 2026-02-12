<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('categories', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('image');
            }
            
            if (!Schema::hasColumn('categories', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('parent_id');
            }
            
            if (!Schema::hasColumn('categories', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
            
            // Add foreign key constraint for parent_id if it exists
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->dropForeign(['parent_id']);
            }
            
            $columnsToDrop = ['image', 'parent_id', 'created_by', 'updated_by'];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};