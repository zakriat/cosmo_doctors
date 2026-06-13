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
        Schema::table('billing_item', function (Blueprint $table) {
            $table->double('inclusive_tax_amount')->default(0);
            $table->longText('inclusive_tax')->nullable();
        });
    }


};
