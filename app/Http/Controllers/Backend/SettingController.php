<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BodyChartSetting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Socialite;
use Google\Client;
use Google\Service\Oauth2;
use Google_Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
class SettingController extends Controller
{
    public function __construct()
    {
        // Page Title
        $this->module_title = 'settings.title';

        // module name
        $this->module_name = 'settings';

        // module icon
        $this->module_icon = 'fas fa-cogs';

        view()->share([
            'module_title' => $this->module_title,
            'module_name' => $this->module_name,
            'module_icon' => $this->module_icon,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $module_action = 'List';

        return view('backend.settings.index', compact('module_action'));
    }

    public function index_data(Request $request)
    {
        if (!isset($request->fields)) {
            return response()->json($data, 404);
        }
        $fields = explode(',', $request->fields);
        $data = Setting::whereIn('name', $fields)->get();

        $responseData = [];
        foreach ($data as $setting) {
            $field = $setting->name;
            $value = $setting->val;

            // Process specific fields, like asset URLs
            if (in_array($field, ['logo', 'mini_logo', 'dark_logo', 'dark_mini_logo', 'favicon'])) {
                $value = asset($value);
            }

            $responseData[$field] = $value;
        }
        $responseData['clinic_booking_url'] = route('app.quick-booking');
        return response()->json($responseData, 200);
    }

    public function store(Request $request)
    {
        if($request->body_chart_images){

            $this->bodychartSetting($request);
        }
        $data = $request->all();
        if ($request->has('json_file')) {
            $file = $request->file('json_file');
            $fileName = $file->getClientOriginalName();
            $directoryPath = storage_path('app/data');

            if (!File::isDirectory($directoryPath)) {
                File::makeDirectory($directoryPath, 0777, true, true);
            }

            $files = File::files($directoryPath);

            foreach ($files as $existingFile) {
                if (strtolower($existingFile->getExtension()) === 'json') {
                    File::delete($existingFile->getPathname());
                }
            }
            $file->move($directoryPath, $fileName);
        }

        unset($data['json_file']);
        if ($request->wantsJson()) {
            $rules = Setting::getSelectedValidationRules(array_keys($request->all()));
        } else {
            $rules = Setting::getValidationRules();
        }

        $data = $this->validate($request, $rules);

        $validSettings = array_keys($rules);

        foreach ($data as $key => $val) {
            if (in_array($key, $validSettings)) {
                $mimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/vnd.microsoft.icon'];
                if (gettype($val) == 'object') {
                    if ($val->getType() == 'file' && in_array($val->getmimeType(), $mimeTypes)) {
                        $setting = Setting::add($key, '', Setting::getDataType($key), Setting::getType($key));
                        $mediaItems = $setting->addMedia($val)->toMediaCollection($key);
                        $setting->update(['val' => $mediaItems->getUrl()]);
                    }
                } else {
                    $setting = Setting::add($key, $val, Setting::getDataType($key), Setting::getType($key));
                }
            }
        }

        // Set app locale if default_language is present
        if (isset($data['default_language'])) {
            $locale = $data['default_language'];
            app()->call('App\\Http\\Controllers\\LanguageController@switch', ['language' => $locale]);
        }
        if ($request->wantsJson()) {
            $message = __('settings.save_setting');

            return response()->json(['message' => $message, 'status' => true], 200);
        } else {
            return redirect()->back()->with('status', __('messages.setting_save'));
        }
    }

    public function clear_cache()
    {
        Setting::flushCache();

        $message = __('messages.cache_cleard');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    // public function verify_email(Request $request)
    // {
    //     $mailObject = $request->all();
    //     try {
    //         \Config::set('mail', $mailObject);
    //         Mail::raw('This is a smtp mail varification test mail!', function ($message) use ($mailObject) {
    //             $message->to($mailObject['email'])->subject('Test Email');
    //         });

    //         return response()->json(['message' => 'Verification Successful', 'status' => true], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Verification Failed', 'status' => false], 500);
    //     }
    // }

    public function verify_email(Request $request)
{
    $mailObject = $request->all();
    
    try {
        // Set mail configuration properly
        config([
            'mail.mailers.smtp.host' => $mailObject['mail_host'],
            'mail.mailers.smtp.port' => $mailObject['mail_port'],
            'mail.mailers.smtp.encryption' => $mailObject['mail_encryption'],
            'mail.mailers.smtp.username' => $mailObject['mail_username'],
            'mail.mailers.smtp.password' => $mailObject['password'],
            'mail.from.address' => $mailObject['mail_from'],
            'mail.from.name' => $mailObject['from_name'],
        ]);

        Mail::raw('This is a smtp mail verification test mail!', function ($message) use ($mailObject) {
            $message->to($mailObject['email'])->subject('Test Email');
        });

        return response()->json(['message' => 'Verification Successful', 'status' => true], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Verification Failed: ' . $e->getMessage(), 
            'status' => false
        ], 500);
    }
}

    public function googleKey(Request $request)
    {
        $googleMeetVal = Setting::where('name', 'google_meet_method')->first()?->val ?? null;
        $isZoomVal = Setting::where('name', 'is_zoom')->first()?->val ?? null;

        $data = ($googleMeetVal == 1 || $isZoomVal == 1) ? 1 : 0;
        return response()->json($data);
    }


    public function bodychartSetting($bodychartData)
    {
        $jsonData = $bodychartData->body_chart_images;
        $data = json_decode($jsonData, true);

        $existingIds = collect($data)->pluck('uniqueId')->filter();
        if ($existingIds->isNotEmpty()) {
            $deletedata = BodyChartSetting::whereNotIn('uniqueId', $existingIds);
            $deletedata->delete();
            //$deletedata->clearMediaCollection('bodychart_template');
        }
        if (empty($data)) {
            BodyChartSetting::truncate();
        }
        foreach ($data as $index => $item) {
            if (isset($item['uniqueId'])) {
                $existingBodyChart = BodyChartSetting::where('uniqueId', $item['uniqueId'])->first();
            }
            if ($existingBodyChart) {
                $existingBodyChart->name = $item['name'];
                $existingBodyChart->default = $item['default'];
                $existingBodyChart->image_name=$item['image_name'];
                $existingBodyChart->save();
                if ($bodychartData->hasFile($index)) {
                    $existingBodyChart->clearMediaCollection('bodychart_template');
                    $existingBodyChart->addMedia($bodychartData->$index)->toMediaCollection('bodychart_template');
                    $existingBodyChart->full_url = $existingBodyChart->getFirstMediaUrl('bodychart_template');
                    $existingBodyChart->save();
                }
            } else {
                $bodyChart = BodyChartSetting::create([
                    'name' => $item['name'],
                    'default' => $item['default'],
                    'uniqueId' => $item['uniqueId'],
                    'image_name'=>$item['image_name'],
                ]);
                if ($bodychartData->hasFile($index)) {
                $bodyChart->addMedia($bodychartData->$index)->toMediaCollection('bodychart_template');
                $bodyChart->full_url = $bodyChart->getFirstMediaUrl('bodychart_template');
                $bodyChart->save();
                }
            }
        }
    }
    public function googleId(Request $request)
    {
        $setting = Setting::where('type', 'google_meet_method')->where('name', 'google_clientid')->first();
        $client = new Client([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'redirect_uri' => env('GOOGLE_REDIRECT'),
            'scopes' => 'https://www.googleapis.com/auth/contacts', // Add required scopes here
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        ]);
        $authUrl = $client->createAuthUrl();
        return response()->json($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {

        $client = new Google_Client();

        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT'));
        $client->addScope('https://www.googleapis.com/auth/userinfo.email');

        if (!$request->filled('code')) {
            // Redirect the user to Google's authorization endpoint
            return redirect()->to($client->createAuthUrl());
        }

        // Exchange authorization code for access token
        $client->authenticate($request->input('code'));
        $accessToken = $client->getAccessToken();

        // Extract access token and expiration time
        $token = $accessToken['access_token'];
        $expiresIn = $accessToken['expires_in'];

        // Calculate expiration timestamp
        $expiresAt = Carbon::createFromTimestamp($expiresIn)->toDateTimeString();

        // Store the access token and expiration timestamp in the user's table
        $user = Auth::user(); // Assuming you're using Laravel's authentication
        $user->google_access_token = $token;
        $user->expires_in = $expiresAt;
        $user->save();

        return response()->json($accessToken);
    }

    public function storeToken(Request $request)
    {
        $user = auth()->user(); // Assuming the user is authenticated
        $user->google_access_token = json_encode($request->all()); // Convert array to JSON
        $user->is_telmet = $request->is_telmet;
        $user->save();

        return response()->json(['message' => 'Token stored successfully']);
    }

    public function revokeToken(Request $request)
    {
        $user = auth()->user(); // Assuming the user is authenticated
        $user->google_access_token = null;
        $user->is_telmet = 0;
        $user->save();

        return response()->json(['message' => 'Logged out successfully']);
    }
    public function downloadJson(){
        $directory = 'data';
        $files = Storage::files($directory);
        $jsonFiles = array_map(function($file) {
            return pathinfo($file, PATHINFO_BASENAME);
        }, array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'json';
        }));
        return response()->json($jsonFiles);
    }

}
