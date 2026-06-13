<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_triages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('nurse_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->enum('urgency_level', ['E1', 'U2', 'S3', 'R4'])->nullable();
            $table->enum('outcome', ['emergency', 'urgent', 'soon', 'routine', 'redirect', 'home_visit'])->nullable();
            $table->longText('nurse_notes')->nullable();
            $table->unsignedBigInteger('clinician_escalated_to')->nullable();
            $table->longText('handover_summary')->nullable();
            $table->enum('status', ['new', 'in_progress', 'escalated', 'closed'])->default('new');

            // Q10 intake fields
            $table->string('onset_bucket')->nullable();
            $table->enum('trend', ['worse', 'same', 'improving'])->nullable();
            $table->boolean('fever_flag')->nullable();
            $table->tinyInteger('severity_score')->nullable();
            $table->boolean('function_impacted')->nullable();
            $table->boolean('hydration_concern')->nullable();
            $table->json('risk_flags')->nullable();
            $table->text('meds_text')->nullable();
            $table->text('allergy_text')->nullable();
            $table->boolean('recent_antibiotics')->nullable();
            $table->boolean('identity_confirmed')->default(false);

            // Red flag action record
            $table->boolean('red_flag_triggered')->default(false);
            $table->text('red_flag_action_taken')->nullable();

            // Redirect target
            $table->string('redirect_service')->nullable();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_triages');
    }
};
