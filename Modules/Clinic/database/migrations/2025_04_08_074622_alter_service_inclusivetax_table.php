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
            $table->boolean('is_inclusive_tax')->default(0);
            $table->longText('inclusive_tax')->nullable();
            $table->double('inclusive_tax_price')->default(0);
        });
        Schema::table('doctor_service_mappings', function (Blueprint $table) {
            $table->double('inclusive_tax_price')->default(0);
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
