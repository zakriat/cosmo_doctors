<?php

namespace App\Http\Controllers\Auth\Trait;

use App\Events\Auth\UserLoginSuccess;
use App\Events\Frontend\UserRegistered;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Modules\Service\Models\SystemService;
use Modules\Commission\Models\EmployeeCommission;
use Modules\Commission\Models\Commission;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\DoctorClinicMapping;
use Modules\Clinic\Models\Receptionist;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\DoctorSession;
use Modules\Wallet\Models\Wallet;

trait AuthTrait
{
    protected function loginTrait($request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember_me');

        if (Auth::attempt($credentials + ['status' => 1], $remember)) {
            $user = auth()->user();

            if ($user->roles()->count() == 1) {
                if (multiVendor() == "0") {
                    if ($user->hasRole('vendor') || $user->hasRole('doctor') || $user->hasRole('receptionist')) {
                        $role =  $user->user_type;
                        if ($role == 'vendor' || ($role == 'doctor' && optional($user->doctor)->vendor->user_type == 'vendor') || ($role == 'receptionist' && optional($user->receptionist)->vendor->user_type == 'vendor')) {
                            Auth::logout();

                            return ['status' => 406, 'message' => __('messages.account_deactivated')];
                        }
                    }
                }

                if($user->hasRole('vendor') || $user->hasRole('doctor') || $user->hasRole('receptionist')){

                    if($user->email_verified_at == null) {

                        Auth::logout();

                       return ['status' => 406, 'message' => __('messages.account_not_verify')];

                    }

                }
                if($user->hasRole('user')){

                        Auth::logout();
                       return ['status' => 406, 'message' => 'Unauthorized role & The provided credentials do not match our records'];
                }

                event(new UserLoginSuccess($request, auth()->user()));

                return ['status' => 200, 'message' => 'Login successful!'];
            }
        }

        return ['status' => 401, 'error' => __('auth.failed')];
    }

    protected function registerTrait($request, $model = null)
    {

        $validator = validator($request->all(),[
            'first_name' => ['required', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'mobile' => ['required', 'string', 'max:15', 'unique:users'],
        ]);



        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => $validator->errors()->first(),
            ], 422);
        }
        // $emailExists = User::where('email', $request->email)->exists();
        // if ($emailExists) {
        //     return response()->json(['message' => 'Email is already in use.'], 422);
        // }
        $arr = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'gender' => $request->gender,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'date_of_birth' => $request->date_of_birth,
        ];

        if (isset($model)) {
            $user = $model::create($arr);
        } else {
            $user = User::create($arr);
        }


        $usertype = $user->user_type;

        $user->assignRole($usertype);

        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        $user->save();

        if($user->user_type == 'user'){

            /* generate google authenticator qr code  */
            $user->qr_image = self::multiFactorAuth($user);
            $wallet = [
                'title' => $user->first_name.' '.$user->last_name,
                'user_id' => $user->id,
                'amount' => 0
            ];
            Wallet::create($wallet);
        }

        if ($usertype == "doctor" || $usertype == "vendor") {
            $commissions = Commission::where('type', 'service')->get();
            foreach ($commissions as $commission) {
                EmployeeCommission::create([
                    'employee_id' => $user->id,
                    'commission_id' => $commission->id,
                ]);
            }
        }

        if ($request->has('clinic_id') && $request->clinic_id != '') {
            $days = [
                ['day' => 'monday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'tuesday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'wednesday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'thursday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'friday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'saturday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'sunday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => true, 'breaks' => []],
            ];

            $clinic_data = Clinics::where('id',$request->clinic_id)->first();
            $vendor_id = $clinic_data->vendor_id;

            if ($usertype == "doctor") {

                $data = [
                    'clinic_id' => $request->clinic_id,
                    'doctor_id' => $user->id,
                ];

                DoctorClinicMapping::updateOrCreate(
                    [
                        'clinic_id' => $request->clinic_id,
                        'doctor_id' => $user->id,
                    ],
                    $data
                );

                foreach ($days as $key => $val) {

                    $val['clinic_id'] = $request->clinic_id;
                    $val['doctor_id'] =  $user->id;

                    DoctorSession::create($val);
                }
                if($clinic_data) {

                    $doctor_data = [
                        'vendor_id' => $vendor_id,
                        'doctor_id' => $user->id, // Corrected typo from $usre_id to $user->id
                    ];

                    Doctor::updateOrCreate(
                        [
                            'doctor_id' => $user->id,
                        ],
                        $doctor_data
                    );
                }
            }


            if ($usertype == "receptionist") {

                $receptionist_data=[

                    'clinic_id'=>$request->clinic_id,
                    'receptionist_id'=>$user->id,
                    'vendor_id'=>$vendor_id,

                ];

                Receptionist::updateOrCreate(
                    [
                        'receptionist_id' => $user->id,
                    ],
                    $receptionist_data
                );


            }

        }


        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('config:cache');

        // event(new Registered($user));
        // event(new UserRegistered($user));

        return $user;
    }

    /**
     * generate google authenticator qr code
     * @param $userData
     */
    protected function multiFactorAuth($userData)
    {
        $google2fa = app('pragmarx.google2fa');
        $google2fa_secret = $google2fa->generateSecretKey();

        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $userData->email,
            $google2fa_secret
        );

        User::where('id',$userData->id)->update([
            'google2fa_secret' => $google2fa_secret
        ]);


        return $QR_Image;

    }
}
