<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Setting;
use Modules\Currency\Models\Currency;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

function randomString($length)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $token = substr(str_shuffle($chars), 0, $length);
    return $token;
}

function onesingle($fields)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Authorization:Basic ' . setting('onesignal_rest_api_key'),
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);
}
function mail_footer($type)
{
    return [
        'notification_type' => $type,
        'logged_in_user_fullname' => auth()->user() ? auth()->user()->full_name ?? default_user_name() : '',
        'logged_in_user_role' => auth()->user() ? auth()->user()->getRoleNames()->first()->name ?? '-' : '',
        'company_name' => setting('app_name'),
        'company_contact_info' => implode('', [
            setting('helpline_number') . PHP_EOL,
            setting('inquriy_email'),
        ]),
    ];
}

function sendNotificationOnBookingUpdate($type, $requestservice)
{

    $data = mail_footer($type, $requestservice);

    // $address = [
    //     'address_line_1' => $booking->branch->address->address_line_1,
    //     'address_line_2' => $booking->branch->address->address_line_2,
    //     'city' => $booking->branch->address->city,
    //     'state' => $booking->branch->address->state,
    //     'country' => $booking->branch->address->country,
    //     'postal_code' => $booking->branch->address->postal_code,
    // ];
    $data['requestservice'] = $requestservice;

    App\Jobs\BulkNotification::dispatch($data);
}

