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
        Schema::table('appointments', function (Blueprint $table) {
            // Add category_id column after service_id
            $table->unsignedBigInteger('category_id')->nullable()->after('service_id');
            
            // Make doctor_id nullable (for categories that don't require doctors)
            $table->unsignedBigInteger('doctor_id')->nullable()->change();
            
            // Add index for better query performance
            $table->index('category_id', 'idx_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('idx_category');
            $table->dropColumn('category_id');
            
            // Note: Cannot easily revert doctor_id to NOT NULL without data loss
            // Manual intervention may be required if rolling back
        });
    }
};
