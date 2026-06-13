<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\Constant\Models\Constant;
use Modules\NotificationTemplate\Models\NotificationTemplate;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        /*
         * NotificationTemplates Seed
         * ------------------
         */

        // DB::table('notificationtemplates')->truncate();
        // echo "Truncate: notificationtemplates \n";

        $types = [
            [
                'type' => 'notification_type',
                'value' => 'new_appointment',
                'name' => 'New Appointment Booked',
            ],
            [
                'type' => 'notification_type',
                'value' => 'accept_appointment',
                'name' => 'Appointment Accepted',
            ],
            [
                'type' => 'notification_type',
                'value' => 'reject_appointment',
                'name' => 'Your appointment is rejected',
            ],
            [
                'type' => 'notification_type',
                'value' => 'accept_appointment_request',
                'name' => 'Appointment Completion',
            ],
            [
                'type' => 'notification_type',
                'value' => 'checkout_appointment',
                'name' => 'Complete On Appointment',
            ],
            [
                'type' => 'notification_type',
                'value' => 'cancel_appointment',
                'name' => 'Appointment Cancellation',
            ],
            [
                'type' => 'notification_type',
                'value' => 'reschedule_appointment',
                'name' => 'Appointment Rescheduled',
            ],
            [
                'type' => 'notification_type',
                'value' => 'quick_appointment',
                'name' => 'Appointment Confirmation',
            ],
            [
                'type' => 'notification_type',
                'value' => 'change_password',
                'name' => 'Change Password',
            ],

            [
                'type' => 'notification_type',
                'value' => 'forget_email_password',
                'name' => 'Forget Email/Password',
            ],

            [
                'type' => 'notification_type',
                'value' => 'new_request_service',
                'name' => 'New Service Request Found!',
            ],

            [
                'type' => 'notification_type',
                'value' => 'accept_request_service',
                'name' => 'Service Request Acceptance',
            ],

            [
                'type' => 'notification_type',
                'value' => 'reject_request_service',
                'name' => 'Service Request Rejected',
            ],

            [
                'type' => 'notification_param_button',
                'value' => 'id',
                'name' => 'ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_name',
                'name' => 'Customer Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'description',
                'name' => 'Description / Note',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'appointment_id',
                'name' => 'Appointment ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'appointment_date',
                'name' => 'Appointment Date',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'appointment_time',
                'name' => 'Appointment Time',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'appointment_services_names',
                'name' => 'Appointment Services Names',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'appointment_duration',
                'name' => 'Appointment Duration',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'doctor_name',
                'name' => 'Doctor Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'venue_address',
                'name' => 'Venue / Address',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'logged_in_user_fullname',
                'name' => 'Your Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'logged_in_user_role',
                'name' => 'Your Position',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'company_name',
                'name' => 'Company Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'company_contact_info',
                'name' => 'Company Info',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_id',
                'name' => 'User\' ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_password',
                'name' => 'User Password',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'link',
                'name' => 'Link',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'site_url',
                'name' => 'Site URL',
            ],
            [
                'type' => 'notification_to',
                'value' => 'user',
                'name' => 'User',
            ],
            [
                'type' => 'notification_to',
                'value' => 'doctor',
                'name' => 'Doctor',
            ],
            [
                'type' => 'notification_to',
                'value' => 'admin',
                'name' => 'Admin',
            ],
            [
                'type' => 'notification_to',
                'value' => 'vendor',
                'name' => 'Vendor',
            ],
            [
                'type' => 'notification_to',
                'value' => 'receptionist',
                'name' => 'Receptionist',
            ],
            [

                'type' => 'notification_type',
                'value' => 'new_request_service',
                'name' => 'New Service Request',
            ],
            [
                'type' => 'notification_type',
                'value' => 'accept_request_service',
                'name' => 'Accept Service Request',
            ],
            [
                'type' => 'notification_type',
                'value' => 'reject_request_service',
                'name' => 'Reject Service Request',
            ],
            [
                'type' => 'notification_type',
                'value' => 'wallet_refund',
                'name' => 'Wallet Refund',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'clinic_id',
                'name' => 'Clinic id',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'clinic_name',
                'name' => 'Clinic Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'title',
                'name' => 'Title',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'description',
                'name' => 'Description',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'phone_number',
                'name' => 'Phone number',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'email',
                'name' => 'Email',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'reply',
                'name' => 'Reply',
            ],
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }


        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('notification_templates')->delete();
        DB::table('notification_template_content_mapping')->delete();

        $template = NotificationTemplate::create([
            'type' => 'new_appointment',
            'name' => 'new_appointment',
            'label' => 'Appointment confirmation',
            'status' => 1,
            'to' => '["admin", "vendor","receptionist", "doctor"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Thank you for choosing our services! Your appointment has been successfully confirmed. We look forward to serving you and providing an exceptional experience. Stay tuned for further updates.',
            'status' => 1,
            'subject' => 'New Appointment Booked - Confirmed!',
            'user_type' => 'admin',  // Example user type
            'template_detail' => '<p>New Appointment Booked! Appointment ID: [[ id ]]. Please check the details in the system</p>',
            'mail_template_detail' => '<p>Dear [[ admin_name ]],</p>
<p>We are pleased to inform you that a new appointment has been successfully booked. Below are the appointment details:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Patient Name: [[ user_name&nbsp;]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
<li>Services: [[ appointment_services_names ]]</li>
<li>Location: [[ venue_address ]]</li>
</ul>
<p>Please review the appointment and ensure all arrangements are in place. If there are any changes, kindly update the system promptly.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Confirmed - Details Inside!',
            'sms_template_detail' => '<p><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">New Appointment Booked! Appointment ID: [[ id ]]. Please check the details in the system. </span></p>',
            'sms_subject' => 'New Appointment Booked - Confirmed!',
            'whatsapp_template_detail' => '<p>New Appointment Booked! Appointment ID: [[ id ]]. Please check the details in the system.</p>',
            'whatsapp_subject' => 'New Appointment Booked - Confirmed!',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Thank you for choosing our services! Your appointment has been successfully confirmed. We look forward to serving you and providing an exceptional experience. Stay tuned for further updates.',
            'status' => 1,
            'subject' => 'New Appointment Alert',
            'user_type' => 'vendor',  // Vendor user type
            'template_detail' => '<p>New Appointment Alert! Scheduled on [[ appointment_date ]] at [[ appointment_time ]]. Check details.</p>',
            'mail_template_detail' => '<p><strong>Dear [[ vendor_name ]],</strong></p>
            <p>We are excited to inform you that a new appointment has been scheduled. Please find the details below:</p>
            <p><strong>Appointment Details:</strong></p>
            <ul>
            <li><strong>Appointment ID:</strong> #[[ appointment_id ]]</li>
            <li><strong>Date:</strong> [[ appointment_date ]]</li>
            <li><strong>Time:</strong> [[ appointment_time ]]</li>
            <li><strong>Location:</strong> [[ venue_address ]]</li>
            </ul>
            <p>Kindly review the information and prepare for the appointment. If you have any questions or require assistance, please feel free to reach out to our team.</p>
            <p><strong>Best regards,</strong><br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
            <p>&nbsp;</p>',
            'mail_subject' => 'New Appointment Booked',
            'sms_template_detail' => '<p>New Appointment Alert! Scheduled on [[ appointment_date ]] at [[ appointment_time ]]. Check details.</p>',
            'sms_subject' => 'New Appointment Booked!',
            'whatsapp_template_detail' => '<p>New Appointment Alert! Scheduled on [[ appointment_date ]] at [[ appointment_time ]]. Check details.</p>',
            'whatsapp_subject' => 'New Appointment Booked!',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Thank you for choosing our services! Your appointment has been successfully confirmed. We look forward to serving you and providing an exceptional experience. Stay tuned for further updates.',
            'status' => 1,
            'subject' => 'New Patient Appointment Scheduled',
            'user_type' => 'doctor',
            'template_detail' => '<p>New Appointment Confirmed with [[ user_name ]] on [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'mail_template_detail' => '<p>Dear Dr. [[ doctor_name ]],</p>
            <p>We are pleased to confirm your upcoming appointment with [[ patient_name ]]. Below are the details:</p>
            <p>Appointment Details:</p>
            <ul>
            <li>Appointment ID: #[[ appointment_id ]]</li>
            <li>Date: [[ appointment_date ]]</li>
            <li>Time: [[ appointment_time ]]</li>
            <li>Services: [[ appointment_services_names ]]</li>
            <li>Duration: [[ appointment_duration ]]</li>
            <li>Location: [[ venue_address ]]</li>
            </ul>
            <p>Please prepare accordingly. If you have any questions or need further assistance, feel free to contact us.</p>
            <p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
            <p>&nbsp;</p>',
            'mail_subject' => 'New Patient Appointment Scheduled',
            'sms_template_detail' => '<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-family: Arial;"><span style="font-size: 14.6667px; white-space-collapse: preserve;">New Appointment Confirmed with [[ user_name ]] on [[ appointment_date ]] at [[ appointment_time ]].</span></span></p>',
            'sms_subject' => 'New Patient Appointment Scheduled',
            'whatsapp_template_detail' => '<p>New Appointment Confirmed with [[ user_name ]] on [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'whatsapp_subject' => 'New Patient Appointment Scheduled',

        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Thank you for choosing our services! Your appointment has been successfully confirmed. We look forward to serving you and providing an exceptional experience. Stay tuned for further updates.',
            'status' => 1,
            'subject' => 'New Appointment Added to Your Schedule',
            'user_type' => 'receptionist',
            'template_detail' => '<p>New Appointment Added: [[ user_name ]] on [[ appointment_date ]]. Please prepare.</p>',
            'mail_template_detail' => '<p>Dear [[ receptionist_name ]],</p>
<p>A new appointment has been scheduled. Please find the details below:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Patient Name: [[ patient_name ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
<li>Service: [[ appointment_services_names ]]</li>
<li>Location: [[ venue_address ]]</li>
</ul>
<p>Please ensure all arrangements are confirmed. If necessary, contact the patient to verify any details.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Appointment Confirmation - Admin Notification',
            'sms_template_detail' => '<p>New Appointment Added: [[ user_name ]] on [[ appointment_date ]]. Please prepare.</p>',
            'sms_subject' => 'New Appointment Added to Your Schedule',
            'whatsapp_template_detail' => '<p>New Appointment Added: [[ user_name ]] on [[ appointment_date ]]. Please prepare.</p>',
            'whatsapp_subject' => 'New Appointment Added to Your Schedule',

        ]);


        $template = NotificationTemplate::create([
            'type' => 'accept_appointment',
            'name' => 'accept_appointment',
            'label' => 'Appointment Accepted',
            'status' => 1,
            'to' => '["user","admin","vendor"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Welcome to your appointment accommodation. We hope you have a pleasant stay!',
            'status' => 1,
            'subject' => 'Appointment Accepted',
            'user_type' => 'user',
            'template_detail' => '<p>Dr. [[ doctor_name ]] confirmed your appointment. ID: #[[ appointment_id ]].</p>',
            'mail_template_detail' => '<p>We are pleased to inform you that your appointment has been accepted by Dr. [[ doctor_name ]]. Below are the details:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
<li>Services: [[ appointment_services_names ]]</li>
<li>Location: [[ venue_address ]]</li>
</ul>
<p>We look forward to seeing you soon! If you have any questions or need to make changes, please feel free to contact us.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Your Appointment is Confirmed!',
            'sms_template_detail' => '<p>Dr. [[ doctor_name ]] confirmed your appointment. ID: #[[ appointment_id ]].</p>',
            'sms_subject' => 'Appointment Accepted',
            'whatsapp_template_detail' => '<p>Dr. [[ doctor_name ]] confirmed your appointment. ID: #[[ appointment_id ]].</p>',
            'whatsapp_subject' => 'Appointment Accepted',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Welcome to your appointment accommodation. We hope you have a pleasant stay!',
            'status' => 1,
            'subject' => 'Appointment Accepted',
            'user_type' => 'admin',
            'template_detail' => '<p>An appointment has been accepted. ID: #[[ appointment_id ]]. Please review.</p>',
            'mail_template_detail' => '<p>Dear [[ admin_name ]],</p>
<p>We are pleased to inform you that the doctor has accepted an appointment. Below are the details:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Patient Name: [[ patient_name ]]</li>
<li>Doctor: Dr. [[ doctor_name ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
<li>Services: [[ appointment_services_names ]]</li>
<li>Location: [[ venue_address ]]</li>
</ul>
<p>Please log in to the system to review the appointment details.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Accepted!',
            'sms_template_detail' => '<p>An appointment has been accepted. ID: #[[ appointment_id ]]. Please review.</p>',
            'sms_subject' => 'Appointment Accepted',
            'whatsapp_template_detail' => '<p>An appointment has been accepted. ID: #[[ appointment_id ]]. Please review.</p>',
            'whatsapp_subject' => 'Appointment Accepted',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Welcome to your appointment accommodation. We hope you have a pleasant stay!',
            'status' => 1,
            'subject' => 'Appointment Accepted',
            'user_type' => 'vendor',
            'template_detail' => '<p>New appointment accepted. ID: #[[ appointment_id ]]. Prepare accordingly.</p>',
            'mail_template_detail' => '<p>Dear [[ vendor_name ]],</p>
<p>The doctor has accepted a new appointment. Below are the details:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
<li>Location: [[ venue_address ]]</li>
</ul>
<p>Please make the necessary preparations for the appointment.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Appointment Confirmation Update',
            'sms_template_detail' => '<p>New appointment accepted. ID: #[[ appointment_id ]]. Prepare accordingly.</p>',
            'sms_subject' => 'Appointment Accepted',
            'whatsapp_template_detail' => '<p>New appointment accepted. ID: #[[ appointment_id ]]. Prepare accordingly.</p>',
            'whatsapp_subject' => 'Appointment Accepted',
        ]);



        $template = NotificationTemplate::create([
            'type' => 'reject_appointment',
            'name' => 'reject_appointment',
            'label' => 'Your appointment is rejected',
            'status' => 1,
            'to' => '["user","admin","vendor"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Thank you for choosing our services. Please remember to check out by [check-out time]. We hope you had a wonderful experience!',
            'status' => 1,
            'subject' => 'Appointment Rejected',
            'user_type' => 'user',
            'template_detail' => '<p>Your appointment with Dr. [[ doctor_name ]] was rejected. ID: #[[ appointment_id ]].</p>',
            'mail_template_detail' => '<p>Dear [[ user_name ]],</p>
<p>Thank you for choosing our services. Unfortunately, we regret to inform you that your appointment has been rejected. Below are the details:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Doctor: Dr. [[ doctor_name ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
</ul>
<p>We apologize for any inconvenience caused. Please contact us at your earliest convenience to reschedule your appointment.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Your Appointment Has Been Rejected',
            'sms_template_detail' => '<p>Your appointment with Dr. [[ doctor_name ]] was rejected. ID: #[[ appointment_id ]].</p>',
            'sms_subject' => 'Your appointment is rejected',
            'whatsapp_template_detail' => '<p>Your appointment with Dr. [[ doctor_name ]] was rejected. ID: #[[ appointment_id ]].</p>',
            'whatsapp_subject' => 'Your appointment is rejected',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'An appointment has been rejected. Please check the details and take necessary actions.',
            'status' => 1,
            'subject' => 'Appointment Rejected',
            'user_type' => 'admin',
            'template_detail' => '<p>An appointment was rejected. ID: #[[ appointment_id ]].</p>
<div id="sharing-aria-speakable" class="sharing-aria-region" role="region" aria-live="assertive" aria-atomic="true"></div>
<div id="sharing-aria-speakable" class="sharing-aria-region" role="region" aria-live="assertive" aria-atomic="true"></div>',
            'mail_template_detail' => '<p>Subject: Appointment Rejected - Appointment ID #[[ appointment_id ]]</p>
<p>Dear [[ admin_name ]],</p>
<p>We regret to inform you that an appointment has been rejected. Below are the details:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Patient Name: [[ user_name ]]</li>
<li>Doctor: Dr. [[ doctor_name ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
<li>Services: [[ appointment_services_names ]]</li>
</ul>
<p>Please log in to the system for more information and to take any necessary actions.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Appointment Rejected',
            'sms_template_detail' => '<p>An appointment was rejected. ID: #[[ appointment_id ]].</p>',
            'sms_subject' => 'Appointment Rejected',
            'whatsapp_template_detail' => '<p>An appointment was rejected. ID: #[[ appointment_id ]].</p>',
            'whatsapp_subject' => 'Appointment Rejected',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'An appointment has been rejected. Please check the details and update your records accordingly.',
            'status' => 1,
            'subject' => 'Appointment Canceled',
            'user_type' => 'vendor',
            'template_detail' => '<p>An appointment has been canceled. ID: #[[ appointment_id ]].</p>',
            'mail_template_detail' => '<p>Subject: Appointment Canceled - Appointment ID #[[ appointment_id ]]</p>
<p>Dear [[ vendor_name ]],</p>
<p>We regret to inform you that an appointment has been canceled. Below are the details:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
</ul>
<p>Please check for updates in your dashboard and take any necessary actions.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Appointment Rejection Notice',
            'sms_template_detail' => '<p>An appointment has been canceled. ID: #[[ appointment_id ]].</p>',
            'sms_subject' => 'Appointment Canceled',
            'whatsapp_template_detail' => '<p>An appointment has been canceled. ID: #[[ appointment_id ]].</p>',
            'whatsapp_subject' => 'Appointment Canceled',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'checkout_appointment',
            'name' => 'checkout_appointment',
            'label' => 'Appointment Completion',
            'status' => 1,
            'to' => '["user","admin","vendor", "doctor","receptionist"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Congratulations! Your appointment has been successfully completed. We appreciate your business and look forward to serving you again.',
            'status' => 1,
            'subject' => 'Appointment Completed',
            'user_type' => 'user',
            'template_detail' => '<p>Your appointment with Dr. [[ doctor_name ]] has been completed. ID: #[[ appointment_id ]].</p>',
            'mail_template_detail' => '<p>Dear [[ patient_name ]],</p>
<p>We are pleased to inform you that your recent appointment with Dr. [[ doctor_name ]] has been successfully completed. Thank you for choosing our services.</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Services Provided: [[ appointment_services_names ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
</ul>
<p>We truly appreciate your trust and look forward to serving you again in the future.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Thank You for Your Appointment!',
            'sms_template_detail' => '<p>Your appointment with Dr. [[ doctor_name ]] has been completed. ID: #[[ appointment_id ]].</p>',
            'sms_subject' => 'Thank You for Visiting!',
            'whatsapp_template_detail' => '<p>Your appointment with Dr. [[ doctor_name ]] has been completed. ID: #[[ appointment_id ]].</p>',
            'whatsapp_subject' => 'Thank You for Visiting!',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your appointment with the patient has been completed. Please review the details and follow up as necessary.',
            'status' => 1,
            'subject' => 'Appointment Completed',
            'user_type' => 'doctor',
            'template_detail' => '<p>Your appointment with [[ user_name ]] was completed. ID: #[[ appointment_id ]].</p>',
            'mail_template_detail' => '<p>Dear Dr. [[ doctor_name ]],</p>
<p>We are pleased to inform you that the appointment with [[ patient_name ]] has been successfully completed. Below are the details:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Services Provided: [[ appointment_services_names ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
<li>Duration: [[ appointment_duration ]]</li>
</ul>
<p>Thank you for your dedication and excellent service.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Successfully Completed',
            'sms_template_detail' => '<p>Your appointment with [[ user_name ]] was completed. ID: #[[ appointment_id ]].</p>',
            'sms_subject' => 'Appointment Completed',
            'whatsapp_template_detail' => '<p>Your appointment with [[ user_name ]] was completed. ID: #[[ appointment_id ]].</p>',
            'whatsapp_subject' => 'Appointment Completed',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your appointment with the patient has been completed. Please review the details and follow up as necessary.',
            'status' => 1,
            'subject' => 'Appointment Completed',
            'user_type' => 'admin',
            'template_detail' => '<p>The appointment has been completed. ID: #[[ appointment_id ]].</p>',
            'mail_template_detail' => '<p>Dear [[ admin_name ]],</p>
<p>We are pleased to inform you that the appointment has been successfully completed. Below are the details:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Patient Name: [[ patient_name ]]</li>
<li>Doctor: Dr. [[ doctor_name ]]</li>
<li>Services Provided: [[ appointment_services_names ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
<li>Location: [[ venue_address ]]</li>
</ul>
<p>Please log in to the system to review the appointment details.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Appointment Completion and Invoice Details',
            'sms_template_detail' => '<p>The appointment has been completed. ID: #[[ appointment_id ]].</p>',
            'sms_subject' => 'Appointment Completed',
            'whatsapp_template_detail' => '<p>The appointment has been completed. ID: #[[ appointment_id ]].</p>',
            'whatsapp_subject' => 'Appointment Completed',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your appointment with the patient has been completed. Please review the details and follow up as necessary.',
            'status' => 1,
            'subject' => 'Appointment Completed',
            'user_type' => 'vendor',
            'template_detail' => '<p>The appointment has been completed. ID: #[[ appointment_id ]].</p>',
            'mail_template_detail' => '<p>Dear [[ vendor_name ]],</p>
<p>The scheduled appointment has been successfully completed. Below are the details:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
<li>Services Provided: [[ appointment_services_names ]]</li>
</ul>
<p>Thank you for your support and cooperation.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Appointment Completed',
            'sms_template_detail' => '<p>Subject: Appointment Completion and Invoice Details</p>
<p>&nbsp;</p>
<p>Dear Dr. [[ user_name ]],</p>
<p>&nbsp;</p>
<p>We are writing to inform you that your recent appointment with the patient has been successfully completed. We appreciate your dedication to providing excellent care and the opportunity to collaborate with you.</p>
<p>&nbsp;</p>
<h4>Appointment Details:</h4>
<p>&nbsp;</p>
<p>Appointment Date: [[ appointment_date ]]</p>
<p>Appointment Time: [[ appointment_time ]]</p>
<p>Service Provided: [[ appointment_services_names ]]</p>
<p>Service Duration: [[ appointment_duration ]]</p>
<p>Patient: [[ patient_name ]]</p>
<p>&nbsp;</p>
<p>The appointment was completed smoothly, and we hope it met or exceeded expectations. Please review the attached invoice for the services rendered. The invoice includes a detailed breakdown of the services provided, any applicable taxes, and the total amount due. If the invoice is hosted online, please refer to the provided instructions to access it.</p>
<p>&nbsp;</p>
<p>Thank you for your continued commitment and excellent service.</p>
<p>&nbsp;</p>
<p>Best regards,</p>
<p>&nbsp;</p>
<p>[[ logged_in_user_fullname ]]<br>[[ logged_in_user_role ]]<br>[[ company_name ]]</p>
<p>[[ company_contact_info ]]</p>',
            'sms_subject' => 'Appointment Completed',
            'whatsapp_template_detail' => '<p>Subject: Appointment Completion and Invoice Details</p>
<p>&nbsp;</p>
<p>Dear Dr. [[ user_name ]],</p>
<p>&nbsp;</p>
<p>We are writing to inform you that your recent appointment with the patient has been successfully completed. We appreciate your dedication to providing excellent care and the opportunity to collaborate with you.</p>
<p>&nbsp;</p>
<h4>Appointment Details:</h4>
<p>&nbsp;</p>
<p>Appointment Date: [[ appointment_date ]]</p>
<p>Appointment Time: [[ appointment_time ]]</p>
<p>Service Provided: [[ appointment_services_names ]]</p>
<p>Service Duration: [[ appointment_duration ]]</p>
<p>Patient: [[ patient_name ]]</p>
<p>&nbsp;</p>
<p>The appointment was completed smoothly, and we hope it met or exceeded expectations. Please review the attached invoice for the services rendered. The invoice includes a detailed breakdown of the services provided, any applicable taxes, and the total amount due. If the invoice is hosted online, please refer to the provided instructions to access it.</p>
<p>&nbsp;</p>
<p>Thank you for your continued commitment and excellent service.</p>
<p>&nbsp;</p>
<p>Best regards,</p>
<p>&nbsp;</p>
<p>[[ logged_in_user_fullname ]]<br>[[ logged_in_user_role ]]<br>[[ company_name ]]</p>
<p>[[ company_contact_info ]]</p>',
            'whatsapp_subject' => 'Appointment Completed',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your appointment with the patient has been completed. Please review the details and follow up as necessary.',
            'status' => 1,
            'subject' => 'Appointment Marked Complete',
            'user_type' => 'receptionist',
            'template_detail' => '<p>Dr. [[ doctor_name ]] completed an appointment. ID: #[[ appointment_id ]].</p>',
            'mail_template_detail' => '<p>Dear [[ receptionist_name ]],</p>
<p>The following appointment has been marked as completed. Please update the records as necessary.</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Patient Name: [[ patient_name ]]</li>
<li>Doctor: Dr. [[ doctor_name ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
</ul>
<p>Thank you for your attention to this matter.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Marked Complete',
            'sms_template_detail' => '<p>Dr. [[ doctor_name ]] completed an appointment. ID: #[[ appointment_id ]].</p>',
            'sms_subject' => 'Appointment Marked Complete',
            'whatsapp_template_detail' => '<p>Dr. [[ doctor_name ]] completed an appointment. ID: #[[ appointment_id ]].</p>',
            'whatsapp_subject' => 'Appointment Marked Complet',
        ]);



        $template = NotificationTemplate::create([
            'type' => 'cancel_appointment',
            'name' => 'cancel_appointment',
            'label' => 'Appointment Cancellation',
            'status' => 1,
            'to' => '["user","admin","vendor", "doctor","receptionist"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'We regret to inform you that your appointment has been cancelled. If you have any questions or need further assistance, please contact our support team.',
            'status' => 1,
            'subject' => 'Appointment Cancelled',
            'user_type' => 'user',
            'template_detail' => '<p>Your appointment scheduled for [[ appointment_date ]] at [[ appointment_time ]] has been cancelled.</p>',
            'mail_template_detail' => '<p>Dear [[ patient_name ]],</p>
<p>We regret to inform you that your appointment scheduled for [[ appointment_date ]] at [[ appointment_time ]] has been cancelled.</p>
<p>If you have any questions or require further assistance, please feel free to contact our support team at [[ company_contact_info ]].</p>
<p>We sincerely apologize for any inconvenience this may cause and appreciate your understanding.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Cancelled',
            'sms_template_detail' => '<p>Your appointment scheduled for [[ appointment_date ]] at [[ appointment_time ]] has been cancelled.</p>',
            'sms_subject' => 'Appointment Cancelled',
            'whatsapp_template_detail' => '<p>Your appointment scheduled for [[ appointment_date ]] at [[ appointment_time ]] has been cancelled.</p>',
            'whatsapp_subject' => 'Appointment Cancelled',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'We regret to inform you that your appointment with the patient has been cancelled. Please check your schedule for further updates.',
            'status' => 1,
            'subject' => 'Appointment Cancelled',
            'user_type' => 'doctor',
            'template_detail' => '<p>The appointment with [[ user_name ]] has been cancelled. ID: #[[ appointment_id ]].</p>',
            'mail_template_detail' => '<p>Dear Dr. [[ doctor_name ]],</p>
<p>We regret to inform you that the appointment with [[ patient_name ]] has been cancelled.</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Scheduled Date: [[ appointment_date ]]</li>
<li>Scheduled Time: [[ appointment_time ]]</li>
<li>Services: [[ appointment_services_names ]]</li>
</ul>
<p>Please update your schedule accordingly.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Appointment Cancellation Notification',
            'sms_template_detail' => '<p>The appointment with [[ user_name ]] has been cancelled. ID: #[[ appointment_id ]].</p>',
            'sms_subject' => 'Appointment Cancelled',
            'whatsapp_template_detail' => '<p>The appointment with [[ user_name ]] has been cancelled. ID: #[[ appointment_id ]].</p>',
            'whatsapp_subject' => 'Appointment Cancelled',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'We regret to inform you that your appointment with the patient has been cancelled. Please check your schedule for further updates.',
            'status' => 1,
            'subject' => 'Appointment Cancelled',
            'user_type' => 'admin',
            'template_detail' => '<p>The appointment with ID #[[ appointment_id ]] has been cancelled.</p>',
            'mail_subject' => 'Appointment Cancellation Notification',
            'mail_template_detail' => '<p>Dear [[ admin_name ]],</p>
<p>We would like to inform you that the following appointment has been cancelled:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Patient Name: [[ patient_name ]]</li>
<li>Doctor: Dr. [[ doctor_name ]]</li>
<li>Scheduled Date: [[ appointment_date ]]</li>
<li>Scheduled Time: [[ appointment_time ]]</li>
</ul>
<p>Please review the cancellation details in the system and take any necessary actions.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Cancellation Notification',
            'sms_template_detail' => '<p>The appointment with ID #[[ appointment_id ]] has been cancelled.</p>',
            'sms_subject' => 'Appointment Cancelled',
            'whatsapp_template_detail' => '<p>The appointment with ID #[[ appointment_id ]] has been cancelled.</p>',
            'whatsapp_subject' => 'Appointment Cancelled',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'We regret to inform you that your appointment with the patient has been cancelled. Please check your schedule for further updates.',
            'status' => 1,
            'subject' => 'Appointment Cancelled',
            'user_type' => 'vendor',
            'template_detail' => '<p>The appointment with [[ user_name ]] has been cancelled. ID: #[[ appointment_id ]].</p>',
            'mail_subject' => 'Appointment Cancellation Notification',
            'mail_template_detail' => '<p>Dear [[ vendor_name ]],</p>
<p>We regret to inform you that the appointment with [[ patient_name ]] has been cancelled.</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
</ul>
<p>Please make the necessary adjustments to your schedule.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Cancellation Alert',
            'sms_template_detail' => '<p>The appointment with [[ user_name ]] has been cancelled. ID: #[[ appointment_id ]].</p>',
            'sms_subject' => 'Appointment Cancellation Alert',
            'whatsapp_template_detail' => '<p>The appointment with [[ user_name ]] has been cancelled. ID: #[[ appointment_id ]].</p>',
            'whatsapp_subject' => 'Appointment Cancellation Alert',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'We regret to inform you that your appointment with the patient has been cancelled. Please check your schedule for further updates.',
            'status' => 1,
            'subject' => 'Appointment Cancelled',
            'user_type' => 'receptionist',
            'template_detail' => '<p>The appointment with [[ user_name ]] has been cancelled. ID: #[[ appointment_id ]].</p>',
            'mail_template_detail' => '<p>Dear [[ receptionist_name ]],</p>
<p>We regret to inform you that the following appointment has been cancelled:</p>
<p>Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Patient Name: [[ patient_name ]]</li>
<li>Doctor: Dr. [[ doctor_name ]]</li>
<li>Scheduled Date: [[ appointment_date ]]</li>
<li>Scheduled Time: [[ appointment_time ]]</li>
</ul>
<p>Please ensure the records are updated accordingly.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Cancelled',
            'sms_template_detail' => '<p>The appointment with [[ user_name ]] has been cancelled. ID: #[[ appointment_id ]].</p>',
            'sms_subject' => 'Appointment Cancelled',
            'whatsapp_template_detail' => '<p>The appointment with [[ user_name ]] has been cancelled. ID: #[[ appointment_id ]].</p>',
            'whatsapp_subject' => 'Appointment Cancellation Update',
        ]);




        $template = NotificationTemplate::create([
            'type' => 'reschedule_appointment',
            'name' => 'reschedule_appointment',
            'label' => 'Appointment Rescheduled',
            'status' => 1,
            'to' => '["user","admin", "vendor", "doctor","receptionist"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your appointment has been successfully rescheduled.',
            'status' => 1,
            'subject' => 'Appointment Rescheduled',
            'user_type' => 'user',
            'template_detail' => '<p>Your appointment has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'mail_template_detail' => '<div class="flex-shrink-0 flex flex-col relative items-end">
<div>
<div class="pt-0">
<div class="gizmo-bot-avatar flex h-8 w-8 items-center justify-center overflow-hidden rounded-full">&nbsp;</div>
</div>
</div>
</div>
<div class="group/conversation-turn relative flex w-full min-w-0 flex-col agent-turn">
<div class="flex-col gap-1 md:gap-3">
<div class="flex max-w-full flex-col flex-grow">
<div class="min-h-8 text-message flex w-full flex-col items-end gap-2 whitespace-normal break-words [.text-message+&amp;]:mt-5" dir="auto" data-message-author-role="assistant" data-message-id="15fc0f5c-b6a5-4306-b5b1-2e5e20fce903" data-message-model-slug="gpt-4o">
<div class="flex w-full flex-col gap-1 empty:hidden first:pt-[3px]">
<div class="markdown prose w-full break-words dark:prose-invert light">
<p>Dear [[ user_name ]],</p>
<p>We regret to inform you that your appointment originally scheduled for [[ appointment_date ]] at [[ appointment_time ]] has been rescheduled.</p>
<p>Your new appointment details are as follows:</p>
<ul>
<li>New Date: [[ appointment_date ]]</li>
<li>New Time: [[ appointment_time ]]</li>
<li>Doctor: Dr. [[ doctor_name ]]</li>
<li>Services: [[ appointment_services_names ]]</li>
</ul>
<p>If you have any questions or need further assistance, please get in touch with our support team at [[ company_contact_info ]]. We sincerely apologize for any inconvenience this may cause and appreciate your understanding.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
</div>
</div>
</div>
</div>
<div class="mb-2 flex gap-3 empty:hidden -ml-2">
<div class="items-center justify-start rounded-xl p-1 flex">
<div class="flex items-center">&nbsp;</div>
</div>
</div>
</div>
</div>',
            'mail_subject' => 'Your Appointment Has Been Rescheduled',
            'sms_template_detail' => '<p>Your appointment has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'sms_subject' => 'Appointment Rescheduled',
            'whatsapp_template_detail' => '<p>Your appointment has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'whatsapp_subject' => 'Appointment Rescheduled',
        ]);


        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'An appointment has been successfully rescheduled.',
            'status' => 1,
            'subject' => 'Appointment Rescheduled',
            'user_type' => 'admin',
            'template_detail' => '<p>The appointment with ID #[[ appointment_id ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'mail_template_detail' => '<p>Dear [[ admin_name ]],</p>
<p>The appointment with ID #[[ appointment_id ]] has been rescheduled.</p>
<p>Updated Appointment Details:</p>
<ul>
<li>Patient: [[ user_name ]]</li>
<li>New Date: [[ appointment_date ]]</li>
<li>New Time: [[ appointment_time ]]</li>
<li>Doctor: Dr. [[ doctor_name ]]</li>
<li>Services: [[ appointment_services_names ]]</li>
</ul>
<p>Please ensure that the updated details are reflected in the system.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Appointment Rescheduled',
            'sms_template_detail' => '<p>The appointment with ID #[[ appointment_id ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'sms_subject' => 'Appointment Rescheduled',
            'whatsapp_template_detail' => '<p>The appointment with ID #[[ appointment_id ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'whatsapp_subject' => 'Appointment Rescheduled',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'An appointment has been successfully rescheduled.',
            'status' => 1,
            'subject' => 'Appointment Rescheduled',
            'user_type' => 'vendor',
            'template_detail' => '<p>The appointment with [[ user_name ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'mail_template_detail' => '<p>Dear [[ vendor_name ]],</p>
<p>We regret to inform you that the appointment with [[ user_name ]] has been rescheduled.</p>
<p>Updated Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>New Date: [[ appointment_date ]]</li>
<li>New Time: [[ appointment_time ]]</li>
</ul>
<p>Please make the necessary adjustments to your schedule.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Rescheduled',
            'sms_template_detail' => '<p>The appointment with [[ user_name ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'sms_subject' => 'Appointment Rescheduled',
            'whatsapp_template_detail' => '<p>The appointment with [[ user_name ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'whatsapp_subject' => 'Appointment Rescheduled',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'The appointment for your client has been rescheduled.',
            'status' => 1,
            'subject' => 'Appointment Rescheduled',
            'user_type' => 'doctor',
            'template_detail' => '<p>The appointment with [[ user_name ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'mail_template_detail' => '<p>Dear Dr. [[ doctor_name ]],</p>
<p>The appointment with [[ user_name ]] has been rescheduled.</p>
<p>Updated Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>New Date: [[ appointment_date ]]</li>
<li>New Time: [[ appointment_time ]]</li>
<li>Services: [[ appointment_services_names ]]</li>
</ul>
<p>Please update your schedule accordingly.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Client Appointment Rescheduled',
            'sms_template_detail' => '<p>The appointment with [[ user_name ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'sms_subject' => 'Appointment Rescheduled',
            'whatsapp_template_detail' => '<p>The appointment with [[ user_name ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'whatsapp_subject' => 'Appointment Rescheduled',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'An appointment with your patient has been successfully rescheduled.',
            'status' => 1,
            'subject' => 'Appointment Rescheduled',
            'user_type' => 'receptionist',
            'template_detail' => '<p>The appointment with [[ user_name ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'mail_template_detail' => '<p>Dear [[ vendor_name ]],</p>
<p>We regret to inform you that the appointment with [[ user_name ]] has been rescheduled.</p>
<p>Updated Appointment Details:</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>New Date: [[ appointment_date ]]</li>
<li>New Time: [[ appointment_time ]]</li>
</ul>
<p>Please make the necessary adjustments to your schedule.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Rescheduled',
            'sms_template_detail' => '<p>The appointment with [[ user_name ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'sms_subject' => 'Appointment Rescheduled',
            'whatsapp_template_detail' => '<p>The appointment with [[ user_name ]] has been rescheduled to [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'whatsapp_subject' => 'Appointment Rescheduled',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'quick_appointment',
            'name' => 'quick_appointment',
            'label' => 'Quick Booking',
            'status' => 1,
            'to' => '["admin", "vendor", "doctor","receptionist","user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your appointment has been successfully booked. Please find the details below.',
            'status' => 1,
            'subject' => 'Appointment Confirmed',
            'user_type' => 'user',
            'template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'mail_template_detail' => '<p>Dear [[user_name]],</p>
<p>We are pleased to inform you that your appointment has been successfully booked. Here are your appointment details:</p>
<ul>
<li>Date: [[appointment_date]]</li>
<li>Time: [[appointment_time]]</li>
<li>Duration: [[appointment_duration]]</li>
<li>Services: [[appointment_services_names]]</li>
<li>Doctor: Dr. [[doctor_name]]</li>
<li>Venue: [[venue_address]]</li>
</ul>
<p>Please arrive a few minutes early. If you need to reschedule or cancel, kindly notify us at least 24 hours in advance.</p>
<p>Thank you for choosing [[company_name]].<br>We look forward to serving you.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Confirmed',
            'sms_template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'sms_subject' => 'Appointment Confirmed',
            'whatsapp_template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'whatsapp_subject' => 'Appointment Confirmed',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your appointment has been successfully booked. Please find the details below.',
            'status' => 1,
            'subject' => 'Appointment Confirmed',
            'user_type' => 'admin',
            'template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'mail_template_detail' => '<p>Dear [[user_name]],</p>
<p>We are pleased to inform you that your appointment has been successfully booked. Here are your appointment details:</p>
<ul>
<li>Date: [[appointment_date]]</li>
<li>Time: [[appointment_time]]</li>
<li>Duration: [[appointment_duration]]</li>
<li>Services: [[appointment_services_names]]</li>
<li>Doctor: Dr. [[doctor_name]]</li>
<li>Venue: [[venue_address]]</li>
</ul>
<p>Please arrive a few minutes early. If you need to reschedule or cancel, kindly notify us at least 24 hours in advance.</p>
<p>Thank you for choosing [[company_name]].<br>We look forward to serving you.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Confirmed',
            'sms_template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'sms_subject' => 'Appointment Confirmed',
            'whatsapp_template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'whatsapp_subject' => 'Appointment Confirmed',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your appointment has been successfully booked. Please find the details below.',
            'status' => 1,
            'subject' => 'Appointment Confirmed',
            'user_type' => 'admin',
            'template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'mail_template_detail' => '<p>Dear [[user_name]],</p>
<p>We are pleased to inform you that your appointment has been successfully booked. Here are your appointment details:</p>
<ul>
<li>Date: [[appointment_date]]</li>
<li>Time: [[appointment_time]]</li>
<li>Duration: [[appointment_duration]]</li>
<li>Services: [[appointment_services_names]]</li>
<li>Doctor: Dr. [[doctor_name]]</li>
<li>Venue: [[venue_address]]</li>
</ul>
<p>Please arrive a few minutes early. If you need to reschedule or cancel, kindly notify us at least 24 hours in advance.</p>
<p>Thank you for choosing [[company_name]].<br>We look forward to serving you.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Confirmed',
            'sms_template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'sms_subject' => 'Appointment Confirmed',
            'whatsapp_template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'whatsapp_subject' => 'Appointment Confirmed',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'An appointment has been successfully rescheduled.',
            'status' => 1,
            'subject' => 'Appointment Confirmed',
            'user_type' => 'vendor',
            'template_detail' => '<p>Appointment confirmed for [[ user_name ]] on [[ appointment_date ]] at [[ appointment_time ]]. Please be prepared.</p>',
            'mail_template_detail' => '<p>Dear [[ vendor_name ]],</p>
<p>The appointment with [[ user_name ]] has been confirmed.</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
<li>Services: [[ appointment_services_names ]]</li>
<li>Venue: [[ venue_address ]]</li>
</ul>
<p>Please ensure all preparations are in order.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Appointment Confirmed',
            'sms_template_detail' => '<p>Appointment confirmed for [[ user_name ]] on [[ appointment_date ]] at [[ appointment_time ]]. Please be prepared.</p>',
            'sms_subject' => 'Appointment Confirmed',
            'whatsapp_template_detail' => '<p>Appointment confirmed for [[ user_name ]] on [[ appointment_date ]] at [[ appointment_time ]]. Please be prepared.</p>',
            'whatsapp_subject' => 'Appointment Confirmed',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'The appointment for your client has been rescheduled.',
            'status' => 1,
            'subject' => 'Appointment Confirmed',
            'user_type' => 'doctor',
            'mail_template_detail' => '<p>Dear Dr. [[ doctor_name ]],</p>
<p>You have a confirmed appointment with [[ user_name ]].</p>
<ul>
<li>Appointment ID: #[[ appointment_id ]]</li>
<li>Date: [[ appointment_date ]]</li>
<li>Time: [[ appointment_time ]]</li>
<li>Services: [[ appointment_services_names ]]</li>
<li>Duration: [[ appointment_duration ]]</li>
<li>Venue: [[ venue_address ]]</li>
</ul>
<p>Please prepare accordingly. Let us know if you need any additional information.</p>
<p>Best regards,<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Confirmed',
            'sms_template_detail' => '<p>You have an appointment with [[ user_name ]] on [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'sms_subject' => 'Appointment Confirmed',
            'whatsapp_template_detail' => '<p>You have an appointment with [[ user_name ]] on [[ appointment_date ]] at [[ appointment_time ]].</p>',
            'whatsapp_subject' => 'Appointment Confirmed',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'An appointment with your patient has been successfully rescheduled.',
            'status' => 1,
            'subject' => 'Appointment Confirmed',
            'user_type' => 'receptionist',
            'template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'mail_template_detail' => '<p>Dear [[user_name]],</p>
<p>We are pleased to inform you that your appointment has been successfully booked. Here are your appointment details:</p>
<ul>
<li>Date: [[appointment_date]]</li>
<li>Time: [[appointment_time]]</li>
<li>Duration: [[appointment_duration]]</li>
<li>Services: [[appointment_services_names]]</li>
<li>Doctor: Dr. [[doctor_name]]</li>
<li>Venue: [[venue_address]]</li>
</ul>
<p>Please arrive a few minutes early. If you need to reschedule or cancel, kindly notify us at least 24 hours in advance.</p>
<p>Thank you for choosing [[company_name]].<br>We look forward to serving you.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Confirmed',
            'sms_template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'sms_subject' => 'Appointment Confirmed',
            'whatsapp_template_detail' => '<p>Your appointment is confirmed for [[ appointment_date ]] at [[ appointment_time ]]. Doctor: Dr. [[ doctor_name ]].</p>',
            'whatsapp_subject' => 'Appointment Confirmed',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'change_password',
            'name' => 'change_password',
            'label' => 'Change Password',
            'to' => '["admin", "vendor", "doctor","receptionist","user"]',
            'status' => 1,
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'subject' => 'Password Updated',
            'user_type' => 'user',
            'template_detail' => '<p>Your password was changed successfully. Secure your account if you did not authorize this.</p>',
            'mail_template_detail' => '<p>Dear [[ user_name ]], <br><br>We wanted to inform you that your password has been successfully changed.<br><br>If you did not initiate this change, please secure your account immediately. <br><br>To protect your account: - Verify your identity and reset your password. <br>- Ensure you use a strong, unique password. <br>- Update passwords on any other accounts using similar credentials. <br>If you have any concerns, please contact our customer support team. <br><br>Thank you, <br>[[ company_name ]] <br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Your Appointment Has Been Rescheduled',
            'sms_template_detail' => '<p>Your password was changed successfully. Secure your account if you did not authorize this.</p>',
            'sms_subject' => 'Password Updated',
            'whatsapp_template_detail' => '<p>Your password was changed successfully. Secure your account if you did not authorize this.</p>',
            'whatsapp_subject' => 'Password Updated',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'subject' => 'Password Updated',
            'user_type' => 'admin',
            'template_detail' => '<p>Your password was changed successfully. Secure your account if you did not authorize this.</p>',
            'mail_template_detail' => '<p>Dear [[receptionist_name]],</p>
<p>We would like to confirm that your account password has been updated. If this action was not taken by you, please reset your password as soon as possible.</p>
<p>For support, please reach out to our team.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>',
            'mail_subject' => 'Password Change Confirmed',
            'sms_template_detail' => '<p>Your password has been updated. Reset it if you have not initiated this change.</p>',
            'sms_subject' => 'Password Changed',
            'whatsapp_template_detail' => '<p>Your password has been updated. Reset it if you have not initiated this change.</p>',
            'whatsapp_subject' => 'Password Changed',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'subject' => 'Password Updated',
            'user_type' => 'vendor',
            'template_detail' => '<p>The password for your account has been successfully updated. Contact support if needed.</p>',
            'mail_template_detail' => '<p>Dear [[vendor_name]],</p>
<p>Your password has been successfully changed. If you did not initiate this change, please secure your account immediately by resetting your password.</p>
<p>If you have any concerns, feel free to contact support.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Password Changed',
            'sms_template_detail' => '<p>The password for your account has been successfully updated. Contact support if needed.</p>',
            'sms_subject' => 'Password Changed',
            'whatsapp_template_detail' => '<p>The password for your account has been successfully updated. Contact support if needed.</p>',
            'whatsapp_subject' => 'Password Changed',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'subject' => 'Password Updated',
            'user_type' => 'doctor',
            'template_detail' => '<p>Your account password has been changed. If this wasn&rsquo;t you, please reset it immediately.</p>',
            'mail_template_detail' => '<p>Dear Dr. [[doctor_name]],</p>
<p>Your account password has been successfully changed. If you did not make this change, please secure your account by resetting your password immediately.</p>
<p>For any assistance, please contact our support team.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Password Updated',
            'sms_template_detail' => '<p>Your account password has been changed. If this wasn&rsquo;t you, please reset it immediately.</p>',
            'sms_subject' => 'Password Updated',
            'whatsapp_template_detail' => '<p>Your account password has been changed. If this wasn&rsquo;t you, please reset it immediately.</p>',
            'whatsapp_subject' => 'Password Updated',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'subject' => 'Password Updated',
            'user_type' => 'receptionist',
            'template_detail' => '<p>Your account password has been changed. If this wasn&rsquo;t you, please reset it immediately.</p>',
            'mail_template_detail' => '<p>Dear [[receptionist_name]],</p>
<p>We would like to confirm that your account password has been updated. If this action was not taken by you, please reset your password as soon as possible.</p>
<p>For support, please reach out to our team.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Password Change',
            'sms_template_detail' => '<p>Your password has been updated. Reset it if you did not initiate this change.</p>',
            'sms_subject' => 'Password Updated',
            'whatsapp_template_detail' => '<p>Your password has been updated. Reset it if you did not initiate this change.</p>',
            'whatsapp_subject' => 'Password Updated',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'forget_email_password',
            'name' => 'forget_email_password',
            'label' => 'Forget Email/Password',
            'to' => '["admin", "vendor", "doctor","receptionist","user"]',
            'status' => 1,
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'user_type' => 'user',
            'subject' => 'Reset Password Instructions Sent',
            'template_detail' => '<p>We&rsquo;ve sent you a password reset link via email. Please check your inbox to proceed.</p>',
            'mail_template_detail' => '<p>Dear [[user_name]],</p>
<p>A password reset request has been initiated for your account. To reset your password:</p>
<ol>
<li>Click on the link below.</li>
<li>Enter your email address and follow the instructions provided.</li>
</ol>
<p><strong>Reset Password Link</strong>: [[link]]</p>
<p>If this was not requested by you, please contact our support team for assistance.</p>
<p>Thank you,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Your Appointment Has Been Rescheduled',
            'sms_template_detail' => '<p>We&rsquo;ve sent you a password reset link via email. Please check your inbox to proceed.</p>',
            'sms_subject' => 'Reset Password Instructions Sent',
            'whatsapp_template_detail' => '<p>We&rsquo;ve sent you a password reset link via email. Please check your inbox to proceed.</p>',
            'whatsapp_subject' => 'Password Reset Instructions',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'user_type' => 'admin',
            'subject' => 'Admin Password Reset Requested',
            'template_detail' => '<p>Your admin password reset request has been initiated. Check your email for the reset link.</p>',
            'mail_template_detail' => '<p>Dear [[admin_name]],</p>
<p>A request has been made to reset your admin account password. Please use the link below to reset your password:</p>
<p><strong>Reset Password Link</strong>: [[link]]</p>
<p>If you did not initiate this request, please contact our support team immediately.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Appointment Rescheduling Notification',
            'sms_template_detail' => '<p>Your admin password reset request has been initiated. Check your email for the reset link.</p>',
            'sms_subject' => 'Admin Password Reset Requested',
            'whatsapp_template_detail' => '<p>Your admin password reset request has been initiated. Check your email for the reset link.</p>',
            'whatsapp_subject' => 'Admin Password Reset Requested',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'user_type' => 'vendor',
            'subject' => 'Reset Your Password',
            'template_detail' => '<p>A password reset link has been sent to your registered email. Click the link to reset your password.</p>',
            'mail_template_detail' => '<p>Dear [[vendor_name]],</p>
<p>We have received a request to reset your account password. Please click the link below to proceed:</p>
<p><strong>Reset Password Link</strong>: [[link]]</p>
<p>If you did not request this change, please contact our support team promptly.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Password Reset Request for Your Account',
            'sms_template_detail' => '<p>A password reset link has been sent to your registered email. Click the link to reset your password.</p>',
            'sms_subject' => 'Reset Your Password',
            'whatsapp_template_detail' => '<p>A password reset link has been sent to your registered email. Click the link to reset your password.</p>',
            'whatsapp_subject' => 'Reset Your Password',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'user_type' => 'doctor',
            'subject' => 'Reset Your Password',
            'template_detail' => '<p>A password reset link has been sent to your registered email. Click the link to reset your password.</p>',
            'mail_template_detail' => '<p>Dear Dr. [[doctor_name]],</p>
<p>We received a request to reset your password. To reset your password:</p>
<ol>
<li>Click on the link provided below.</li>
<li>Follow the instructions to set a new password.</li>
</ol>
<p><strong>Reset Password Link</strong>: [[link]]</p>
<p>If this wasn&rsquo;t initiated by you, please notify support at once.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Password Reset Link Sent',
            'sms_template_detail' => '<p>A password reset link has been sent to your email. Please follow the instructions to reset your password.</p>',
            'sms_subject' => 'Password Reset Link Sent',
            'whatsapp_template_detail' => '<p>A password reset link has been sent to your email. Please follow the instructions to reset your password.</p>',
            'whatsapp_subject' => 'Password Reset Link Sent',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'user_type' => 'receptionist',
            'subject' => 'Password Reset Assistance',
            'template_detail' => '<p>We have sent a password reset link to your email. Please use it to set a new password.</p>',
            'mail_template_detail' => '<p>Dear [[receptionist_name]],</p>
<p>A password reset request was made for your account. Please use the link below to reset your password:</p>
<p><strong>Reset Password</strong>: [[link]]</p>
<p>If you didn&rsquo;t make this request, please let us know immediately for security purposes.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>
<p>&nbsp;</p>',
            'mail_subject' => 'Password Reset Assistance',
            'sms_template_detail' => '<p>We have sent a password reset link to your email. Please use it to set a new password.</p>',
            'sms_subject' => 'Password Reset Assistance',
            'whatsapp_template_detail' => '<p>We have sent a password reset link to your email. Please use it to set a new password.</p>',
            'whatsapp_subject' => 'Password Reset Assistance',
        ]);

        // $template = NotificationTemplate::create([
        //     'type' => 'order_placed',
        //     'name' => 'order_placed',
        //     'label' => 'Order Placed',
        //     'status' => 1,
        //     'to' => '["user","admin"]',
        // ]);
        // $template->defaultNotificationTemplateMap()->create([
        //     'language' => 'en',
        //     'notification_link' => '',
        //     'notification_message' => 'Thank you for choosing Us for your recent order. We are delighted to confirm that your order has been successfully placed.!',
        //     'status' => 1,
        //     'subject' => 'Order Placed!',
        //     'template_detail' => '<p>Thank you for choosing Us for your recent order. We are delighted to confirm that your order has been successfully placed.!</p>',
        // ]);

        // $template = NotificationTemplate::create([
        //     'type' => 'order_proccessing',
        //     'name' => 'order_proccessing',
        //     'label' => 'Order Processing',
        //     'status' => 1,
        //     'to' => '["user","admin"]',
        // ]);
        // $template->defaultNotificationTemplateMap()->create([
        //     'language' => 'en',
        //     'notification_link' => '',
        //     'notification_message' => "We're excited to let you know that your order is now being prepared and will soon be on its way to satisfy your taste buds!",
        //     'status' => 1,
        //     'subject' => 'Order Processing!',
        //     'template_detail' => "<p>We're excited to let you know that your order is now being prepared and will soon be on its way to satisfy your taste buds!</p>",
        // ]);

        // $template = NotificationTemplate::create([
        //     'type' => 'order_delivered',
        //     'name' => 'order_delivered',
        //     'label' => 'Order Delivered',
        //     'status' => 1,
        //     'to' => '["user","admin"]',
        // ]);
        // $template->defaultNotificationTemplateMap()->create([
        //     'language' => 'en',
        //     'notification_link' => '',
        //     'notification_message' => "We're delighted to inform you that your order has been successfully delivered to your doorstep.",
        //     'status' => 1,
        //     'subject' => 'Order Delivered!',
        //     'template_detail' => "<p>We're delighted to inform you that your order has been successfully delivered to your doorstep.</p>",
        // ]);

        // $template = NotificationTemplate::create([
        //     'type' => 'order_cancelled',
        //     'name' => 'order_cancelled',
        //     'label' => 'Oreder Cancelled',
        //     'status' => 1,
        //     'to' => '["user","admin"]',
        // ]);
        // $template->defaultNotificationTemplateMap()->create([
        //     'language' => 'en',
        //     'notification_link' => '',
        //     'notification_message' => 'We regret to inform you that your recent order has been cancelled.',
        //     'status' => 1,
        //     'subject' => 'Order Cancelled!',
        //     'template_detail' => '<p>We regret to inform you that your recent order has been cancelled.</p>',
        // ]);


        $template = NotificationTemplate::create([
            'type' => 'new_request_service',
            'name' => 'new_request_service',
            'label' => 'New Service Request Found!',
            'status' => 1,
            'to' => '["admin","vendor"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'New Service Request Found!',
            'status' => 1,
            'subject' => 'New Service Request Found!',
            'user_type' => 'admin',
            'template_detail' => '<p>A new service request has been submitted. Check your dashboard for more details and take action.</p>',
            'mail_template_detail' => '<p>Dear Admin,</p>
<p>A new service request has been submitted. Please check dashboard:</p>
<p>Log in to your dashboard to view more details and take action.</p>
<p>Best regards,<br>[[company_name]]<br>[[company_contact_info]]</p>',
            'mail_subject' => 'New Service Request Submitted',
            'sms_template_detail' => '<p>A new service request has been submitted. Check your dashboard for more details and take action.</p>',
            'sms_subject' => 'New Service Request Submitted',
            'whatsapp_template_detail' => '<p>A new service request has been submitted. Check your dashboard for more details and take action.</p>',
            'whatsapp_subject' => 'New Service Request Submitted',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'New Service Request Found!',
            'status' => 1,
            'subject' => 'New Service Request Found!',
            'user_type' => 'vendor',
            'template_detail' => '<p>A new service request is available for review. Please log in to your dashboard for more details.</p>',
            'mail_template_detail' => '<p data-pm-slice="1 1 []">Dear [[vendor_name]],</p>
<p data-pm-slice="1 1 []">&nbsp;</p>
<p>A new service request has been submitted and is available for you to review.</p>
<p>Please log in to your dashboard to review the request and take the necessary action.</p>
<p>&nbsp;</p>
<p>Best regards,</p>
<p>[[company_name]]</p>
<p>[[company_contact_info]</p>',
            'mail_subject' => 'New Service Request Received',
            'sms_template_detail' => '<p>A new service request is available for review. Please log in to your dashboard for more details.</p>',
            'sms_subject' => 'New Service Request Received',
            'whatsapp_template_detail' => '<p>A new service request is available for review. Please log in to your dashboard for more details.</p>',
            'whatsapp_subject' => 'New Service Request Received',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'accept_request_service',
            'name' => 'accept_request_service',
            'label' => 'Service Request Acceptance',
            'status' => 1,
            'to' => '["vendor"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your Service Request Has Been Accepted!',
            'status' => 1,
            'subject' => 'Your Service Request Has Been Accepted!',
            'user_type' => 'vendor',
            'template_detail' => '<p>Your service request has been successfully accepted. Log in to your dashboard for more details.</p>',
            'mail_template_detail' => '<p>Dear [[ vendor_name ]],</p>
<p>We are pleased to inform you that your service request has been accepted. Here are the details:</p>
<p>Request ID: [[ request_id ]]</p>
<p>Request Date: [[ request_date ]]</p>
<p>Service Requested: [[ service_requested ]]</p>
<p>Requested By: [[ requestor_name ]]</p>
<p>&nbsp;</p>
<p>Our team will be in touch with you shortly to provide further instructions and schedule the service.</p>
<p>&nbsp;</p>
<p>Thank you for your prompt attention to this request.</p>
<p>&nbsp;</p>
<p>Best regards,</p>
<p>&nbsp;</p>
<p>[[ logged_in_user_fullname ]]<br>[[ logged_in_user_role ]]<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Your Service Request Is Accepted!',
            'sms_template_detail' => '<p>Your service request has been successfully accepted. Log in to your dashboard for more details.</p>',
            'sms_subject' => 'Your Service Request Is Accepted!',
            'whatsapp_template_detail' => '<p>Your service request has been successfully accepted. Log in to your dashboard for more details.</p>',
            'whatsapp_subject' => 'Your Service Request Is Accepted!',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'reject_request_service',
            'name' => 'reject_request_service',
            'label' => 'Service Request Rejected',
            'status' => 1,
            'to' => '["vendor"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your Service Request Has Been Rejected!',
            'status' => 1,
            'subject' => 'Your Service Request Has Been Rejected!',
            'user_type' => 'vendor',
            'template_detail' => '<p>Your service request has been rejected. Please check the details in your dashboard for more information.</p>',
            'mail_template_detail' => '<p>Dear [[ vendor_name ]],</p>
<p>We regret to inform you that your recent service request has been rejected. Below are the details of your request:</p>
<p>&nbsp;</p>
<p>Request ID: [[ request_id ]]</p>
<p>Request Date: [[ request_date ]]</p>
<p>Service Requested: [[ service_requested ]]</p>
<p>Reason for Rejection: [[ rejection_reason ]]</p>
<p>We appreciate your understanding and encourage you to submit a new request if you have any other needs or if you believe there has been an error.</p>
<p>If you have any questions or require further clarification, please do not hesitate to contact us.</p>
<p>Best regards,</p>
<p>[[ logged_in_user_fullname ]]<br>[[ logged_in_user_role ]]<br>[[ company_name ]]<br>[[ company_contact_info ]]</p>',
            'mail_subject' => 'Your Service Request Has Been Rejected!',
            'sms_template_detail' => '<p>Your service request has been rejected. Please check the details in your dashboard for more information.</p>',
            'sms_subject' => 'Your Service Request Has Been Rejected!',
            'whatsapp_template_detail' => '<p>Your service request has been rejected. Please check the details in your dashboard for more information.</p>',
            'whatsapp_subject' => 'Your Service Request Has Been Rejected!',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'wallet_refund',
            'name' => 'wallet_refund',
            'label' => 'Wallet Refund',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Wallet Refund',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Wallet Refund',
            'mail_subject' => 'Wallet Refund',
            'sms_subject' => 'Wallet Refund',
            'whatsapp_subject' => 'Wallet Refund',
            'template_detail' => '<p>Refund of Appointment has been processed to the patient.</p>',
            'mail_template_detail' => '<p>Refund of Appointment has been processed to the patient.</p>',
            'sms_template_detail' => '<p>Refund of Appointment has been processed to the patient.</p>',
            'whatsapp_template_detail' => '<p>Refund of Appointment has been processed to the patient.</p>',
        ]);

    }
};
