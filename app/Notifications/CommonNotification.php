<?php

namespace App\Notifications;

use App\Broadcasting\CustomWebhook;
use App\Broadcasting\OneSingleChannel;
use App\Mail\MailMailableSend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\NotificationTemplate\Models\NotificationTemplate;
use Modules\NotificationTemplate\Models\NotificationTemplateContentMapping;
use Spatie\WebhookServer\WebhookCall;
use App\Broadcasting\FcmChannel;
use Benwilkins\FCM\Message;
use Google\Client as Google_Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use Twilio\Rest\Client;
use App\Models\User;
class CommonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $type;

    public $data;

    public $subject;

    public $notification;

    public $notification_message;

    public $notification_link;

    public $appData;

    public $custom_webhook;

    /**
     * Create a new notification instance.
     */
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
// dd($data);
        $userType = $data['user_type'];
        $notifications = NotificationTemplate::where('type', $this->type)
            ->with('defaultNotificationTemplateMap')
            ->first();

        $notify_data = NotificationTemplateContentMapping::where('template_id', $notifications->id)->get();
        $templateData = $notify_data->where('user_type', $userType)->first();
        $this->template_data = $templateData;

        if (!$templateData) {
            throw new \Exception("Template data for user type '{$userType}' not found.");
        }
        $templatesubject = $templateData->subject ?? null;
        $templateDetail = $templateData->template_detail ?? null;
        $mailtemplateDetail = $templateData->mail_template_detail ?? null;
        $templatemailsubject = $templateData->mail_subject ?? null;


        foreach ($this->data as $key => $value) {
            $replacement = is_scalar($value) ? (string)$value : ''; 
            $templateDetail = str_replace('[[ ' . $key . ' ]]', $replacement, $templateDetail);
            $templatesubject = str_replace('[[ ' . $key . ' ]]', $replacement, $templatesubject);
            $mailtemplateDetail = str_replace('[[ ' . $key . ' ]]', $replacement, $mailtemplateDetail);
            $templatemailsubject = str_replace('[[ ' . $key . ' ]]', $replacement, $templatemailsubject);
        }

        $this->data['type'] = $templateData->subject ?? 'None';
        $this->data['message'] = $templateDetail ?? __('messages.default_notification_body');
        $this->appData = $notifications->channels;
        $this->notification = $templateData;
        $this->subject = $templatesubject != '' ?  $templatesubject : 'None';
        $this->notification_message = $templateData->notification_message != '' ? $templateData->notification_message : __('messages.default_notification_body');
        $this->data['template'] =  $templateDetail != '' ? $templateDetail : __('messages.default_notification_body');
        $this->data['mailTemplate'] =  $mailtemplateDetail != '' ? $mailtemplateDetail : __('messages.default_notification_body');
        $this->data['mailSubject'] =  $templatemailsubject != '' ? $templatemailsubject : 'None';
        
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $notificationData = $this->data;
        $templateData = $this->template_data;

        $notificationSettings = $this->appData;
        $notification_settings = [];
        $notification_access = isset($notificationSettings[$this->type]) ? $notificationSettings[$this->type] : [];
        if (isset($notificationSettings)) {
            foreach ($notificationSettings as $key => $notification) {
                if ($notification) {
                    switch ($key) {

                        case 'PUSH_NOTIFICATION':
                            array_push($notification_settings, FcmChannel::class);

                            break;

                        case 'IS_CUSTOM_WEBHOOK':
                            array_push($notification_settings, CustomWebhook::class);

                            break;

                        case 'IS_MAIL':
                            $templateDetail = $templateData->mail_template_detail ?? __('messages.default_notification_body');

                            if (!empty($notificationData)) {
                                foreach ($notificationData as $key => $value) {
                                    if (is_array($value)) {
                                        $value = implode(', ', $value); // convert array to comma-separated string
                                    } elseif (is_object($value)) {
                                        // If object can be stringified
                                        $value = method_exists($value, '__toString') ? (string) $value : json_encode($value);
                                    } elseif ($value === null) {
                                        $value = '';
                                    }

                                    $templateDetail = str_replace('[[ ' . $key . ' ]]', $value, $templateDetail);
                                }
                            }

                            $notificationData['type'] = $templateData->mail_subject ?? 'None';
                            $notificationData['message'] = $templateDetail ?? __('messages.default_notification_body');
                            array_push($notification_settings, 'mail');

                            break;
                        case 'IS_SMS':
                            $templateDetail = $templateData->sms_template_detail ?? null;
                            foreach ($notificationData as $key => $value) {
                                $templateDetail = str_replace('[[ ' . $key . ' ]]', $notificationData[$key], $templateDetail);
                            }

                            $notificationData['type'] = $templateData->sms_subject ?? 'None';
                            $notificationData['message'] = $templateDetail ?? __('messages.default_notification_body');

                            $this->sendSmsMessage($notificationData);

                            break;
                        case 'IS_WHATSAPP':
                            $templateDetail = $templateData->whatsapp_template_detail ?? null;
                            foreach ($notificationData as $key => $value) {
                                $templateDetail = str_replace('[[ ' . $key . ' ]]', $notificationData[$key], $templateDetail);
                            }

                            $notificationData['type'] = $templateData->whatsapp_subject ?? 'None';
                            $notificationData['message'] = $templateDetail ?? __('messages.default_notification_body');
                            
                            $this->sendWhatsAppMessage($notificationData);

                            break;
                    }
                }
            }
        }

        return array_merge($notification_settings, ['database']);
    }

    public function toOneSignal($notifiable)
    {
        $msg = $this->subject;
        if (!isset($msg) && $msg == '') {
            $msg = __('message.notification_body');
        }
        $type = 'booking';
        if (isset($this->data['type']) && $this->data['type'] !== '') {
            $type = $this->data['type'];
        }
        $heading = $this->subject;

        return onesingle([
            'app_id' => setting('onesignal_app_id'),
            'include_player_ids' => [$notifiable->player_id],
            'data' => [
                'type' => $this->subject,
                'additional_data' => $this->data,
            ],
            'headings' => [
                'en' => $heading,
            ],
            'contents' => [
                'en' => $msg,
            ],
        ]);
    }

    /**
     * Get mail notification
     *
     * @param  mixed  $notifiable
     * @return MailMailableSend
     */
    public function toMail($notifiable)
    {
        $email = '';

        if (isset($notifiable->email)) {
            $email = $notifiable->email;
        } else {
            $email = $notifiable->routes['mail'];
        }

        return (new MailMailableSend($this->notification, $this->data, $this->type))->to($email)
            ->bcc(isset($this->notification->bcc) ? json_decode($this->notification->bcc) : [])
            ->cc(isset($this->notification->cc) ? json_decode($this->notification->cc) : [])
            ->subject($this->data['mailSubject']);
    }

    public function toWebhook($notifiable)
    {
        $key = setting('custom_webhook_content_key');
        $url = setting('custom_webhook_url');
        $secrate_key = setting('app_name');
        $msg = 'Subject: ' . $this->subject . "\nDescription: " . strip_tags($this->notification_message) . "\n" . 'Link: ' . $this->notification_link;

        return WebhookCall::create()
            ->url($url)
            ->payload([$key => $msg])
            ->useSecret($secrate_key)
            ->dispatch();
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'subject' => $this->subject,
            'data' => $this->data,
        ];
    }

   
    public function toFcm($notifiable)
    {
        
        $msg = isset($this->data['notification_msg']) ? $this->data['notification_msg'] : '';
        if (!isset($msg) && $msg == '' ) {
            $msg =  $this->subject;
        }
        $type = 'booking';
        if (isset($this->data['type']) && $this->data['type'] !== '') {
            $type = $this->data['type'];
        }
        $heading = $this->subject;
        $additionalData = json_encode($this->data);

        return $this->fcm([
            "message" => [
                "topic" => 'user_' . $notifiable->id,
                "notification" => [
                    "title" => $heading,
                    "body" => $msg,
                ],
                "data" => [
                    "sound" => "default",
                    "story_id" => "story_12345",
                    "type" => $type,
                    "additional_data" => $additionalData,
                    "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                ],
                "android" => [
                    "priority" => "high",
                    "notification" => [
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                    ],
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "category" => "NEW_MESSAGE_CATEGORY",
                        ],
                    ],
                ],
            ],
        ]);
    }

    function fcm($fields)
    {
        $otherSetting = \App\Models\Setting::where('name','firebase_project_id')->first();
        $projectID = $otherSetting->val ?? null;
        $access_token = $this->getAccessToken();
        $headers = [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
        ];
        $ch = curl_init('https://fcm.googleapis.com/v1/projects/' . $projectID . '/messages:send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    
        $response = curl_exec($ch);
        Log::info($response);
        curl_close($ch);
    }
    function getAccessToken()
    {
        $directory = storage_path('app/data');
        $credentialsFiles = File::glob($directory . '/*.json');
        // if (empty($credentialsFiles)) {
        //     throw new Exception('No JSON credentials found in the specified directory.');
        // } 
        if (!empty($credentialsFiles)) {
            $client = new Google_Client();
            $client->setAuthConfig($credentialsFiles[0]);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        
            $token = $client->fetchAccessTokenWithAssertion();
        
            return $token['access_token'];
        }
    }


    function sendSmsMessage($data) {
        $settings = Setting::where('type', 'is_sms_integration')
            ->whereIn('name', ['sms_account_sid', 'sms_auth_token', 'mobile_number'])
            ->pluck('val', 'name');

        $user = User::where('id', $data['person_id'])->first();

        if (empty($settings['sms_account_sid']) || empty($settings['sms_auth_token']) || empty($settings['mobile_number'])) {
            return false;
        }

        if (empty($data['person_id']) || empty($data['message'])) {
            return false;
        }

        $sid = $settings['sms_account_sid'];
        $authToken = $settings['sms_auth_token'];
        $twilioPhoneNumber = $settings['mobile_number'];
        $recipientNumber = $user->mobile;
        $messageBody = strip_tags($data['message']);

        $client = new Client($sid, $authToken);

        try {
            $message = $client->messages->create(
                $recipientNumber,
                [
                    'from' => $twilioPhoneNumber,
                    'body' => $messageBody
                ]
            );

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    function sendWhatsAppMessage($data) {
        $settings = Setting::where('type', 'is_whatsapp_integration')
            ->whereIn('name', ['whatsapp_account_sid', 'whatsapp_auth_token', 'whatsapp_number'])
            ->pluck('val', 'name');

        $user = User::where('id', $data['person_id'])->first();
    
        if (empty($settings['whatsapp_account_sid']) || empty($settings['whatsapp_auth_token']) || empty($settings['whatsapp_number'])) {
            return false;  
        }
        
        if (empty($user) || empty($user->mobile) || empty($data['message'])) {
            return false;  
        }
    
        $sid = $settings['whatsapp_account_sid'];
        $authToken = $settings['whatsapp_auth_token'];
        $twilioWhatsAppNumber = 'whatsapp:' . $settings['whatsapp_number'];
        $recipientNumber = 'whatsapp:' . $user->mobile;
        $messageBody = strip_tags($data['message']);
        $client = new Client($sid, $authToken);
    
        try {
            $client->messages->create(
                $recipientNumber,
                [
                    'from' => $twilioWhatsAppNumber,
                    'body' => $messageBody
                ]
            );
            return true;  
        } catch (\Exception $e) {
            return false;  
        }
    }
    
}
