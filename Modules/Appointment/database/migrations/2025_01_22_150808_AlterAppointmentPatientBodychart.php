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
        Schema::table('appointment_patient_bodychart', function (Blueprint $table) {
            $table->longText('name')->change();
            $table->longText('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_patient_bodychart', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('description')->change();
        });
    }
};