function sendNotification($data)
{


    $mailable = \Modules\NotificationTemplate\Models\NotificationTemplate::where('type', $data['notification_type'])->with('defaultNotificationTemplateMap')->first();

    if ($mailable != null && $mailable->to != null) {
        $mails = json_decode($mailable->to);


        foreach ($mails as $key => $mailTo) {
            $data['type'] = $data['notification_type'];
            $data['logged_in_user_fullname'] = auth()->user() ? auth()->user()->full_name ?? default_user_name() : '';
            $data['logged_in_user_role'] = auth()->user() ? auth()->user()->getRoleNames()->first()->name ?? '-' : '';
            $data['company_name'] = setting('app_name');
            $data['company_contact_info'] = setting('helpline_number');

            $refundAmount = $data['wallet']['refund_amount'] ?? null;






            $appointment = isset($data['appointment']) ? $data['appointment'] : null;
            $wallet = isset($data['wallet']) ? $data['wallet'] : null;
            $requestservice = isset($data['requestservice']) ? $data['requestservice'] : null;
            $resendUserData = isset($data['resend_user_data']) ? $data['resend_user_data'] : null;
            $order = isset($data['order']) ? $data['order'] : null;
            $incidenceData = isset($data['incidence']) ? $data['incidence'] : null;

            if (isset($appointment) && $appointment != null) {
                $data['id'] = $appointment['id'];
                $data['description'] = $appointment['description'];
                $data['user_id'] = $appointment['user_id'];
                $data['user_name'] = $appointment['user_name'] ?? '';
                $data['patient_name'] = $appointment['user_name'] ?? '';
                $data['doctor_id'] = $appointment['doctor_id'];
                $data['doctor_name'] = $appointment['doctor_name'];
                $data['appointment_id'] = $appointment['id'];
                $data['appointment_date'] = $appointment['appointment_date'];
                $data['appointment_time'] = $appointment['appointment_time'];
                $data['appointmentduration'] = $appointment['appointment_duration'];
                $data['appointment_services_names'] = $appointment['appointment_services_names'];
                $data['notification_group'] = 'appointment';
                $data['clinic_name'] = $appointment['clinic_name'] ?? '';
                $data['clinic_id'] = $appointment['clinic_id'] ?? '';
                $data['vendor_id'] = $appointment['vendor_id'] ?? '';
                $data['receptionist_id'] = $appointment['receptionist_id'] ?? '';
                $data['receptionist_name'] = $appointment['receptionist_name'] ?? '';
                $data['venue_address'] = $appointment['clinic_address'] ?? '';
                $data['admin_name'] = $data['logged_in_user_fullname'] ?? '';
                $data['site_url'] = env('APP_URL');
                $data['updated_by_role'] = $appointment['updated_by_role'] ?? '';
                unset($data['appointment']);
            } elseif (isset($order) && $order != null) {
                $data['notification_group'] = 'shop';
                $data['id'] = $order['id'];
                $data['user_id'] = $order['user_id'];
                $data['order_code'] = $order['order_code'];
                $data['user_name'] = $order['user_name'];
                $data['order_date'] = $order['order_date'];
                $data['order_time'] = $order['order_time'];
                $data['site_url'] = env('APP_URL');
                unset($data['order']);
            } elseif (isset($requestservice) && $requestservice != null) {
                $data['id'] = $requestservice['id'];
                $data['name'] = $requestservice['name'];
                $data['description'] = $requestservice['description'];
                $data['type'] = $requestservice['type'];
                $data['vendor_id'] = $requestservice['vendor_id'];
                $data['notification_group'] = 'requestservice';
            } elseif (isset($wallet) && $wallet != null) {
                $data['id'] = $wallet['wallet']->id;
                $data['user_id'] = $wallet['wallet']->user_id;
                $data['notification_group'] = 'wallet';
                $data['refund_amount'] = $data['wallet']['refund_amount'] ?? null;
            } elseif (isset($resendUserData) && $resendUserData != null) {
                $data['user_id'] = $resendUserData['user_id'];
                $data['user_name'] = $resendUserData['user_name'];
                $data['user_email'] = $resendUserData['user_email'];
                $data['user_password'] = $resendUserData['password'];
                $data['notification_group'] = 'resend_user_credentials';

                unset($data['resend_user_data']);
            } elseif (isset($incidenceData) && $incidenceData != null) {
                $data['title'] = isset($incidenceData['title']) ? $incidenceData['title'] : '';
                $data['description'] = isset($incidenceData['description']) ? $incidenceData['description'] : '';
                $data['phone'] = isset($incidenceData['phone']) ? $incidenceData['phone'] : '';
                $data['email'] = isset($incidenceData['email']) ? $incidenceData['email'] : '';
                $data['user_name'] = isset($incidenceData['user_name']) ? $incidenceData['user_name'] : '';
            }

            switch ($mailTo) {
                case 'admin':

                    $admin = \App\Models\User::role('admin')->first();


                    $data['person_id'] = $admin->id;

                    if (isset($admin->email)) {
                        try {
                            $data['user_type'] = 'admin';
                            $admin->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                        } catch (\Exception $e) {
                            Log::error($e);
                        }
                    }

                    break;

                case 'doctor':
                    if (isset($data['doctor_id']) && $data['doctor_id'] != '') {
                        $data['user_type'] = 'doctor';
                        $doctor = \App\Models\User::find($data['doctor_id']);

                        $data['person_id'] = $doctor->id;
                        if (isset($doctor->email)) {
                            try {
                                if ($data['type'] == 'new_appointment') {

                                    $data['notification_msg'] = 'New Appointment #' . $data['id'] . ' Booked by ' . $data['user_name'] . '.';
                                } else if ($data['type'] == 'cancel_appointment') {

                                    $data['notification_msg'] = 'Appointment #' . $data['id'] . ' is cancelled by ' . $data['user_name'] . '.';
                                } else if ($data['type'] == 'reschedule_appointment') {

                                    $data['notification_msg'] = 'Appointment #' . $data['id'] . ' is rescheduled by ' . $data['user_name'] . '.';
                                }
                                $doctor->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                            } catch (\Exception $e) {
                                Log::error($e);
                            }
                        }
                    }

                    break;

                case 'vendor':
                    if (isset($data['vendor_id']) && $data['vendor_id'] != '') {
                        $data['user_type'] = 'vendor';
                        $vendor = \App\Models\User::find($data['vendor_id']);

                        $data['person_id'] = $vendor->id;
                        if (isset($vendor->email)) {
                            try {

                                if ($data['type'] == 'new_appointment') {

                                    $data['notification_msg'] = 'New Appointment #' . $data['id'] . ' Booked by ' . $data['user_name'] . '.';
                                } else if ($data['type'] == 'accept_appointment') {

                                    $data['notification_msg'] = 'Appointment #' . $data['id'] . ' is accepted by ' . $data['doctor_name'] . '.';
                                } else if ($data['type'] == 'cancel_appointment') {

                                    $data['notification_msg'] = 'Appointment #' . $data['id'] . ' is cancelled by ' . $data['user_name'] . '.';
                                } else if ($data['type'] == 'reschedule_appointment') {

                                    $data['notification_msg'] = 'Appointment #' . $data['id'] . ' is rescheduled by ' . $data['user_name'] . '.';
                                }

                                $vendor->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                            } catch (\Exception $e) {
                                Log::error($e);
                            }
                        }
                    }

                    break;
                case 'receptionist':
                    if (isset($data['receptionist_id']) && $data['receptionist_id'] != '') {
                        $data['user_type'] = 'receptionist';
                        $receptionist = \App\Models\User::where('user_type','receptionist')->find($data['receptionist_id']);

                        $data['person_id'] = $receptionist->id;
                        if (isset($receptionist->email)) {
                            try {

                                if ($data['type'] == 'new_appointment') {

                                    $data['notification_msg'] = 'New Appointment #' . $data['id'] . ' Booked by ' . $data['user_name'] . '.';
                                } else if ($data['type'] == 'accept_appointment') {

                                    $data['notification_msg'] = 'Appointment #' . $data['id'] . ' is accepted by ' . $data['doctor_name'] . '.';
                                } else if ($data['type'] == 'cancel_appointment') {

                                    $data['notification_msg'] = 'Appointment #' . $data['id'] . ' is cancelled by ' . $data['user_name'] . '.';
                                } else if ($data['type'] == 'reschedule_appointment') {

                                    $data['notification_msg'] = 'Appointment #' . $data['id'] . ' is rescheduled by ' . $data['user_name'] . '.';
                                }

                                $receptionist->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                            } catch (\Exception $e) {
                                Log::error($e);
                            }
                        }
                    }

                    break;

                case 'user':
                    if (isset($data['user_id'])) {
                        $data['user_type'] = 'user';
                        $user = \App\Models\User::find($data['user_id']);

                        $data['person_id'] = $user->id;
                        try {

                            if ($data['type'] == 'accept_appointment') {

                                $data['notification_msg'] = 'Your Appointment #' . $data['id'] . ' is accepted by ' . $data['doctor_name'] . '.';
                            } else if ($data['type'] == 'accept_appointment') {

                                $data['notification_msg'] = 'Your Appointment #' . $data['id'] . ' is accepted by ' . $data['doctor_name'] . '.';
                            } else if ($data['type'] == 'cancel_appointment') {
                                if ($data['updated_by_role'] == 'user') {
                                    $data['notification_msg'] = 'Appointment #' . $data['id'] . ' has been cancelled.';
                                } else {
                                    $data['notification_msg'] = 'Appointment #' . $data['id'] . ' has been cancelled by' . $data['updated_by_role'] . '.';
                                }
                            } else if ($data['type'] == 'reschedule_appointment') {

                                $data['notification_msg'] = 'Your Appointment #' . $data['id'] . ' is rescheduled by You.';
                            } else if ($data['type'] == 'wallet_refund') {


                                $data['notification_msg'] = 'Your wallet has been credited with a refund ' . \Currency::format($data['refund_amount']);
                            }

                            $user->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                        } catch (\Exception $e) {
                            Log::error($e);
                        }
                    }
                    break;
            }
        }
    }
}
function timeAgoInt($date)
{
    if ($date == null) {
        return '-';
    }
    $datetime = new \DateTime($date);
    $datetime->setTimezone(new \DateTimeZone(setting('default_time_zone') ?? 'UTC'));
    $diff_time = \Carbon\Carbon::parse($datetime)->diffInHours();

    return $diff_time;
}
function timeAgo($date)
{
    if ($date == null) {
        return '-';
    }
    $datetime = new \DateTime($date);
    $datetime->setTimezone(new \DateTimeZone(setting('default_time_zone') ?? 'UTC'));
    $diff_time = \Carbon\Carbon::parse($datetime)->diffForHumans();

    return $diff_time;
}
function dateAgo($date, $type2 = '')
{
    if ($date == null || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    $diff_time1 = \Carbon\Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();
    $datetime = new \DateTime($date);
    $datetime->setTimezone(new \DateTimeZone(setting('default_time_zone') ?? 'UTC'));
    $diff_time = \Carbon\Carbon::parse($datetime->format('Y-m-d H:i:s'))->isoFormat('LLL');
    if ($type2 != '') {
        return $diff_time;
    }

    return $diff_time1 . ' on ' . $diff_time;
}

function customDate($date, $format = 'd-m-Y h:i A')
{
    if ($date == null || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    $datetime = new \DateTime($date);
    $la_time = new \DateTimeZone(setting('default_time_zone') ?? 'UTC');
    $datetime->setTimezone($la_time);
    $newDate = $datetime->format('Y-m-d H:i:s');
    $diff_time = \Carbon\Carbon::createFromTimeStamp(strtotime($newDate))->format($format);

    return $diff_time;
}

function saveDate($date)
{
    if ($date == null || $date == '0000-00-00 00:00:00') {
        return null;
    }
    $datetime = new \DateTime($date);
    // $la_time = new \DateTimeZone(\Auth::check() ? \Auth::user()->time_zone ?? 'UTC' : 'UTC');
    $la_time = new \DateTimeZone(setting('default_time_zone') ?? 'UTC');
    $datetime->setTimezone($la_time);
    $newDate = $datetime->format('Y-m-d H:i:s');
    $diff_time = \Carbon\Carbon::createFromTimeStamp(strtotime($newDate));

    return $diff_time;
}
function strtotimeToDate($date)
{
    if ($date == null || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    $datetime = new \DateTime($date);
    $datetime->setTimezone(new \DateTimeZone(setting('default_time_zone') ?? 'UTC'));
    $diff_time = \Carbon\Carbon::createFromTimeStamp($datetime);

    return $diff_time;
}
function formatOffset($offset)
{
    $hours = $offset / 3600;
    $remainder = $offset % 3600;
    $sign = $hours > 0 ? '+' : '-';
    $hour = (int) abs($hours);
    $minutes = (int) abs($remainder / 60);

    if ($hour == 0 and $minutes == 0) {
        $sign = ' ';
    }

    return 'GMT' . $sign . str_pad($hour, 2, '0', STR_PAD_LEFT)
        . ':' . str_pad($minutes, 2, '0');
}

function timeZoneList()
{
    $list = \DateTimeZone::listAbbreviations();
    $idents = \DateTimeZone::listIdentifiers();

    $data = $offset = $added = [];
    foreach ($list as $abbr => $info) {
        foreach ($info as $zone) {
            if (!empty($zone['timezone_id']) and !in_array($zone['timezone_id'], $added) and in_array($zone['timezone_id'], $idents)) {
                $z = new \DateTimeZone($zone['timezone_id']);
                $c = new \DateTime(null, $z);
                $zone['time'] = $c->format('H:i a');
                $offset[] = $zone['offset'] = $z->getOffset($c);
                $data[] = $zone;
                $added[] = $zone['timezone_id'];
            }
        }
    }

    array_multisort($offset, SORT_ASC, $data);
    $options = [];
    foreach ($data as $key => $row) {
        $options[$row['timezone_id']] = $row['time'] . ' - ' . formatOffset($row['offset']) . ' ' . $row['timezone_id'];
    }

    return $options;
}

/*
 * Global helpers file with misc functions.
 */
if (!function_exists('app_name')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function app_name()
    {
        return setting('app_name') ?? config('app.name');
    }
}
/**
 * Avatar Find By Gender
 */
if (!function_exists('default_user_avatar')) {
    function default_user_avatar()
    {
        return asset(config('app.avatar_base_path') . 'avatar.webp');
    }
    function default_user_name()
    {
        return __('messages.unknown_user');
    }
}
if (!function_exists('user_avatar')) {
    function user_avatar()
    {
        if (auth()->user()->profile_image ?? null) {
            return auth()->user()->profile_image;
        } else {
            return asset(config('app.avatar_base_path') . 'avatar.webp');
        }
    }
}

if (!function_exists('default_file_url')) {
    function default_file_url()
    {
        return asset(config('app.image_path') . 'default.webp');
    }
}

/*
 * Global helpers file with misc functions.
 */
if (!function_exists('user_registration')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function user_registration()
    {
        $user_registration = false;

        if (env('USER_REGISTRATION') == 'true') {
            $user_registration = true;
        }

        return $user_registration;
    }
}

/**
 * Global Json DD
 * !USAGE
 * return jdd($id);
 */
if (!function_exists('jdd')) {
    function jdd($data)
    {
        return response()->json($data, 500);
        exit();
    }
}

/*
 *
 * label_case
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('label_case')) {
    /**
     * Prepare the Column Name for Lables.
     */
    function label_case($text)
    {
        $order = ['_', '-'];
        $replace = ' ';

        $new_text = trim(\Illuminate\Support\Str::title(str_replace('"', '', $text)));
        $new_text = trim(\Illuminate\Support\Str::title(str_replace($order, $replace, $text)));
        $new_text = preg_replace('!\s+!', ' ', $new_text);

        return $new_text;
    }
}

/*
 *
 * show_column_value
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('show_column_value')) {
    /**
     * Return Column values as Raw and formatted.
     *
     * @param  string  $valueObject  Model Object
     * @param  string  $column  Column Name
     * @param  string  $return_format  Return Type
     * @return string Raw/Formatted Column Value
     */
    function show_column_value($valueObject, $column, $return_format = '')
    {
        $column_name = $column->Field;
        $column_type = $column->Type;

        $value = $valueObject->$column_name;

        if ($return_format == 'raw') {
            return $value;
        }

        if (($column_type == 'date') && $value != '') {
            $datetime = \Carbon\Carbon::parse($value);

            return $datetime->isoFormat('LL');
        } elseif (($column_type == 'datetime' || $column_type == 'timestamp') && $value != '') {
            $datetime = \Carbon\Carbon::parse($value);

            return $datetime->isoFormat('LLLL');
        } elseif ($column_type == 'json') {
            $return_text = json_encode($value);
        } elseif ($column_type != 'json' && \Illuminate\Support\Str::endsWith(strtolower($value), ['png', 'jpg', 'jpeg', 'gif', 'svg'])) {
            $img_path = asset($value);

            $return_text = '<figure class="figure">
                                <a href="' . $img_path . '" data-lightbox="image-set" data-title="Path: ' . $value . '">
                                    <img src="' . $img_path . '" style="max-width:200px;" class="figure-img img-fluid rounded img-thumbnail" alt="">
                                </a>
                                <figcaption class="figure-caption">Path: ' . $value . '</figcaption>
                            </figure>';
        } else {
            $return_text = $value;
        }

        return $return_text;
    }
}

/*
 *
 * fielf_required
 * Show a * if field is required
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('fielf_required')) {
    /**
     * Prepare the Column Name for Lables.
     */
    function fielf_required($required)
    {
        $return_text = '';

        if ($required != '') {
            $return_text = '<span class="text-danger">*</span>';
        }

        return $return_text;
    }
}

