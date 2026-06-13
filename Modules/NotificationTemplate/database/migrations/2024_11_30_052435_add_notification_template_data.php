<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Modules\Constant\Models\Constant;
use Modules\NotificationTemplate\Models\NotificationTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $types = [
            [
                'type' => 'notification_param_button',
                'value' => 'user_email',
                'name' => 'Customer Email',
            ],
            [
                'type' => 'notification_type',
                'value' => 'resend_user_credentials',
                'name' => 'Resend User Credentials',
            ],
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $template = NotificationTemplate::create([
            'type' => 'resend_user_credentials',
            'name' => 'resend_user_credentials',
            'label' => 'Resend User Credentials',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS'=>'0' ,'IS_WHATSAPP'=>'0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Resend User Credentials',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Resend User Credentials',
            'mail_subject' => 'Resend User Credentials',
            'sms_subject' => 'Resend User Credentials',
            'whatsapp_subject' => 'Resend User Credentials',
            'template_detail' => '<p>Your kivicare account user credential.</p> <p>Your  email:  [[ user_email ]]  ,  username: [[ user_name ]] and password: [[ user_password ]] </p>',
            'mail_template_detail' => '<p>Dear [[ user_name ]],</p><p>
                                    Your kivicare account user credential</p><p>
                                    Your  email:  [[ user_email ]]  ,  username: [[ user_name ]] and password: [[ user_password ]]</p><p>
                                    <p>&nbsp;</p>
                                    <p>[[ logged_in_user_fullname ]]<br>[[ logged_in_user_role ]]<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
                                    
            'sms_template_detail' => '<p>Welcome to KiviCare ,</p>
                                    <p>Your kivicare account user credential.</p> 
                                    <p>Your  email:  [[ user_email ]]  ,  username: [[ user_name ]] and password: [[ user_password ]] </p>',
            'whatsapp_template_detail' => '<p>Welcome to KiviCare ,</p>
                                    <p>Your kivicare account user credential.</p> 
                                    <p>Your  email:  [[ user_email ]]  ,  username: [[ user_name ]] and password: [[ user_password ]] </p>',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
