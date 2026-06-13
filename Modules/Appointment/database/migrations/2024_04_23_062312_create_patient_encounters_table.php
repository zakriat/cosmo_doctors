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
        Schema::create('patient_encounters', function (Blueprint $table) {
            $table->id();
            $table->dateTime('encounter_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable(); 
            $table->unsignedBigInteger('vendor_id')->nullable(); 
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('encounter_template_id')->nullable();
            $table->longText('description')->nullable();      
            $table->boolean('status')->default(1);         
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_encounters');
    }
};
