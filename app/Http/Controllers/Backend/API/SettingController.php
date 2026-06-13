<?php

namespace App\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Modules\Currency\Models\Currency;
use App\Models\User;
use Modules\Tax\Models\Tax;
use Modules\Tax\Models\TaxService;
use Modules\Tax\Transformers\TaxResource;

class SettingController extends Controller
{
    public function appConfiguraton(Request $request)
    {
        $settings = Setting::all()->pluck('val', 'name');

        $currencies = Currency::all();
        $response = [];

        // Define the specific names you want to include
        $specificNames = ['app_name', 'footer_text', 'primary', 'razorpay_secretkey', 'razorpay_publickey', 'stripe_secretkey', 'stripe_publickey', 'paystack_secretkey', 'paystack_publickey', 'paypal_secretkey', 'paypal_clientid', 'flutterwave_secretkey', 'flutterwave_publickey', 'onesignal_app_id', 'onesignal_rest_api_key', 'onesignal_channel_id', 'google_maps_key', 'helpline_number', 'copyright', 'inquriy_email', 'site_description', 'patient_app_play_store', 'patient_app_app_store', 'clinicadmin_app_play_store', 'clinicadmin_app_app_store', 'isForceUpdate', 'version_code', 'account_id', 'client_id', 'client_secret', 'airtel_secretkey', 'airtel_clientid', 'phonepay_app_id', 'phonepay_merchant_id', 'phonepay_salt_key', 'phonepay_salt_index', 'midtrans_clientid', 'cinet_siteid', 'cinet_apikey', 'cinet_secretkey', 'sadad_id', 'sadad_key', 'sadad_domain'];
        foreach ($settings as $name => $value) {
            if (in_array($name, $specificNames)) {
                if (strpos($name, 'onesignal_') === 0 && $request->is_authenticated == 1) {
                    $nestedKey = 'onesignal_customer_app';
                    $nestedName = str_replace('', 'onesignal_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'patient_app_') === 0) {
                    $nestedKey = 'patient_app_url';
                    $nestedName = str_replace('', 'patient_app_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'clinicadmin_app_') === 0) {
                    $nestedKey = 'clinicadmin_app_url';
                    $nestedName = str_replace('', 'clinicadmin_app_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'razorpay_') === 0 && $request->is_authenticated == 1 && $settings['razor_payment_method'] !== null) {
                    $nestedKey = 'razor_pay';
                    $nestedName = str_replace('', 'razorpay_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'stripe_') === 0 && $request->is_authenticated == 1 && $settings['str_payment_method'] !== null) {
                    $nestedKey = 'stripe_pay';
                    $nestedName = str_replace('', 'stripe_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'paystack_') === 0 && $request->is_authenticated == 1 && $settings['paystack_payment_method'] !== null) {
                    $nestedKey = 'paystack_pay';
                    $nestedName = str_replace('', 'paystack_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'paypal_') === 0 && $request->is_authenticated == 1 && $settings['paypal_payment_method'] !== null) {
                    $nestedKey = 'paypal_pay';
                    $nestedName = str_replace('', 'paypal_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'flutterwave_') === 0 && $request->is_authenticated == 1 && $settings['flutterwave_payment_method'] !== null) {
                    $nestedKey = 'flutterwave_pay';
                    $nestedName = str_replace('', 'flutterwave_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'airtel_') === 0 && $request->is_authenticated == 1 && $settings['airtel_payment_method'] !== Null) {
                    $nestedKey = 'airtel_pay';
                    $nestedName = str_replace('', 'airtel_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;

                } elseif (strpos($name, 'phonepay_') === 0 && $request->is_authenticated == 1 && $settings['phonepay_payment_method'] !== Null) {
                    $nestedKey = 'phonepay_pay';
                    $nestedName = str_replace('', 'phonepay_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'midtrans_') === 0 && $request->is_authenticated == 1 && $settings['midtrans_payment_method'] !== Null) {
                    $nestedKey = 'midtrans_pay';
                    $nestedName = str_replace('', 'midtrans_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;

                } elseif (strpos($name, 'cinet_') === 0 && $request->is_authenticated == 1 && $settings['cinet_payment_method'] !== Null) {
                    $nestedKey = 'cinet_pay';
                    $nestedName = str_replace('', 'cinet_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;
                } elseif (strpos($name, 'sadad_') === 0 && $request->is_authenticated == 1 && $settings['sadad_payment_method'] !== Null) {
                    $nestedKey = 'sadad_pay';
                    $nestedName = str_replace('', 'sadad_', $name);
                    if (!isset($response[$nestedKey])) {
                        $response[$nestedKey] = [];
                    }
                    $response[$nestedKey][$nestedName] = $value;

                }

                if (!strpos($name, 'onesignal_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'stripe_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'razorpay_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'paystack_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'paypal_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'flutterwave_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'airtel_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'phonepay_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'midtrans_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'cinet_') === 0) {
                    $response[$name] = $value;
                } elseif (!strpos($name, 'sadad_') === 0) {
                    $response[$name] = $value;
                }
            }
        }
        // Fetch currency data
        $currencies = Currency::all();

        $currencyData = null;
        if ($currencies->isNotEmpty()) {
            $currency = $currencies->where('is_primary', 1)->first();
            $currencyData = [
                'currency_name' => $currency->currency_name,
                'currency_symbol' => $currency->currency_symbol,
                'currency_code' => $currency->currency_code,
                'currency_position' => $currency->currency_position,
                'no_of_decimal' => $currency->no_of_decimal,
                'thousand_separator' => $currency->thousand_separator,
                'decimal_separator' => $currency->decimal_separator,
            ];
        }

        $taxes = Tax::active()->whereNull('module_type')->orWhere('module_type', 'services')->where('status', 1)->get();

        $taxesData = $taxes->map(function ($tax) {

            return [
                'id' => $tax->id,
                'title' => $tax->title,
                'type' => $tax->type,
                'value' => $tax->value,
                'status' => $tax->status,
                'module_type' => $tax->module_type,
                "tax_scope" => $tax->tax_type,

            ];
        })->toArray();


        $requiredSettings = ['isForceUpdateforAndroid', 'patient_android_min_force_update_code', 'patient_android_latest_version_update_code', 'clinicadmin_android_min_force_update_code', 'clinicadmin_android_latest_version_update_code'];

        foreach ($requiredSettings as $setting) {
            if (isset($settings[$setting])) {
                $response[$setting] = intval($settings[$setting]);
            } else {
                $response[$setting] = 0;
            }
        }

        $requiredSettings = ['isForceUpdateforIos', 'patient_ios_min_force_update_code', 'patient_ios_latest_version_update_code', 'clinicadmin_ios_min_force_update_code', 'clinicadmin_ios_latest_version_update_code'];

        foreach ($requiredSettings as $setting) {
            if (isset($settings[$setting])) {
                $response[$setting] = intval($settings[$setting]);
            } else {
                $response[$setting] = 0;
            }
        }

        $response['tax'] = $taxesData;
        $response['currency'] = $currencyData;
        $response['google_login_status'] = isset($settings['is_google_login']) ? (int)$settings['is_google_login'] : 0;
        $response['apple_login_status'] = 'false';
        $response['otp_login_status'] = 'false';
        $response['site_description'] = $settings['site_description'] ?? null;
        $response['is_quick_booking_on'] = isset($settings['clinic']) ? (int) $settings['clinic'] : null;
        // Add locale language to the response
        $response['application_language'] = $settings['default_language'] ?? 'en';

        $response['view_patient_soap'] = isset($settings['view_patient_soap']) ? intval($settings['view_patient_soap']) : 0;
        $response['is_body_chart'] = isset($settings['is_body_chart']) ? intval($settings['is_body_chart']) : 0;
        $response['is_telemed_setting'] = isset($settings['is_telemed_setting']) ? intval($settings['is_telemed_setting']) : 0;
        $response['is_multi_vendor'] = isset($settings['is_multi_vendor']) ? intval($settings['is_multi_vendor']) : 0;
        $response['is_encounter_problem'] = isset($settings['is_encounter_problem']) ? intval($settings['is_encounter_problem']) : 0;
        $response['is_encounter_observation'] = isset($settings['is_encounter_observation']) ? intval($settings['is_encounter_observation']) : 0;
        $response['is_encounter_note'] = isset($settings['is_encounter_note']) ? intval($settings['is_encounter_note']) : 0;
        $response['is_encounter_prescription'] = isset($settings['is_encounter_prescription']) ? intval($settings['is_encounter_prescription']) : 0;

        $response['status'] = true;
        $response['cancellation_type'] = isset($settings['cancellation_type']) ? $settings['cancellation_type'] : '';
        $response['cancellation_charge'] = isset($settings['cancellation_charge']) ? (double)$settings['cancellation_charge'] : 0;
        $response['cancellation_charge_hours'] = isset($settings['cancellation_charge_hours']) ? (int) $settings['cancellation_charge_hours'] : 0;
        $response['is_cancellation_charge'] = isset($settings['is_cancellation_charge']) ? intval($settings['is_cancellation_charge']) : 0;
        $response['is_dummy_credentials'] = isset($settings['is_dummy_credentials']) ? intval($settings['is_dummy_credentials']) : 0;
        return response()->json($response);
    }

    public function Configuraton(Request $request)
    {
        $googleMeetSettings = Setting::whereIn('name', ['google_meet_method', 'google_clientid', 'google_secret_key'])
            ->pluck('val', 'name');
        $settings = $googleMeetSettings->toArray();
        return $settings;
    }


}
