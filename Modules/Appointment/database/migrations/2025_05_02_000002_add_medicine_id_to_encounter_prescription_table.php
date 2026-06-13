<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encounter_prescription', function (Blueprint $table) {
            $table->unsignedBigInteger('medicine_id')->nullable()->after('user_id');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('encounter_prescription', function (Blueprint $table) {
            $table->dropForeign(['medicine_id']);
            $table->dropColumn('medicine_id');
        });
    }
};