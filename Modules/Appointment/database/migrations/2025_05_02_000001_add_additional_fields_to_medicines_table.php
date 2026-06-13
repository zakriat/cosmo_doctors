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
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('generic_name')->nullable()->after('name');
            $table->string('brand_name')->nullable()->after('generic_name');
            $table->string('strength')->nullable()->after('brand_name');
            $table->string('dosage_form')->nullable()->after('strength'); // tablet, capsule, syrup, injection, etc.
            $table->string('manufacturer')->nullable()->after('dosage_form');
            $table->string('category')->nullable()->after('manufacturer'); // antibiotic, analgesic, etc.
            $table->text('indication')->nullable()->after('side_effects'); // what it's used for
            $table->text('contraindication')->nullable()->after('indication'); // when not to use
            $table->text('drug_interactions')->nullable()->after('contraindication');
            $table->string('pregnancy_category')->nullable()->after('drug_interactions'); // A, B, C, D, X
            $table->text('storage_conditions')->nullable()->after('pregnancy_category');
            $table->decimal('price', 10, 2)->nullable()->after('storage_conditions');
            $table->boolean('status')->default(true)->after('price');
            $table->integer('created_by')->unsigned()->nullable()->after('status');
            $table->integer('updated_by')->unsigned()->nullable()->after('created_by');
            $table->integer('deleted_by')->unsigned()->nullable()->after('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn([
                'generic_name',
                'brand_name', 
                'strength',
                'dosage_form',
                'manufacturer',
                'category',
                'indication',
                'contraindication',
                'drug_interactions',
                'pregnancy_category',
                'storage_conditions',
                'price',
                'status',
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
        });
    }
};