<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nhs_number')->nullable()->after('pincode');
            $table->string('city_or_town')->nullable()->after('nhs_number');
            $table->string('county')->nullable()->after('city_or_town');
            $table->string('postcode')->nullable()->after('county');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nhs_number', 'city_or_town', 'county', 'postcode']);
        });
    }
};