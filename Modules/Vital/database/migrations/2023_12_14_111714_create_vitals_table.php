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
        Schema::create('vitals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->unsigned();
            $table->double('height_cm');
            $table->double('weight_kg');
            $table->double('height_inch');
            $table->double('weight_pound');
            $table->double('bmi');
            $table->double('tbf');
            $table->double('vfi');
            $table->double('tbw');
            $table->double('sm');
            $table->double('bmc');
            $table->double('bmr');
            // $table->double('initial_height_cm',4, 2);
            // $table->double('initial_height_inch',4, 2);
            // $table->double('initial_weight_kg',4, 2);
            // $table->double('initial_weight_pound',4, 2);
            // $table->double('initial_bmi',4, 2);
            // $table->double('initial_tbf',4, 2);
            // $table->double('initial_vfi',4, 2);
            // $table->double('initial_tbw',4, 2);
            // $table->double('initial_sm',4, 2);
            // $table->double('initial_bmc',4, 2);
            // $table->double('initial_bmr',4, 2);

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vitals');
    }
};