/*
 * Get or Set the Settings Values
 *
 * @var [type]
 */
if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        if (is_null($key)) {
            return new App\Models\Setting();
        }

        if (is_array($key)) {
            return App\Models\Setting::set($key[0], $key[1]);
        }

        $value = App\Models\Setting::get($key);
        return is_null($value) ? value($default) : $value;
    }
}

/*
 * Show Human readable file size
 *
 * @var [type]
 */
if (!function_exists('humanFilesize')) {
    function humanFilesize($size, $precision = 2)
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step = 1024;
        $i = 0;

        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }

        return round($size, $precision) . $units[$i];
    }
}

/*
 *
 * Encode Id to a Hashids\Hashids
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('encode_id')) {
    /**
     * Prepare the Column Name for Lables.
     */
    function encode_id($id)
    {
        $hashids = new Hashids\Hashids(config('app.salt'), 3, 'abcdefghijklmnopqrstuvwxyz1234567890');
        $hashid = $hashids->encode($id);

        return $hashid;
    }
}

/*
 *
 * Decode Id to a Hashids\Hashids
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('decode_id')) {
    /**
     * Prepare the Column Name for Lables.
     */
    function decode_id($hashid)
    {
        $hashids = new Hashids\Hashids(config('app.salt'), 3, 'abcdefghijklmnopqrstuvwxyz1234567890');
        $id = $hashids->decode($hashid);

        if (count($id)) {
            return $id[0];
        } else {
            abort(404);
        }
    }
}

/*
 *
 * Prepare a Slug for a given string
 * Laravel default str_slug does not work for Unicode
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('slug_format')) {
    /**
     * Format a string to Slug.
     */
    function slug_format($string)
    {
        $base_string = $string;

        $string = preg_replace('/\s+/u', '-', trim($string));
        $string = str_replace('/', '-', $string);
        $string = str_replace('\\', '-', $string);
        $string = strtolower($string);

        $slug_string = $string;

        return $slug_string;
    }
}

/*
 *
 * icon
 * A short and easy way to show icon fornts
 * Default value will be check icon from FontAwesome
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('icon')) {
    /**
     * Format a string to Slug.
     */
    function icon($string = 'fas fa-check')
    {
        $return_string = "<i class='" . $string . "'></i>";

        return $return_string;
    }
}

/*
 *
 * Decode Id to a Hashids\Hashids
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('generate_rgb_code')) {
    /**
     * Prepare the Column Name for Lables.
     */
    function generate_rgb_code($opacity = '0.9')
    {
        $str = '';
        for ($i = 1; $i <= 3; $i++) {
            $num = mt_rand(0, 255);
            $str .= "$num,";
        }
        $str .= "$opacity,";
        $str = substr($str, 0, -1);

        return $str;
    }
}

/*
 *
 * Return Date with weekday
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('date_today')) {
    /**
     * Return Date with weekday.
     *
     * Carbon Locale will be considered here
     * Example:
     * Friday, July 24, 2020
     */
    function date_today()
    {
        $str = \Carbon\Carbon::now()->isoFormat('dddd, LL');

        return $str;
    }
}

if (!function_exists('language_direction')) {
    /**
     * return direction of languages.
     *
     * @return string
     */
    function language_direction($language = null)
    {
        if (empty($language)) {
            $language = app()->getLocale();
        }
        $language = strtolower(substr($language, 0, 2));
        $rtlLanguages = [
            'ar', //  'العربية', Arabic
            'arc', //  'ܐܪܡܝܐ', Aramaic
            'bcc', //  'بلوچی مکرانی', Southern Balochi
            'bqi', //  'بختياري', Bakthiari
            'ckb', //  'Soranî / کوردی', Sorani Kurdish
            'dv', //  'ދިވެހިބަސް', Dhivehi
            'fa', //  'فارسی', Persian
            'glk', //  'گیلکی', Gilaki
            'he', //  'עברית', Hebrew
            'lrc', //- 'لوری', Northern Luri
            'mzn', //  'مازِرونی', Mazanderani
            'pnb', //  'پنجابی', Western Punjabi
            'ps', //  'پښتو', Pashto
            'sd', //  'سنڌي', Sindhi
            'ug', //  'Uyghurche / ئۇيغۇرچە', Uyghur
            'ur', //  'اردو', Urdu
            'yi', //  'ייִדיש', Yiddish
        ];
        if (in_array($language, $rtlLanguages)) {
            return 'rtl';
        }

        return 'ltr';
    }
}

if (!function_exists('module_exist')) {
    /**
     * return value for module exist or not.
     *
     * @return bool
     */
    function module_exist($module_name)
    {
        return \Module::find($module_name)?->isEnabled() ?? false;
    }
}

