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
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('report_file')->nullable()->after('test_type');
            $table->timestamp('report_uploaded_at')->nullable()->after('report_file');
            $table->text('report_notes')->nullable()->after('report_uploaded_at');
            $table->enum('report_status', ['pending', 'ready'])->default('pending')->after('report_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['report_file', 'report_uploaded_at', 'report_notes', 'report_status']);
        });
    }
};
