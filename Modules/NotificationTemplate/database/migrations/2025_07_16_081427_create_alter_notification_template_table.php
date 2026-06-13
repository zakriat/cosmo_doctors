<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Constant\Models\Constant;
use Modules\NotificationTemplate\Models\NotificationTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // === Add New Incidence Notification ===
        $template = NotificationTemplate::create([
            'type' => 'new_incidence',
            'name' => 'new_incidence',
            'label' => 'New Incidence Report Created',
            'status' => 1,
            'to' => json_encode(['admin']),
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Resend User Credentials',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New incidence report created',
            'mail_subject' => 'New incidence report created',
            'sms_subject' => 'New incidence report created',
            'whatsapp_subject' => 'New incidence report created',
            'template_detail' => '<p>New incidence report created.</p> <p>Title: [[ title ]] , Description: [[ description ]] and Phone: [[ phone ]] , Email: [[ email ]] and Created By: [[ user_name ]]</p>',
            'mail_template_detail' => '<p>New incidence report created.</p> <p>Title: [[ title ]] , Description: [[ description ]] and Phone: [[ phone ]] , Email: [[ email ]] and Created By: [[ user_name ]]</p>',
            'sms_template_detail' => '<p>Welcome to KiviCare ,</p><p>New incidence report created.</p><p>Title: [[ title ]] , Description: [[ description ]] , Phone: [[ phone ]] , Email: [[ email ]] Created By: [[ user_name ]]</p>',
            'whatsapp_template_detail' => '<p>Welcome to KiviCare ,</p><p>New incidence report created.</p><p>Title: [[ title ]] , Description: [[ description ]] , Phone: [[ phone ]] , Email: [[ email ]] Created By: [[ user_name ]]</p>',
        ]);

        // === Add Incidence Reply Notification ===
        $template = NotificationTemplate::create([
            'type' => 'incidence_reply',
            'name' => 'incidence_reply',
            'label' => 'New Incidence Report Reply',
            'status' => 1,
            'to' => json_encode(['user']),
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Incidence Report Reply by Admin.',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Incidence report reply',
            'mail_subject' => 'Incidence report reply',
            'sms_subject' => 'Incidence report reply',
            'whatsapp_subject' => 'Incidence report reply',
            'template_detail' => '<p>Incidence report reply by Admin.</p> <p>Reply: [[ reply ]]</p>',
            'mail_template_detail' => '<p>Incidence report reply by Admin.</p> <p>Reply: [[ reply ]]</p>',
            'sms_template_detail' => '<p>Incidence report reply by Admin.</p> <p>Reply: [[ reply ]]</p>',
            'whatsapp_template_detail' => '<p>Incidence report reply by Admin.</p> <p>Reply: [[ reply ]]</p>',
        ]);

        // === Add Constants ===
        $types = [
            [
                'type' => 'notification_type',
                'value' => 'new_incidence',
                'name' => 'New Incidence Report Created',
            ],
            [
                'type' => 'notification_type',
                'value' => 'incidence_reply',
                'name' => 'Incidence Report Reply',
            ],
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(
                ['type' => $value['type'], 'value' => $value['value']],
                $value
            );
        }
    }

    public function down()
    {
        NotificationTemplate::whereIn('type', ['new_incidence', 'incidence_reply'])->delete();

        Constant::where('type', 'notification_type')
            ->whereIn('value', ['new_incidence', 'incidence_reply'])
            ->delete();
    }
};