// function storeMediaFile($module, $files, $key = 'file_url')
// {

//     $module->clearMediaCollection($key);

//     if (is_array($files)) {
//         foreach ($files as $file) {
//             if (!empty($file)) {
//                 $module->addMedia($file)->toMediaCollection($key);
//             }
//         }
//     } else {
//         $module->clearMediaCollection($key);
//         $mediaItems = $module->addMedia($files)->toMediaCollection($key);
//     }
// }

function storeMediaFile($module, $files, $key = 'file_url')
{
    $module->clearMediaCollection($key);

    $handleMediaUpload = function ($file) use ($module, $key) {
        if (!empty($file)) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = $originalName . '.' . $extension;

            $module->addMedia($file)
                ->usingFileName($fileName)
                ->toMediaCollection($key);
        }
    };

    if (is_array($files)) {
        foreach ($files as $file) {
            $handleMediaUpload($file);
        }
    } else {
        $handleMediaUpload($files);
    }
}


function getCustomizationSetting($name, $key = 'customization_json')
{
    $settingObject = setting($key);
    if (isset($settingObject) && $key == 'customization_json') {
        try {
            $settings = (array) json_decode(html_entity_decode(stripslashes($settingObject)))->setting;

            return collect($settings[$name])['value'];
        } catch (\Exception $e) {
            return '';
        }

        return '';
    } elseif ($key == 'root_color') {
        //
    }

    return '';
}
// Usage: getCustomizationSetting('app_name') it will return value of this json
// getCustomizationSetting('footer')
// getCustomizationSetting('menu_style)

function str_slug($title, $separator = '-', $language = 'en')
{
    return Str::slug($title, $separator, $language);
}

function formatCurrency($number, $noOfDecimal, $decimalSeparator, $thousandSeparator, $currencyPosition, $currencySymbol)
{
    // Format number with specified decimals
    $formattedNumber = number_format($number, $noOfDecimal, $decimalSeparator, $thousandSeparator);

    $currencyString = '';

    switch ($currencyPosition) {
        case 'left':
            $currencyString = $currencySymbol . $formattedNumber;
            break;

        case 'left_with_space':
            $currencyString = $currencySymbol . ' ' . $formattedNumber;
            break;

        case 'right':
            $currencyString = $formattedNumber . $currencySymbol;
            break;

        case 'right_with_space':
            $currencyString = $formattedNumber . ' ' . $currencySymbol;
            break;

        default:
            $currencyString = $currencySymbol . $formattedNumber;
    }

    return $currencyString;
}

function formatCurrencyNoDecimals($number)
{
    return formatCurrency($number, 0, '', ',', 'left', '$');
}

function timeAgoFormate($date)
{
    if ($date == null) {
        return '-';
    }

    // date_default_timezone_set('UTC');

    $diff_time = \Carbon\Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();

    return $diff_time;
}

function getDiscountedProductPrice($product_price, $product_id)
{
    $product = \Modules\Product\Models\Product::where('id', $product_id)->first();

    $discount_applicable = false;

    if ($product != null) {
        if (
            $product->discount_start_date !== null &&
            $product->discount_end_date !== null &&
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            $discount_type = $product['discount_type'];
            $discount_value = $product['discount_value'];

            $discount_amount = 0;

            if ($discount_type == 'percent') {
                $discount_amount += $product_price * $discount_value / 100;
            } elseif ($discount_type == 'fixed') {
                $discount_amount += $discount_value;
            }

            return $product_price - $discount_amount;
        }

        return $product_price;
    } else {
        return 0;
    }
}

function checkInWishList($product_id, $user_id)
{
    $product = \Modules\Product\Models\WishList::where('product_id', $product_id)->where('user_id', $user_id)->first();

    if (!$product) {
        return 0;
    } else {
        return 1;
    }
}

function checkInCart($product_variation_id, $user_id)
{
    $cart = \Modules\Product\Models\Cart::where('user_id', $user_id)->where('product_variation_id', $product_variation_id)->first();

    if (!$cart) {
        return 0;
    } else {
        return 1;
    }
}

function checkIsLike($review_id, $user_id)
{
    $review = \Modules\Product\Models\Review::find($review_id);

    if (!$review) {
        return 0; // Review not found
    }

    $isLiked = $review->likes()
        ->where('user_id', $user_id)
        ->where('is_like', 1)
        ->exists();

    return $isLiked ? 1 : 0;
}

function checkIsdisLike($review_id, $user_id)
{
    $review = \Modules\Product\Models\Review::find($review_id);

    if (!$review) {
        return 0; // Review not found
    }

    $isLiked = $review->likes()
        ->where('user_id', $user_id)
        ->where('dislike_like', 1)
        ->exists();

    return $isLiked ? 1 : 0;
}

function getDiscountedPrice($data)
{
    $sumOfDiscountedPrices = 0;

    if ($data) {
        foreach ($data as $cart) {
            $price = $cart->product_variation->price;

            $discount_applicable = false;

            if (
                $cart->product->discount_start_date !== null &&
                $cart->product->discount_end_date !== null &&
                strtotime(date('d-m-Y H:i:s')) >= $cart->product->discount_start_date &&
                strtotime(date('d-m-Y H:i:s')) <= $cart->product->discount_end_date
            ) {
                $discount_applicable = true;
            }

            if ($discount_applicable) {
                if ($cart->product->discount_type === 'percent') {
                    $discountedPrice = ($price * $cart->product->discount_value) / 100;
                } elseif ($cart->product->discount_type === 'fixed') {
                    $discountedPrice = $cart->product->discount_value;
                }

                $sumOfDiscountedPrices += $discountedPrice;
            }
        }
    }

    return $sumOfDiscountedPrices;
}

if (!function_exists('variationDiscountedPrice')) {
    // return discounted price of a variation
    function variationDiscountedPrice($product, $variation, $addTax = false)
    {
        $price = $variation->price;

        $discount_applicable = false;

        if ($product->discount_start_date == null || $product->discount_end_date == null) {
            $discount_applicable = false;
        } elseif (
            strtotime(date('d-m-Y ')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount_value) / 100;
            } elseif ($product->discount_type == 'fixed') {
                $price -= $product->discount_value;
            }
        }

        if ($addTax) {
            foreach ($product->taxes as $product_tax) {
                if ($product_tax->tax_type == 'percent') {
                    $price += ($price * $product_tax->tax_value) / 100;
                } elseif ($product_tax->tax_type == 'fixed') {
                    $price += $product_tax->tax_value;
                }
            }
        }

        return $price;
    }
}

function getDiscountAmount($data)
{
    $sumOfDiscountedPrices = 0;

    if ($data) {
        foreach ($data as $cart) {
            $price = $cart->product_variation->price * $cart->qty;

            $discount_applicable = false;

            if ($cart->product->discount_start_date == null || $cart->product->discount_end_date == null) {
                $discount_applicable = false;
            } elseif (
                strtotime(date('d-m-Y')) >= $cart->product->discount_start_date &&
                strtotime(date('d-m-Y')) <= $cart->product->discount_end_date
            ) {
                $discount_applicable = true;
            }

            if ($discount_applicable) {
                if ($cart->product->discount_type === 'percent') {
                    $discountedPrice = ($price * $cart->product->discount_value) / 100;
                } elseif ($cart->product->discount_type === 'fixed') {
                    $discountedPrice = $cart->product->discount_value;
                }

                $sumOfDiscountedPrices += $discountedPrice;
            }
        }
    }

    return $sumOfDiscountedPrices;
}

if (!function_exists('getSubTotal')) {
    // return sub total price
    function getSubTotal($carts, $couponDiscount = true, $couponCode = '', $addTax = true)
    {
        $price = 0;
        $amount = 0;
        if (count($carts) > 0) {
            foreach ($carts as $cart) {
                $product = $cart->product_variation->product;
                $variation = $cart->product_variation;

                $discountedVariationPriceWithTax = variationDiscountedPrice($product, $variation, $addTax);
                $price += (float) $discountedVariationPriceWithTax * $cart->qty;
            }
        }

        return $price - $amount;
    }
}

