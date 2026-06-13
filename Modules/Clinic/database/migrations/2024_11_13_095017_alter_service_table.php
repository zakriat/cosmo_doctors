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
        Schema::table('clinics_services', function (Blueprint $table) {
            $table->tinyInteger('is_enable_advance_payment')->nullable()->default('0');
            $table->double('advance_payment_amount')->nullable();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->double('advance_payment_amount')->nullable();
            $table->double('advance_paid_amount')->nullable();
        });

        Schema::table('appointment_transactions', function (Blueprint $table) {
            $table->tinyInteger('advance_payment_status')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
