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
            // Add blood test related columns
            $table->enum('type', ['appointment', 'blood_test'])->default('appointment')->after('status');
            $table->string('gf_entry_id')->unique()->nullable()->after('type');
            $table->boolean('initiated_from_dashboard')->default(false)->after('gf_entry_id');
            $table->string('test_type')->nullable()->after('initiated_from_dashboard');
            $table->json('raw_gf_data')->nullable()->after('test_type');
            $table->timestamp('synced_at')->nullable()->after('raw_gf_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Remove blood test columns
            $table->dropColumn([
                'type',
                'gf_entry_id',
                'initiated_from_dashboard',
                'test_type',
                'raw_gf_data',
                'synced_at'
            ]);
        });
    }
};