if (!function_exists('generateVariationOptions')) {
    //  generate combinations based on variations
    function generateVariationOptions($options)
    {
        if (count($options) == 0) {
            return $options;
        }
        $variation_ids = [];
        foreach ($options as $option) {
            $value_ids = [];
            if (isset($variation_ids[$option->variation_id])) {
                $value_ids = $variation_ids[$option->variation_id];
            }
            if (!in_array($option->variation_value_id, $value_ids)) {
                array_push($value_ids, $option->variation_value_id);
            }
            $variation_ids[$option->variation_id] = $value_ids;
        }
        $options = [];
        foreach ($variation_ids as $id => $values) {
            $variationValues = [];
            foreach ($values as $value) {
                $variationValue = \Modules\Product\Models\VariationValue::find($value);
                $val = [
                    'id' => $value,
                    'name' => $variationValue->name,
                ];
                array_push($variationValues, $val);
            }
            $variation = \Modules\Product\Models\Variations::find($id);
            if ($variation) {
                $data['id'] = $id;
                $data['name'] = $variation->name;
                $data['values'] = $variationValues;

                array_push($options, $data);
            }
        }

        return $options;
    }
}

function getproductDiscountAmount($data)
{
    $sumOfDiscountedPrices = 0;

    if ($data) {
        foreach ($data as $cart) {
            $price = $cart->product_price * $cart->product_qty;

            $discount_applicable = false;

            if ($cart->product->discount_start_date == null || $cart->product->discount_end_date == null) {
                $discount_applicable = false;
            } elseif (
                strtotime(date('d-m-Y')) >= $cart->product->discount_start_date &&
                strtotime(date('d-m-Y')) <= $cart->product->discount_end_date
            ) {
                $discount_applicable = true;
            }

            if ($discount_applicable) {
                if ($cart->product->discount_type === 'percent') {
                    $discountedPrice = ($price * $cart->product->discount_value) / 100;
                } elseif ($cart->product->discount_type === 'fixed') {
                    $discountedPrice = $cart->product->discount_value;
                }

                $sumOfDiscountedPrices += $discountedPrice;
            }
        }
    }

    return $sumOfDiscountedPrices;
}

function getTaxamount($amount)
{

    $tax_list = \Modules\Tax\Models\Tax::where('status', 1)->where('module_type', 'products')->get();

    $total_tax_amount = 0;
    $tax_details = [];
    $tax_amount = 0;

    foreach ($tax_list as $tax) {
        if ($tax->type == 'percent') {
            $tax_amount = $amount * $tax->value / 100;
        } elseif ($tax->type == 'fixed') {
            $tax_amount = $tax->value;
        }

        $tax_details[] = [
            'tax_name' => $tax->title,
            'tax_type' => $tax->type,
            'tax_value' => $tax->value,
            'tax_amount' => $tax_amount,
        ];

        $total_tax_amount += $tax_amount;
    }

    return [
        'total_tax_amount' => $total_tax_amount,
        'tax_details' => $tax_details,
    ];
}

function getBookingTaxamount($amount, $tax_data)
{
    $tax_list = $tax_data;

    $total_tax_amount = 0;
    $tax_details = [];
    $tax_amount = 0;

    if ($tax_list != null) {

        foreach ($tax_list as $tax) {

            if (is_array($tax) && isset($tax['type'])) {

                if ($tax['type'] == 'percent') {
                    $tax_amount = $amount * $tax['percent'] / 100;
                } elseif ($tax['type'] == 'fixed') {
                    $tax_amount = $tax['tax_amount'];
                }

                $tax_details[] = [
                    'tax_name' => $tax['name'],
                    'tax_type' => $tax['type'],
                    "tax_scope" => isset($tax['tax_type']) ? $tax['tax_type'] : (isset($tax['tax_scope']) ? $tax['tax_scope'] : null),

                    'tax_value' => isset($tax['percent']) ? $tax['percent'] : $tax['tax_amount'],
                    'tax_amount' => $tax_amount,
                ];

                $total_tax_amount += $tax_amount;
            }
        }
    } else {

        $tax_list = \Modules\Tax\Models\Tax::active()
            ->whereNull('module_type')
            ->orWhere('module_type', 'services')->where('status', 1)->where('tax_type', 'exclusive')->get();


        foreach ($tax_list as $tax) {

            if ($tax['type'] == 'percent') {
                $tax_amount = $amount * $tax['value'] / 100;
            } elseif ($tax['type'] == 'fixed') {
                $tax_amount = $tax['value'];
            }

            $tax_details[] = [
                'tax_name' => $tax['title'],
                'tax_type' => $tax['type'],
                'tax_value' => isset($tax['percent']) ? $tax['percent'] : $tax['value'],
                "tax_scope" => isset($tax['tax_type']) ? $tax['tax_type'] : (isset($tax['tax_scope']) ? $tax['tax_scope'] : null),

                'tax_amount' => $tax_amount,
            ];

            $total_tax_amount += $tax_amount;
        }
    }




    return [
        'total_tax_amount' => $total_tax_amount,
        'tax_details' => $tax_details,
    ];
}

function multiVendor()
{
    $setting_data = \App\Models\Setting::where('name', 'is_multi_vendor')->first();
    return $setting_data ? $setting_data->val : 0;
}
function encounter()
{
    $fields = ['is_encounter_problem', 'is_encounter_observation', 'is_encounter_note', 'is_encounter_prescription'];
    foreach ($fields as $field) {
        $encounter[$field] = setting($field);
        if (in_array($field, ['body_image'])) {
            $encounter[$field] = asset(setting($field));
        }
    }
    return $encounter;
}
function bodychart()
{

    $setting_data = \App\Models\Setting::where('name', 'is_body_chart')->first();
    return $setting_data->val ?? 0;
}

function soap()
{
    $setting_data = \App\Models\Setting::where('name', 'view_patient_soap')->first();
    return $setting_data->val ?? 0;
}

function prescription()
{
    $setting_data = \App\Models\Setting::where('name', 'is_encounter_prescription')->first();
    return $setting_data->val ?? 0;
}
function telemedSetting()
{
    $setting_data = \App\Models\Setting::where('name', 'is_telemed_setting')->first();
    return $setting_data->val ?? 0;
}


function getzoomVideoUrl($data)
{

    $setting_data = \App\Models\Setting::where('type', 'is_zoom')->get();
    $account_id = '';
    $client_id = '';
    $client_secret = '';

    if ($setting_data->isNotEmpty()) {

        foreach ($setting_data as $setting) {
            if ($setting->name === 'account_id') {
                $account_id = $setting->val;
            } elseif ($setting->name === 'client_id') {
                $client_id = $setting->val;
            } elseif ($setting->name === 'client_secret') {
                $client_secret = $setting->val;
            }
        }
    } else {

        return $zoom_url = '';
    }
    // $account_id = 'WJHpsUd9TKKt99vWOKqeig';
    // $client_id = 'AcILlYbFS2ajeVjFPQMdwg';
    // $client_secret = '150kB12FZyJ5W4AHoDi1EpwG9mCrxJX9';

    $authorization = base64_encode($client_id . ':' . $client_secret);

    $curl = curl_init();

    $url = "https://zoom.us/oauth/token?grant_type=account_credentials&account_id=$account_id";

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Host: zoom.us',
            'Authorization: Basic ' . $authorization,
            'Cookie: TS018dd1ba=01a4bf7a43d12da00e647623dc86cc762d126ecbc8d05da8041a487f2d9af5f795dbc94ddaff0e57c96c75394da34ba688b274d027; TS01f92dc5=01a4bf7a43d12da00e647623dc86cc762d126ecbc8d05da8041a487f2d9af5f795dbc94ddaff0e57c96c75394da34ba688b274d027; __cf_bm=2zLDWSFRt_rnknkjf0bFIFxuJIu1ZSd48NLBQiH7ByU-1691735997-0-AfSs0V8YmXQE0t25v+BewBtQQlqkCxAOHQI9pbUANYn5bxIi09JPmaKA/LM7IUsjd3iHRFhgr8BttQMgSkzlOdk=; _zm_chtaid=528; _zm_ctaid=T-ladtb9RQy5jtqpoyz5Ew.1691735997275.ec80d36ec41d720bf03b5c8cdc81edaa; _zm_mtk_guid=0bab4993560a483fb93c8926f1220417; _zm_page_auth=us05_c_9QH5C_6TQJS-eycodFJaSg; _zm_ssid=us05_c_jooeGe7USU6A4k5JXX5Psg; cred=7CB892515BDA6C68C6344C62AB3336E3'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $response_data = json_decode($response, true);

    if (isset($response_data['access_token'])) {
        $access_token = $response_data['access_token'];

        // Now you can use $access_token to create your Zoom join URL
        $zoom_url = getjoinUrl($access_token, $data);
    } else {
        $zoom_url = [];
    }
    return $zoom_url;
}

