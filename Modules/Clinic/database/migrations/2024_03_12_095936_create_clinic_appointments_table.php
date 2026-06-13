<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('clinic_appointments', function (Blueprint $table) {
            $table->id();
            $table->integer('clinic_id');
            $table->dateTime('appointment_date')->nullable(); // Use dateTime() for appointment_date
            $table->dateTime('appointment_time')->nullable(); // Use dateTime() for appointment_time
            $table->integer('vendor_id')->nullable();
            $table->string('doctor_id')->nullable();
            $table->string('service_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('status')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->timestamps(); // This line creates created_at and updated_at columns
            $table->softDeletes();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('clinic_appointments');
    }
};
