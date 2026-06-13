<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triage_pre_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('answers')->nullable();
            $table->boolean('blocker_triggered')->default(false);
            $table->string('blocker_question')->nullable();
            $table->string('recommended_urgency')->nullable();
            $table->string('recommended_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triage_pre_checks');
    }
};
