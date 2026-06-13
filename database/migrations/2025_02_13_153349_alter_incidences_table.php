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
        Schema::table('incidences', function (Blueprint $table) {
            $table->text('profile_image')->nullable()->after('user_id');
            $table->date('incident_date')->nullable()->after('status');
            $table->tinyInteger('incident_type')->default(1)->comment('1 => open 2 => closed 3 => reject')->after('incident_date');
            $table->date('incident_closed_date')->nullable()->after('incident_type');
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