function getjoinUrl($access_token, $data)
{
    $curl = curl_init();
    $user = \App\Models\User::find($data->user_id);
    $doctor = \App\Models\User::find($data['doctor_id']);
    $center = Modules\Clinic\Models\ClinicServiceMapping::with('service', 'center')
        ->where('service_id', $data->service_id)
        ->where('clinic_id', $data->clinic_id)
        ->first();
    if ($center) {
        $clinicService = $center->service;
        $clinic = $center->center;
        $emailData = [
            'service_name' => $clinicService->name,
            'user_name' => "{$user->first_name} {$user->last_name}",
            'clinic_name' => $clinic->name,
            'doctor_name' => "{$doctor->first_name} {$doctor->last_name}",
            'appointment_date' => $data->appointment_date,
            'appointment_time' => $data->appointment_time,
        ];
    }
    $service_name = isset($emailData['service_name']) ? $emailData['service_name'] : '';
    $service_duration = isset($data['service_duration']) ? $data['service_duration'] : '30'; // Default duration
    $date_time = new DateTime($emailData['appointment_date'] . ' ' . $emailData['appointment_time']);
    $formatted_date_time = $date_time->format('Y-m-d\TH:i:s\Z'); // Format the date-time as required
    $desc = "Dear {$emailData['user_name']},<br><br>" .
        "We are delighted to confirm your appointment for {$emailData['service_name']} at {$emailData['clinic_name']} with Dr. {$emailData['doctor_name']} on {$emailData['appointment_date']} at {$emailData['appointment_time']}.<br><br>" .
        "Best regards,<br>" .
        "{$emailData['clinic_name']}";
    $description = strip_tags($desc);
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.zoom.us/v2/users/me/meetings',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "topic": "' . $service_name . '",
            "type": 2,
            "start_time": "' . $formatted_date_time . '",
            "settings": {
                "host_video": true,
                "participant_video": true,
                "join_before_host": true,
                "mute_upon_entry": "true",
                "watermark": "true",
                "audio": "voip",
                "auto_recording": "cloud"
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token
        ),
    ));

    $response = curl_exec($curl);

    if ($response) {
        $response_data = json_decode($response, true);

        if (isset($response_data['start_url']) && isset($response_data['join_url'])) {
            $start_url = $response_data['start_url'];
            $join_url = $response_data['join_url'];
        } else {
            $start_url = '';
            $join_url = '';
        }
    } else {
        $start_url = '';
        $join_url = '';
    }

    curl_close($curl);
    $zoom_url = [

        'start_url' => $start_url,
        'join_url' => $join_url,

    ];

    $meetingDetails['title'] = "Join Zoom Meeting: {$service_name}";
    $meetingDetails['description'] = $description;
    $meetingDetails['link'] = $start_url;
    $emails = \App\Models\User::whereIn('id', [$data['doctor_id'], $data['user_id']])
        ->pluck('email')
        ->toArray();
    Mail::to($emails)->send(new App\Mail\AppointmentConfirmation($meetingDetails));
    return $zoom_url;
}
function greeting()
{
    $timezone = new \DateTimeZone(setting('default_time_zone') ?? 'UTC');
    $now = \Carbon\Carbon::now($timezone);
    $hour = $now->hour;

    if ($hour >= 5 && $hour < 12) {
        return trans('messages.good_morning');
    } elseif ($hour >= 12 && $hour < 17) {
        return trans('messages.good_afternoon');
    } elseif ($hour >= 17 && $hour < 22) {
        return trans('messages.good_evening');
    } else {
        return trans('messages.good_night');
    }
}
function setNamePrefix($user)
{
    $display_name = $user->first_name . " " . $user->last_name;
    $gender = $user->gender === 'male' ? 'Mr.' : 'Ms.';
    return $gender . $display_name;
}

function calculateDoctorCommission($data)
{
    $clinicId = $data->id;
    $doctorId = $data->clinicdoctor->value('doctor_id');

    $doctorCommission = Modules\Appointment\Models\Appointment::with('doctor')
        ->where('clinic_id', $clinicId)
        ->where('doctor_id', $doctorId)
        ->get()
        ->sum(function ($appointment) {
            return optional($appointment->commission)->commission_amount ?? 0;
        });

    return $doctorCommission;
}
function calculateAdminCommission($data)
{
    // Fetch the admin commission from the database (assuming Commission model has 'type' and 'value' attributes)
    $commission = Modules\Commission\Models\Commission::find(1);
    $commission_type = $commission->commission_type ?? '';
    $commission_value = $commission->commission_value ?? 0;
    // Calculate the doctor's commission
    $doctorCommission = calculateDoctorCommission($data);

    // Fetch the total amount for appointments for the clinic
    $appointments = Modules\Appointment\Models\Appointment::with('appointmenttransaction')
        ->where('clinic_id', $data->id)
        ->get();

    $service_amount = 0;

    foreach ($appointments as $appointment) {
        $service_amount += optional($appointment->appointmenttransaction)->total_amount ?? 0;
    }

    // Calculate the total amount after deducting the doctor's commission
    $total_amount = $service_amount - $doctorCommission;

    // Calculate the admin commission based on commission type
    $adminCommission = 0;
    if ($appointments->contains('clinic_id', $data->id)) {
        $clinicAppointmentCount = $appointments->where('clinic_id', $data->id)->count();

        if ($commission_type == 'fix') {
            // If commission type is fixed, use the fixed value
            $adminCommission = $commission_value * $clinicAppointmentCount;
        } elseif ($commission_type == 'percentage') {
            // If commission type is percentage, calculate based on total amount
            $adminCommission = $commission_value * $total_amount / 100;
        }
    }


    return $adminCommission;
}


function calculateClinicCommission($data)
{
    // Fetch the doctor and admin commission amounts
    $doctorCommission = calculateDoctorCommission($data);
    $adminCommission = calculateAdminCommission($data);

    // Fetch the total amount for appointments for the clinic
    $appointments = Modules\Appointment\Models\Appointment::with('appointmenttransaction')
        ->where('clinic_id', $data->id)
        ->get();

    $service_amount = 0;

    foreach ($appointments as $appointment) {
        $service_amount += optional($appointment->appointmenttransaction)->total_amount ?? 0;
    }

    // Calculate the total amount after deducting the doctor and admin commissions
    $total_amount = $service_amount - $doctorCommission - $adminCommission;

    // Return the clinic commission
    return $total_amount;
}

function getFileExistsCheck($media)
{
    $mediaCondition = false;

    if ($media) {
        if ($media->disk == 'public') {
            $mediaCondition = file_exists($media->getPath());
        } else {
            $mediaCondition = \Storage::disk($media->disk)->exists($media->getPath());
        }
    }

    return $mediaCondition;
}

