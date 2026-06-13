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
        Schema::create('clinics_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('system_service_id')->nullable();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('type')->default('in_clinic');
            $table->tinyInteger('is_video_consultancy')->default(0);
            $table->double('charges')->default(0);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->bigInteger('duration_min')->default(15);
            $table->string('time_slot')->default(15);
            $table->boolean('status')->default(1);
            $table->boolean('discount')->default(0);
            $table->double('discount_value')->nullable();
            $table->string('discount_type')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinics_services');
    }
};
