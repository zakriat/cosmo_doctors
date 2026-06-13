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
        Schema::create('appointment_patient_records', function (Blueprint $table) {
            $table->id();
            $table->longText('subjective');
            $table->longText('objective');
            $table->longText('assessment');
            $table->longText('plan');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('encounter_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
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
        Schema::dropIfExists('appointment_patient_records');
    }
};