function getAttachmentArray($attchments)
{
    $files = [];
    if (count($attchments) > 0) {
        foreach ($attchments as $attchment) {
            if (getFileExistsCheck($attchment)) {
                $file = [
                    'id' => $attchment->id,
                    'url' => $attchment->getFullUrl()
                ];
                array_push($files, $file);
            }
        }
    }

    return $files;
}

function dateFormatList()
{
    return [
        'Y-m-d' => date('Y-m-d'),
        'm-d-Y' => date('m-d-Y'),
        'd-m-Y' => date('d-m-Y'),
        'd/m/Y' => date('d/m/Y'),
        'm/d/Y' => date('m/d/Y'),
        'Y/m/d' => date('Y/m/d'),
        'Y.m.d' => date('Y.m.d'),
        'd.m.Y' => date('d.m.Y'),
        'm.d.Y' => date('m.d.Y'),
        'jS M Y' => date('jS M Y'),
        'M jS Y' => date('M jS Y'),
        'D, M d, Y' => date('D, M d, Y'),
        'D, d M, Y' => date('D, d M, Y'),
        'D, M jS Y' => date('D, M jS Y'),
        'D, jS M Y' => date('D, jS M Y'),
        'F j, Y' => date('F j, Y'),
        'd F, Y' => date('d F, Y'),
        'jS F, Y' => date('jS F, Y'),
        'l jS F Y' => date('l jS F Y'),
        'l, F j, Y' => date('l, F j, Y'),

    ];
}

function getTimeInFormat($format)
{
    $now = new DateTime();
    $hours = $now->format('H');
    $minutes = $now->format('i');
    $seconds = $now->format('s');
    $milliseconds = $now->format('v');
    $totalSecondsSinceMidnight = ($hours * 3600) + ($minutes * 60) + $seconds;

    switch ($format) {
        case "H:i":
            return "$hours:$minutes";
        case "H:i:s":
            return "$hours:$minutes:$seconds";
        case "g:i A":
            $ampm = $hours >= 12 ? 'PM' : 'AM';
            $formattedHours = $hours % 12 || 12;
            return "$formattedHours:$minutes $ampm";
        case "H:i:s T":
            return "$hours:$minutes:$seconds UTC";
        case "H:i:s.v":
            return "$hours:$minutes:$seconds.$milliseconds";
        case "U":
            return $now->getTimestamp();
        case "u":
            return $milliseconds * 1000;
        case "G.i":
            return $hours + $minutes / 60;
        case "@BMT":
            $swatchBeat = floor($totalSecondsSinceMidnight / 86.4);
            return "@{$swatchBeat}BMT";
        default:
            return "Invalid format";
    }
}

function timeFormatList()
{
    $timeFormats = [
        "H:i",
        "H:i:s",
        "g:i A",
        "H:i:s T",
        "H:i:s.v",
        "U",
        "u",
        "G.i",
        "@BMT"
    ];

    return array_map(function ($format) {
        return ['format' => $format, 'time' => getTimeInFormat($format)];
    }, $timeFormats);
}


if (!function_exists('formatDate')) {
    function formatDate($date)
    {
        // Get timezone from settings, default to UTC if not found
        $timezone = \App\Models\Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';
        // Get date format from settings, default to 'Y-m-d' if not found
        $dateSetting = \App\Models\Setting::where('name', 'date_formate')->first();
        $dateformate = $dateSetting ? $dateSetting->val : 'Y-m-d';

        // Get time format from settings, default to 'h:i A' if not found
        $timeSetting = \App\Models\Setting::where('name', 'time_formate')->first();
        $timeformate = $timeSetting ? $timeSetting->val : 'h:i A';

        // Combine date and time format
        $combinedFormat = $dateformate . ' ' . $timeformate;

        // Return formatted date based on the timezone and settings
        return \Carbon\Carbon::parse($date)->timezone($timezone)->format($combinedFormat);
    }
}

if (!function_exists('isActive')) {
    /**
     * Returns 'active' or 'done' class based on the current step.
     *
     * @param  string|array  $route
     * @param  string  $className
     * @return string
     */
    function isActive($route, $className = 'active')
    {
        $currentRoute = Route::currentRouteName();

        if (is_array($route)) {
            return in_array($currentRoute, $route) ? $className : '';
        }

        return $currentRoute == $route ? $className : '';
    }
}

