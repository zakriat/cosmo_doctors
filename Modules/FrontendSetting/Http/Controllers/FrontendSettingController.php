<?php

namespace Modules\FrontendSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\FrontendSetting\Models\FrontendSetting;
use Modules\Setting\Models\Setting;
use App\Models\User;


class FrontendSettingController extends Controller
{
    public function __construct()
    {
        // Page Title
        $this->module_title = 'frontend.title';


        view()->share([
            'module_title' => $this->module_title,
        ]);
    }
    public function frontendSettings(Request $request)
    {
        $auth_user = auth()->user();

        // Restrict access to vendors
        if ($auth_user->hasRole('vendor')) {
            abort(403, 'Access denied. Vendors are not allowed to access frontend settings.');
        }

        $pageTitle = __('messages.frontend_setting');
        $page = $request->page;

        if ($page == '') {
            if ($auth_user->hasAnyRole(['admin', 'demo_admin'])) {
                $page = 'landing-page-setting';
            }
        }

        return view('frontendsetting::index', compact('page', 'pageTitle', 'auth_user'));
    }

    public function layoutPage(Request $request)
    {
        $auth_user = auth()->user();
        
        // Restrict access to vendors
        if ($auth_user->hasRole('vendor')) {
            abort(403, 'Access denied. Vendors are not allowed to access frontend settings.');
        }
        
        $page = $request->page;
        $user_id = $auth_user->id;

        // $settings = AppSetting::firstOrNew();
        $user_data = User::find($user_id);
        $tabpage = '';

        $landing_page_data = FrontendSetting::where('type', $page)->first();

        if (!empty($landing_page_data['value'])) {
            $decodedata = json_decode($landing_page_data['value']);

            switch ($page) {
                case 'landing-page-setting':
                    $tabpage = 'section_1';
                    break;
                case 'heder-menu-setting':
                    // Include all supported toggles, defaulting to 0 when absent
                    $keys = [
                        'header_setting',
                        'categories',
                        'services',
                        'clinics',
                        'doctors',
                        'appointments',
                        'incidence',
                        'enable_search',
                        'enable_language',
                        'enable_darknight_mode',
                    ];
                    foreach ($keys as $key) {
                        $landing_page_data[$key] = isset($decodedata->$key) ? $decodedata->$key : 0;
                    }
                    break;
                case 'footer-setting':
                    $keys = ['footer_setting', 'enable_quick_link', 'enable_top_service', 'service_id', 'enable_top_category', 'category_id'];
                    foreach ($keys as $key) {
                        $landing_page_data[$key] = $decodedata->$key;
                    }
                    break;
                case 'login-register-setting':
                    $keys = ['login_register', 'title', 'description'];
                    foreach ($keys as $key) {
                        $landing_page_data[$key] = $decodedata->$key;
                    }
                    break;
                default:
                    // Additional logic for default case if needed
                    break;
            }
        }

        $data = view('frontendsetting::' . $page, compact('landing_page_data', 'page', 'tabpage', 'user_data'))->render();
        return response()->json($data);
    }
    public function landingLayoutPage(Request $request)
    {
        $auth_user = auth()->user();
        
        // Restrict access to vendors
        if ($auth_user->hasRole('vendor')) {
            abort(403, 'Access denied. Vendors are not allowed to access frontend settings.');
        }
        
        $tabpage = $request->tabpage;
        $user_id = $auth_user->id;
        $user_data = User::find($user_id);
        $landing_page = FrontendSetting::where('key', $tabpage)->first();

        switch ($tabpage) {
            case 'section_1':
                if (!empty($landing_page['value'])) {
                    $decodedata = json_decode($landing_page['value']);
                    $landing_page['section_1'] = $decodedata->section_1;
                    $landing_page['title'] = $decodedata->title;
                    $landing_page['enable_search'] = $decodedata->enable_search;
                    $landing_page['instant_link'] = $decodedata->instant_link;
                    $landing_page['enable_quick_booking'] = $decodedata->enable_quick_booking;
                }
                $data = view('frontendsetting::sections.' . $tabpage, compact('user_data', 'tabpage', 'landing_page'))->render();

                break;

            case 'section_2':
                if (!empty($landing_page['value'])) {
                    $decodedata = json_decode($landing_page['value']);
                }

                $data = view('frontendsetting::sections.' . $tabpage, compact('user_data', 'tabpage', 'landing_page'))->render();

                break;

            case 'section_3':
                if (!empty($landing_page['value'])) {
                    $decodedata = json_decode($landing_page['value']);
                    $landing_page['section_3'] = $decodedata->section_3;
                    $landing_page['title'] = $decodedata->title;
                    $landing_page['service_id'] = $decodedata->service_id;

                }
                $data = view('frontendsetting::sections.' . $tabpage, compact('user_data', 'tabpage', 'landing_page'))->render();
                break;

            case 'section_4':
                if (!empty($landing_page['value'])) {

                    $decodedata = json_decode($landing_page['value']);
                    $landing_page['section_4'] = $decodedata->section_4;
                    $landing_page['title'] = $decodedata->title;
                    $landing_page['description'] = $decodedata->description ?? null;
                }
                $data = view('frontendsetting::sections.' . $tabpage, compact('user_data', 'tabpage', 'landing_page'))->render();
                break;

            case 'section_5':
                if (!empty($landing_page['value'])) {
                    $decodedata = json_decode($landing_page['value']);
                    $landing_page['section_5'] = $decodedata->section_5;
                    $landing_page['title'] = $decodedata->title;
                    $landing_page['subtitle'] = $decodedata->subtitle;
                    $landing_page['clinic_id'] = $decodedata->clinic_id;

                }
                $data = view('frontendsetting::sections.' . $tabpage, compact('user_data', 'tabpage', 'landing_page'))->render();
                break;

            case 'section_6':
                if (!empty($landing_page['value'])) {
                    $decodedata = json_decode($landing_page['value']);
                    $landing_page['section_6'] = $decodedata->section_6;
                    $landing_page['title'] = $decodedata->title;
                    $landing_page['subtitle'] = $decodedata->subtitle;
                    $landing_page['doctor_id'] = $decodedata->doctor_id;

                }
                $data = view('frontendsetting::sections.' . $tabpage, compact('user_data', 'tabpage', 'landing_page'))->render();
                break;

            case 'section_7':
                if (!empty($landing_page['value'])) {
                    $decodedata = json_decode($landing_page['value']);

                }
                $data = view('frontendsetting::sections.' . $tabpage, compact('user_data', 'tabpage', 'landing_page'))->render();

                break;
            case 'section_8':
                if (!empty($landing_page['value'])) {
                    $decodedata = json_decode($landing_page['value']);
                }
                $data = view('frontendsetting::sections.' . $tabpage, compact('user_data', 'tabpage', 'landing_page'))->render();

                break;
            case 'section_9':
                if (!empty($landing_page['value'])) {
                    $decodedata = json_decode($landing_page['value']);

                }
                $data = view('frontendsetting::sections.' . $tabpage, compact('user_data', 'tabpage', 'landing_page'))->render();

                break;

            default:
                $data = view('forntend-setting-landing.' . $tabpage, compact('tabpage', 'landing_page'))->render();
                break;
        }
        return response()->json($data);
    }

