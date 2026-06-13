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
        Schema::table('notification_template_content_mapping', function (Blueprint $table) {
            $table->string('user_type')->nullable();
            $table->longText('mail_template_detail')->nullable();
            $table->string('mail_subject')->nullable();
            $table->longText('sms_template_detail')->nullable();
            $table->string('sms_subject')->nullable();
            $table->longText('whatsapp_template_detail')->nullable();
            $table->string('whatsapp_subject')->nullable();
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
