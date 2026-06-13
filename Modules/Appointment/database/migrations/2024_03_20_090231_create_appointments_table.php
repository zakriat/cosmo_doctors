<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default(config('appointment.DEFAULT_STATUS'));
            $table->dateTime('start_date_time')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->dateTime('appointment_date')->nullable();
            $table->Time('appointment_time')->nullable();
            $table->double('duration')->default(0);
            $table->unsignedBigInteger('service_id')->nullable();
            $table->longText('appointment_extra_info')->nullable();
            $table->double('total_amount')->default(0);
            $table->double('service_amount')->default(0);
            $table->double('service_price')->default(0);
            $table->longText('meet_link')->nullable();
            $table->longText('start_video_link')->nullable();
            $table->longText('join_video_link')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');              
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