    public function landingpageSettingsUpdates(Request $request)
    {
        $auth_user = auth()->user();
        
        // Restrict access to vendors
        if ($auth_user->hasRole('vendor')) {
            abort(403, 'Access denied. Vendors are not allowed to access frontend settings.');
        }
        
        $data = $request->all();
        $page = $request->page;
        $type = $request->type;

        $status = isset($data['status']) && $data['status'] == 'on' ? 1 : 0;

        $configurations = [
            'section_1' => ['enable_search', 'instant_link', 'enable_quick_booking'],
            'section_2' => ['category_id'],
            'section_3' => ['service_id'],
            'section_4' => [],
            'section_5' => ['clinic_id'],
            'section_6' => ['doctor_id'],
            'section_7' => ['title', 'subtitle', 'decription'],
            'section_4' => [],
            'section_9' => ['blog_id'],
        ];

        $landing_page_data = [
            $type => $status,
        ];
        if (!empty($data['title'])) {
            $landing_page_data['title'] = $data['title'];
        }
        if (!empty($data['subtitle'])) {
            $landing_page_data['subtitle'] = $data['subtitle'];
        }
        if (!empty($data['description'])) {
            $landing_page_data['description'] = $data['description'];
        }
        foreach ($configurations[$type] ?? [] as $field) {
            $landing_page_data[$field] = isset($data[$field]) ? $data[$field] : [];
        }

        $res = FrontendSetting::updateOrCreate(['id' => $request->id], [
            'type' => 'landing-page-setting',
            'key' => $type,
            'status' => $status,
            'value' => json_encode($landing_page_data),
        ]);

        if ($type == 'section_4') {
            if ($request->hasFile('main_image')) {
                storeMediaFile($res, $request->main_image, 'main_image');
            }

            if ($request->hasFile('google_play')) {
                storeMediaFile($res, $request->google_play, 'google_play');
            }

            if ($request->hasFile('app_store')) {
                storeMediaFile($res, $request->app_store, 'app_store');
            }
        }

        return redirect()->route('frontend_setting.index', ['page' => $page, 'tabpage' => $type])->withSuccess(__('messages.landing_page_settings') . ' ' . __('messages.updated'));
    }

