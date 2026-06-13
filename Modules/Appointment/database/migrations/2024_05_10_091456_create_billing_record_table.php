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
        Schema::create('billing_record', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('encounter_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->unsignedBigInteger('clinic_id')->nullable(); 
            $table->unsignedBigInteger('doctor_id')->nullable(); 
            $table->unsignedBigInteger('service_id')->nullable(); 
            $table->double('total_amount')->default(0); 
            $table->double('service_amount')->default(0); 
            $table->double('discount_amount')->default(0); 
            $table->string('discount_type')->nullable(); 
            $table->double('discount_value')->nullable();
            $table->longText('tax_data')->nullable(); 
            $table->date('date')->nullable(); 
            $table->integer('payment_status')->default(0);
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
        Schema::dropIfExists('billing_record');
    }
};
