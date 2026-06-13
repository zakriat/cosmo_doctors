<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Links a blood test appointment to the GP/clinic appointment that triggered it
            $table->unsignedBigInteger('linked_appointment_id')->nullable()->after('triage_id');
            // Ordered by (staff user who created the blood test order)
            $table->unsignedBigInteger('ordered_by')->nullable()->after('linked_appointment_id');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['linked_appointment_id', 'ordered_by']);
        });
    }
};