    public function getLandingLayoutPageConfig(Request $request)
    {
        $mode = $request->type;
        $page = 'landing-page-setting';
        $select = 'value';


        $landing_page = FrontendSetting::select('id', 'key', $select, 'status', 'type')->where('key', $mode)->first();
        $landing_page['key'] = $mode;


        return response()->json(['success' => 'Ajax request submitted successfully', 'data' => $landing_page]);
    }



    public function landingPageLayout(Request $request)
    {
        $page = 'landing-page-setting';
        $tabpage = $request->query('tabpage', 'section_1');

        $settings = FrontendSetting::whereIn('key', ['landing_page'])->get();
        $user_data = auth()->user();

        // Initialize landing_page array
        $landing_page = [];

        // Map settings data to landing_page array
        foreach ($settings as $setting) {
            $landing_page[$setting->key] = $setting->value;
        }

        return view('frontendsetting::pages.landingpage', compact('page', 'tabpage'));
    }

    public function headingpagesettings(Request $request)
    {
        $auth_user = auth()->user();
        
        // Restrict access to vendors
        if ($auth_user->hasRole('vendor')) {
            abort(403, 'Access denied. Vendors are not allowed to access frontend settings.');
        }
        
        $data = $request->all();
        $page = 'heder-menu-setting';
        $message = trans('messages.failed');
        $order = array_diff_key($data, array_flip(['_token', 'id', 'type', 'status', 'active_tab']));
        $status = (isset($data['status']) && $data['status'] == 'on') ? 1 : 0;
        $header_setting_data = [
            'header_setting' => $status,
            'enable_search' => (isset($data['enable_search']) && $data['enable_search'] == 'on') ? 1 : 0,
            'enable_language' => (isset($data['enable_language']) && $data['enable_language'] == 'on') ? 1 : 0,
            'enable_darknight_mode' => (isset($data['enable_darknight_mode']) && $data['enable_darknight_mode'] == 'on') ? 1 : 0,
        ];
        foreach ($order as $item => $value) {
            $header_setting_data[$item] = ($value == 'on') ? 1 : 0;
        }
        $res = FrontendSetting::updateOrCreate(
            ['id' => $request->id],
            ['type' => 'heder-menu-setting', 'key' => 'heder-menu-setting', 'status' => $status, 'value' => json_encode($header_setting_data)]
        );

        if ($res) {
            $message = trans('messages.update_form', ['form' => trans('messages.heder-menu-setting')]);
        }

        return redirect()->route('frontend_setting.index', ['page' => $page])->withSuccess(__('messages.header_menu_settings') . ' ' . __('messages.updated'));
    }

    public function footerpagesettings(Request $request)
    {
        $auth_user = auth()->user();
        
        // Restrict access to vendors
        if ($auth_user->hasRole('vendor')) {
            abort(403, 'Access denied. Vendors are not allowed to access frontend settings.');
        }
        
        $data = $request->all();
        $page = 'footer-setting';
        $message = trans('messages.failed');
        $order = array_diff_key($data, array_flip(['_token', 'id', 'type', 'status', 'active_tab']));
        $status = (isset($data['status']) && $data['status'] == 'on') ? 1 : 0;
        $footer_setting_data = [
            'footer_setting' => $status,
            'enable_quick_link' => (isset($data['enable_quick_link']) && $data['enable_quick_link'] == 'on') ? 1 : 0,
            'enable_top_category' => (isset($data['enable_top_category']) && $data['enable_top_category'] == 'on') ? 1 : 0,
            'category_id' => isset($data['category_id']) ? $data['category_id'] : [],
            'enable_top_service' => (isset($data['enable_top_service']) && $data['enable_top_service'] == 'on') ? 1 : 0,
            'service_id' => isset($data['service_id']) ? $data['service_id'] : [],
        ];

        $res = FrontendSetting::updateOrCreate(
            ['id' => $request->id],
            ['type' => 'footer-setting', 'key' => 'footer-setting', 'status' => $status, 'value' => json_encode($footer_setting_data)]
        );

        if ($res) {
            $message = trans('messages.update_form', ['form' => trans('messages.footer-setting')]);
        }

        return redirect()->route('frontend_setting.index', ['page' => $page])->withSuccess(__('messages.footer_settings') . ' ' . __('messages.updated'));
    }

    public function footerPage(Request $request)
    {
        $page = 'footer-setting';
        return view('frontendsetting::pages.footer_page', compact('page'));
    }

}
