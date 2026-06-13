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
        Schema::table('clinics_categories', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('description');
            $table->enum('service_classification', ['doctor_required', 'doctor_optional', 'no_doctor_required'])
                  ->default('doctor_required')
                  ->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinics_categories', function (Blueprint $table) {
            $table->dropColumn(['price', 'service_classification']);
        });
    }
};