function dbConnectionStatus(): bool
{
    try {
        DB::connection()->getPdo();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function formatUpdatedAt($updatedAt)
{
    $diff = Carbon::now()->diffInHours($updatedAt);
    return $diff < 25 ? $updatedAt->diffForHumans() : $updatedAt->isoFormat('llll');
}

function getSingleMedia($model, $collection = 'profile_image', $skip = true)
{
    if (!\Auth::check() && $skip) {
        return asset('images/user/user.png');
    }
    $media = null;
    if ($model !== null) {
        $media = $model->getFirstMedia($collection);
    }

    if (getFileExistsCheck($media)) {
        return $media->getFullUrl();
    } else {

        switch ($collection) {
            case 'image_icon':
                $media = asset('images/user/user.png');
                break;
            case 'profile_image':
                $media = asset('images/user/user.png');
                break;
            case 'service_attachment':
                $media = asset('images/default.png');
                break;
            case 'site_logo':
                $media = asset('images/logo.png');
                break;
            case 'site_favicon':
                $media = asset('images/favicon.png');
                break;
            case 'app_image':
                $media = asset('images/frontend/mb-serv-1.png');
                break;
            case 'app_image_full':
                $media = asset('images/frontend/mb-serv-full.png');
                break;
            case 'footer_logo':
                $media = asset('landing-images/logo/logo.png');
                break;
            case 'logo':
                $media = asset('images/logo.png');
                break;
            case 'favicon':
                $media = asset('images/favicon.png');
                break;
            case 'loader':
                $media = asset('images/loader.gif');
                break;
            case 'helpdesk_attachment':
                $media = asset('images/default.png');
                break;
            case 'helpdesk_activity_attachment':
                $media = asset('images/default.png');
                break;
            case 'main_image':
                $media = asset('img/frontend/app-images.png');
                break;
            case 'google_play':
                $media = asset('img/frontend/android-btn.png');
                break;
            case 'app_store':
                $media = asset('img/frontend/ios-btn.png');
                break;
            default:
                $media = asset('images/default.png');
                break;
        }
        return $media;
    }
}

function authSession($force = false)
{
    $session = new \App\Models\User;
    if ($force) {
        $user = \Auth::user()->getRoleNames();
        \Session::put('auth_user', $user);
        $session = \Session::get('auth_user');
        return $session;
    }
    if (\Session::has('auth_user')) {
        $session = \Session::get('auth_user');
    } else {
        $user = \Auth::user();
        \Session::put('auth_user', $user);
        $session = \Session::get('auth_user');
    }
    return $session;
}

function comman_custom_response($response, $status_code = 200)
{
    return response()->json($response, $status_code);
}

// In helpers.php
if (!function_exists('generateBreadcrumb')) {
    function generateBreadcrumb()
    {
        $breadcrumb = [];
        $routeName = Route::currentRouteName();

        $breadcrumb[] = ['name' => __('frontend.home'), 'url' => route('frontend.index')];

        // if (in_array($routeName, ['account-setting', 'edit-profile', 'wallet-history', 'appointment-list', 'encounter-list'])) {
        //     $breadcrumb[] = ['name' => 'Profile', 'url' => route('account-setting')];
        // }

        if ($routeName == 'categories') {
            $breadcrumb[] = ['name' => __('frontend.category'), 'url' => route('categories')];
        } elseif ($routeName == 'service-details') {
            $breadcrumb[] = ['name' => __('frontend.services'), 'url' => route('services')];
            $breadcrumb[] = ['name' => __('frontend.service_details'), 'url' => ''];
        } elseif ($routeName == 'clinic-details') {
            $breadcrumb[] = ['name' => __('frontend.clinics'), 'url' => route('clinics')];
            $breadcrumb[] = ['name' => __('frontend.clinic_details'), 'url' => ''];
        } elseif ($routeName == 'doctor-details') {
            $breadcrumb[] = ['name' => __('frontend.doctors'), 'url' => route('doctors')];
            $breadcrumb[] = ['name' => __('frontend.doctor_details'), 'url' => ''];
        } elseif ($routeName == 'appointment-list') {
            $breadcrumb[] = ['name' => __('frontend.my_appointments'), 'url' => route('appointment-list')];
        } elseif ($routeName == 'appointment-details') {
            $breadcrumb[] = ['name' => __('frontend.appointments_details'), 'url' => 'appointment-details'];
        } elseif ($routeName == 'edit-profile') {
            $breadcrumb[] = ['name' => __('frontend.edit_profile'), 'url' => route('edit-profile')];
        } elseif ($routeName == 'encounter-list') {
            $breadcrumb[] = ['name' => __('frontend.encounters'), 'url' => route('encounter-list')];
        } elseif ($routeName == 'wallet-history') {
            $breadcrumb[] = ['name' => __('frontend.wallet_history'), 'url' => route('wallet-history')];
        } elseif ($routeName == 'account-setting') {
            $breadcrumb[] = ['name' => __('frontend.settings'), 'url' => ''];
        } elseif ($routeName == 'blog-details') {
            $breadcrumb[] = ['name' => __('frontend.blog'), 'url' => route('blogs')];
            $breadcrumb[] = ['name' => __('frontend.blog_details'), 'url' => ''];
        } elseif ($routeName == 'user-notifications') {
            $breadcrumb[] = ['name' => __('frontend.notifications'), 'url' => 'user-notifications'];
        } elseif ($routeName == 'contact-us') {
            $breadcrumb[] = ['name' => __('frontend.contact_us'), 'url' => 'contact-us'];
        } elseif ($routeName == 'incidence.index') {
            $breadcrumb[] = ['name' => __('frontend.incidence'), 'url' => route('incidence.index')];
        } else {
            $breadcrumb[] = ['name' => ucfirst(__('frontend.' . $routeName)), 'url' => ''];
        }

        return $breadcrumb;
    }
}

function getDurationFormat($duration)
{

    $hours = floor($duration / 60);
    $remainingMinutes = $duration % 60;

    $formatted = '';

    if ($hours > 0) {
        $formatted .= $hours . __('frontend.hour') . ' ';
    }

    if ($remainingMinutes > 0 || $hours == 0) {
        $formatted .= $remainingMinutes . __('frontend.min');
    }

    return trim($formatted);
}

function getDisplayName($user)
{

    $display_name = '-';

    if ($user) {

        $display_name  =  $user->first_name . ' ' . $user->last_name;
        if ($user->user_type == 'doctor') {
            return 'Dr. ' . $display_name;
        }
    }

    return $display_name;
}


function GetpaymentMethod($name)
{

    if ($name) {
        $payment_key = Setting::where('name', $name)->value('val');
        return $payment_key !== null ? $payment_key : null;
    }
    return null;
}

function GetcurrentCurrency()
{

    $currency = Currency::where('is_primary', 1)->first();

    $currency_code = $currency ? strtolower($currency->currency_code) : 'usd';
    return $currency_code;
}

function GetCurrencySymbol()
{
    $currency = Currency::where('is_primary', 1)->first();
    // $currency_code = $currency ? strtolower($currency->currency_symbol) : '$';
    return [
        'code' => $currency?->currency_code ?? 'USD', // e.g., DZD
        'symbol' => $currency?->currency_symbol ?? '$', // e.g., د.ج
    ];
}

function DateFormate($date)
{
    if ($date == null) {
        return null;
    }
    $datetime = new \DateTime($date);

    $la_time = new \DateTimeZone(setting('default_time_zone') ?? 'UTC');
    $datetime->setTimezone($la_time);
    $newDate = $datetime->format(setting('date_formate') ?? 'Y-m-d');

    return $newDate;
}

if (!function_exists('exportSheetHeader')) {
    /**
 * Get localized payment status text
 *
 * @param int $status Payment status value (0, 1, 2, etc.)
 * @param string $locale Optional locale override
 * @return string Localized payment status text
 */
if (!function_exists('getLocalizedPaymentStatus')) {
    function getLocalizedPaymentStatus($status, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        // Define payment status mapping
        $statusMap = [
            0 => 'payment_status_pending',
            1 => 'payment_status_paid',
            2 => 'payment_status_failed',
            3 => 'payment_status_refunded',
            4 => 'payment_status_partially_paid',
            5 => 'payment_status_advance_paid',
        ];

        $translationKey = $statusMap[$status] ?? 'payment_status_pending';

        return __("appointment.{$translationKey}", [], $locale);
    }
}

/**
 * Get all payment statuses with localized names
 *
 * @param string $locale Optional locale override
 * @return array Array of payment statuses with localized names
 */
if (!function_exists('getLocalizedPaymentStatuses')) {
    function getLocalizedPaymentStatuses($locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        $statuses = [
            ['value' => 0, 'name' => getLocalizedPaymentStatus(0, $locale)],
            ['value' => 1, 'name' => getLocalizedPaymentStatus(1, $locale)],
            ['value' => 2, 'name' => getLocalizedPaymentStatus(2, $locale)],
            ['value' => 3, 'name' => getLocalizedPaymentStatus(3, $locale)],
            ['value' => 4, 'name' => getLocalizedPaymentStatus(4, $locale)],
            ['value' => 5, 'name' => getLocalizedPaymentStatus(5, $locale)],
        ];

        return $statuses;
    }
}

function exportSheetHeader($moduleName, $columns, $dateRange)
    {
        return function ($event) use ($moduleName, $columns, $dateRange) {
            $sheet = $event->sheet->getDelegate();
            $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($columns));

            // Logo logic - simplified for better compatibility with ODS/XLS
            $logoSetting = \App\Models\Setting::where('name', 'logo')->first();
            $logoPath = $logoSetting ? $logoSetting->val : null;

            if ($logoPath) {
                try {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');

                if (str_starts_with($logoPath, url('/storage'))) {
                    $publicStoragePath = str_replace(
                        url('/storage'),
                        storage_path('app/public'),
                        $logoPath
                    );
                    if (file_exists($publicStoragePath)) {
                        $drawing->setPath($publicStoragePath);
                    }
                } elseif (filter_var($logoPath, FILTER_VALIDATE_URL)) {
                    $logoContent = @file_get_contents($logoPath);
                    if ($logoContent !== false) {
                        $tmpLogo = sys_get_temp_dir() . '/logo_' . uniqid() . '.png';
                        file_put_contents($tmpLogo, $logoContent);
                        $drawing->setPath($tmpLogo);
                    }
                } else {
                    $fullPath = public_path($logoPath);
                    if (file_exists($fullPath)) {
                        $drawing->setPath($fullPath);
                    }
                }

                    if ($drawing->getPath() && file_exists($drawing->getPath())) {
                    $drawing->setHeight(40);
                    $drawing->setCoordinates('A1');
                    $drawing->setWorksheet($sheet);
                    }
                } catch (\Exception $e) {
                    // Skip logo if there's an error to prevent export failure
                    \Log::warning('Logo export failed: ' . $e->getMessage());
                }
            }

            // Adjust row height for logo and text
            $sheet->getRowDimension(1)->setRowHeight(45);
            $sheet->getRowDimension(2)->setRowHeight(25);

            // Module name
            $sheet->setCellValue('B1', $moduleName);
            $sheet->mergeCells("B1:{$lastColumn}1");
            $sheet->getStyle("B1:{$lastColumn}1")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("B1:{$lastColumn}1")->getAlignment()->setVertical('center')->setHorizontal('center');

            // Date range
            if (!empty($dateRange) && count($dateRange) === 2) {
                $sheet->setCellValue('A2', "From Date: {$dateRange[0]}   |   To Date: {$dateRange[1]}");
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->getStyle("A2:{$lastColumn}2")->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle("A2:{$lastColumn}2")->getAlignment()->setVertical('center')->setHorizontal('center');
            }
        };
    }
}
