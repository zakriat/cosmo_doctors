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
        Schema::create('doctor_category_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('clinic_id');
            $table->decimal('charges', 10, 2)->default(0);
            $table->boolean('status')->default(1);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better query performance
            $table->index(['doctor_id', 'category_id'], 'idx_doctor_category');
            $table->index('category_id', 'idx_category');
            $table->index('clinic_id', 'idx_clinic');
            $table->index('status', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_category_mappings');
    }
};
