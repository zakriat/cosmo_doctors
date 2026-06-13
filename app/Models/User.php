<?php

namespace App\Models;

use App\Models\Presenters\UserPresenter;
use App\Models\Traits\HasHashedMediaTrait;
use App\Trait\CustomFieldsTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Commission\Models\CommissionEarning;
use Modules\Commission\Models\EmployeeCommission;
use Modules\Employee\Models\ServiceProviderEmployee;
use Modules\Employee\Models\EmployeeRating;
use Modules\Service\Models\ServiceEmployee;
use Modules\Blog\Models\Blog;
use App\Models\Nurse;
use Modules\Subscriptions\Models\Subscription;
use Modules\Tip\Models\TipEarning;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\DoctorServiceMapping;
use Modules\Clinic\Models\DoctorDocument;
use Modules\Clinic\Models\DoctorClinicMapping;
use Modules\Appointment\Models\Appointment;
use Modules\Clinic\Models\Receptionist;
use Modules\World\Models\City;
use Modules\World\Models\State;
use Modules\World\Models\Country;
use Modules\Clinic\Models\DoctorRating;
use Modules\Clinic\Models\DoctorSession;
use Illuminate\Support\Facades\DB;
use App\Models\Traits\Auditable;


class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use HasHashedMediaTrait;
    use UserPresenter;
    use HasApiTokens;
    use CustomFieldsTrait;
    use Auditable;

    const CUSTOM_FIELD_MODEL = 'App\Models\User';

    protected $guarded = [
        'id',
        'updated_at',
        '_token',
        '_method',
        'password_confirmation',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password',
        'gender',
        'date_of_birth',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'nhs_number',
        'city_or_town',
        'county',
        'postcode',
        'status',
        'user_type',
        'username',
        'player_id',
        'web_player_id',
        'is_manager',
        'show_in_calender',
        'avatar',
        'is_banned',
        'is_subscribe',
        'last_notification_seen',
        'user_setting',
        'latitude',
        'longitude',
        'google_access_token',
        'is_telmet',
    ];

    protected $dates = [
        'deleted_at',
        'date_of_birth',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret'
    ];

    protected $casts = [
        'user_setting' => 'array',
    ];

    protected $appends = ['full_name', 'profile_image'];

    // protected function google2faSecret(): Attribute
    // {
    //     return new Attribute(
    //         get: fn ($value) =>  decrypt($value),
    //         set: fn ($value) =>  encrypt($value),
    //     );
    // }

    public function getFullNameAttribute() // notice that the attribute name is in CamelCase.
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function providers()
    {
        return $this->hasMany('App\Models\UserProvider');
    }

    /**
     * Get the list of users related to the current User.
     *
     * @return [array] roels
     */
    public function getRolesListAttribute()
    {
        return array_map('intval', $this->roles->pluck('id')->toArray());
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForSlack($notification)
    {
        return env('SLACK_NOTIFICATION_WEBHOOK');
    }

    /**
     * Get all of the service_providers for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptionPackage()
    {
        return $this->hasOne(Subscription::class, 'user_id', 'id')->where('status', config('constant.SUBSCRIPTION_STATUS.ACTIVE'));
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)->where('is_banned', 0);
    }

    public function scopeCalenderResource($query)
    {
        $query->where('show_in_calender', 1);
    }

    protected function getProfileImageAttribute()
    {
        $media = $this->getFirstMediaUrl('profile_image');

        return isset($media) && !empty($media) ? $media : asset(config('app.avatar_base_path') . 'avatar.webp');
    }

    // Employee Relations
    public function commission_earning()
    {
        return $this->hasMany(CommissionEarning::class, 'employee_id');
    }

    public function tip_earning()
    {
        return $this->hasMany(TipEarning::class, 'employee_id');
    }

    public function commissionData()
    {
        return $this->hasMany(EmployeeCommission::class, 'employee_id')->with('mainCommission');
    }

    public function service_providers()
    {
        return $this->hasMany(ServiceProviderEmployee::class, 'employee_id');
    }

    public function serviceProvider()
    {
        return $this->hasOne(ServiceProviderEmployee::class, 'employee_id')->with('getServiceprovider');
    }

    public function mainServiceProvider()
    {
        return $this->hasManyThrough(ServiceProvider::class, ServiceProviderEmployee::class, 'employee_id', 'id', 'id', 'service_provider_id');
    }

    public function services()
    {
        return $this->hasMany(ServiceEmployee::class, 'employee_id');
    }



    public function wishlist()
    {
        return $this->belongsToMany(Product::class, 'wishlist', 'user_id', 'product_id');
    }

    public function scopeEmployee($query)
    {
        $query->role('employee');
    }

    public function scopeServiceProvider($query)
    {
        $service_provider_id = request()->selected_session_service_provider_id;
        if (isset($service_provider_id)) {
            $query->join('service_provider_employee', 'users.id', '=', 'service_provider_employee.employee_id')
                ->where('service_provider_employee.service_provider_id', $service_provider_id);
        }
    }

    public function scopeVarified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function rating()
    {
        return $this->hasMany(DoctorRating::class, 'doctor_id', 'id')->with('user')->orderBy('updated_at', 'desc');
    }
    public function userrating()
    {
        return $this->hasMany(DoctorRating::class, 'user_id', 'id');
    }

    public function update_player_id($player_id)
    {
        $this->web_player_id = $player_id;
        $this->save();
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'doctor_id');
    }

    public function doctordata()
    {
        return $this->hasOne(Doctor::class, 'doctor_id');
    }

    public function doctor_service()
    {
        return $this->hasMany(DoctorServiceMapping::class, 'doctor_id')->with('clinicservice');
    }

    public function doctor_document()
    {
        return $this->hasMany(DoctorDocument::class, 'doctor_id');
    }

    public function doctorclinic()
    {
        return $this->hasmany(DoctorClinicMapping::class, 'doctor_id');
    }
    public function doctorsession()
    {
        return $this->hasmany(DoctorSession::class, 'doctor_id');
    }
    public function appointment()
    {
        return $this->hasMany(Appointment::class, 'user_id', 'id')->with('clinicservice', 'doctor');
    }
    public function employeeAppointment()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }


    public function receptionist()
    {
        return $this->hasOne(Receptionist::class, 'receptionist_id');
    }

    public function nurse()
    {
        return $this->hasOne(Nurse::class, 'nurse_id');
    }

    public function cities()
    {
        return $this->hasOne(City::class, 'id', 'city');
    }
    public function states()
    {
        return $this->hasOne(State::class, 'id', 'state');
    }
    public function countries()
    {
        return $this->hasOne(Country::class, 'id', 'country');
    }






    public function scopesetRoleReceptionist($query, $user)
    {

        $user_id = $user->id;


        if (auth()->user()->hasRole(['admin', 'demo_admin'])) {

            if (multiVendor() == 0) {


                $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');

                $query = $query->whereHas('receptionist', function ($qry) use ($user_ids) {
                    $qry->whereIn('vendor_id', $user_ids);
                });

            }

            return $query;
        }

        if ($user->hasRole('vendor')) {

            $query = $query->whereHas('receptionist', function ($qry) use ($user_id) {
                $qry->where('vendor_id', $user_id);
            });

            return $query;

        }

        if (auth()->user()->hasRole('doctor')) {

            User::where('id', $user_id)->with('doctorclinic');

            $clinic_ids = $user->doctorclinic ? $user->doctorclinic->clinic_id : [];

            $active_clinic = Clinics::SetRole(auth()->user())->with('clinicdoctor', 'specialty', 'clinicdoctor', 'receptionist')->whereIn('id', $clinic_ids)->pluck('id')->toArray();

            $query = $query->whereHas('receptionist', function ($qry) use ($active_clinic) {
                $qry->whereIn('clinic_id', $active_clinic);
            });

            return $query;

        }

        if (auth()->user()->hasRole('receptionist')) {

            $receptionist = Receptionist::where('receptionist_id', $user_id)->first();

            $user_id = $receptionist->vendor_id;

            return $query->where('id', $user_id);

        }

        return $query;

    }


    // make the condtion when created_by is vendor,doctor,receptionist then also display that patient 
    public function scopesetRolePatients($query, $user)
    {

        $user_id = $user->id;

        if (auth()->user()->hasRole(['admin', 'demo_admin'])) {

            return $query;
        }

        if ($user->hasRole('vendor')) {

            $query = $query->with('appointment')
                    ->where(function ($q) use ($user_id) {
                        $q->whereHas('appointment.cliniccenter', function ($qry) use ($user_id) {
                            $qry->where('vendor_id', $user_id);
                        })
                        ->orWhere('created_by', $user_id);
                    });

            return $query;

        }

        if (auth()->user()->hasRole('doctor')) {
            if (multiVendor() == 0) {
                $doctor = Doctor::where('doctor_id', $user_id)->first();
                $vendorId = $doctor->vendor_id;

                $query = $query->where('status', 1)
                    ->where(function ($q) use ($user_id, $vendorId) {
                        $q->whereHas('appointment', function ($qry) use ($user_id, $vendorId) {
                            $qry->where('doctor_id', $user_id)
                                ->whereHas('doctorData', function ($subQry) use ($vendorId) {
                                    $subQry->where('vendor_id', $vendorId);
                                });
                        })
                        ->orWhere('created_by', $user_id);
                    });
            } else {
                $query = $query->where('status', 1)
                    ->where(function ($q) use ($user_id) {
                        $q->whereHas('appointment', function ($qry) use ($user_id) {
                            $qry->where('doctor_id', $user_id);
                        })
                        ->orWhere('created_by', $user_id);
                    });
            }

            return $query;
        }

        if (auth()->user()->hasRole('receptionist')) {
            $Receptionist = Receptionist::where('receptionist_id', $user_id)->first();
            $vendorId = $Receptionist->vendor_id;
            $clinic_id = $Receptionist->clinic_id;

            if (multiVendor() == "0") {
                $query = $query->with('appointment')
                    ->where(function ($q) use ($vendorId, $clinic_id, $user_id) {
                        $q->whereHas('appointment.cliniccenter', function ($qry) use ($vendorId, $clinic_id) {
                            $qry->where('vendor_id', $vendorId)
                                ->where('clinic_id', $clinic_id);
                        })
                        ->orWhere('created_by', $user_id);
                    });
            } else {
                $query = $query->with('appointment')
                    ->where(function ($q) use ($clinic_id, $user_id) {
                        $q->whereHas('appointment.cliniccenter', function ($qry) use ($clinic_id) {
                            $qry->where('clinic_id', $clinic_id);
                        })
                        ->orWhere('created_by', $user_id);
                    });
            }

            return $query;
        }


        return $query;
    }


    public function scopeSetRole($query, $user)
    {

        $user_id = $user->id;

        if (auth()->user()->hasRole(['admin', 'demo_admin'])) {

            if (multiVendor() == "0") {


                $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');

                $query = $query->whereHas('doctor', function ($qry) use ($user_ids) {
                    $qry->whereIn('vendor_id', $user_ids);
                })->withCount([
                            'doctorclinic' => function ($q) use ($user_ids) {
                                $q->whereHas('clinics', function ($qq) use ($user_ids) {
                                    $qq->whereIn('vendor_id', $user_ids);
                                });
                            },
                            'doctorsession as doctorsession_count' => function ($query) use ($user_ids) {
                                $query->select(DB::raw('COUNT(DISTINCT clinic_id) as doctorsession_count'))
                                    ->whereHas('clinic', function ($qq) use ($user_ids) {
                                        $qq->whereIn('vendor_id', $user_ids);
                                    });
                                ;
                            }
                        ]);

            } else {

                $query = $query->withCount([
                    'doctorclinic',
                    'doctorsession as doctorsession_count' => function ($query) {
                        $query->select(DB::raw('COUNT(DISTINCT clinic_id) as doctorsession_count'));
                    }
                ]);
            }

        }

        if ($user->hasRole('vendor')) {

            $query = $query->whereHas('doctor', function ($qry) use ($user_id) {
                $qry->where('vendor_id', $user_id);
            })->withCount([
                        'doctorclinic' => function ($q) use ($user_id) {
                            $q->whereHas('clinics', function ($qq) use ($user_id) {
                                $qq->where('vendor_id', $user_id);
                            });
                        },
                        'doctorsession as doctorsession_count' => function ($query) use ($user_id) {
                            $query->select(DB::raw('COUNT(DISTINCT clinic_id) as doctorsession_count'))->whereHas('clinic', function ($qq) use ($user_id) {
                                $qq->where('vendor_id', $user_id);
                            });
                        }
                    ]);

        }

        if (auth()->user()->hasRole('doctor')) {

            //    $query=$query->whereHas('doctor', function ($qry) use ($user_id) {
            //        $qry->where('doctor_id',$user_id)->withCount(['doctorclinic']);
            //    });
            $query = $query->where('id', $user_id)->withCount([
                'doctorclinic',
                'doctorsession as doctorsession_count' => function ($query) {
                    $query->select(DB::raw('COUNT(DISTINCT clinic_id) as doctorsession_count'));
                }
            ])->whereNull('deleted_at');
        }

        if (auth()->user()->hasRole('receptionist')) {

            //    if (multiVendor() == "0"){

            //     $Receptionist=Receptionist::where('receptionist_id',$user_id)->first();

            //      $vendorId=$Receptionist->vendor_id;
            //      $clinicId = $Receptionist->clinic_id;

            //     $query = $query->whereHas('doctor', function ($qry) use ($vendorId) {
            //         $qry->where('vendor_id',$vendorId);
            //        })
            //        ->withCount(['doctorclinic' => function ($query) use ($clinicId) {
            //             $query->where('clinic_id', $clinicId);
            //         },
            //         'doctorsession as doctorsession_count' => function ($query) use ($clinicId) {
            //             $query->select(DB::raw('COUNT(DISTINCT clinic_id) as doctorsession_count'))->where('clinic_id', $clinicId);;
            //         }
            //     ])->whereHas('doctorclinic', function ($qry) use ($clinicId) {
            //         $qry->where('clinic_id', $clinicId);
            //     });


            //    }else{

            //     //    $query = $query->whereHas('doctorclinic.clinics', function ($qry) use ($user_id) {
            //     //        $qry->whereHas('receptionist', function ($qry) use ($user_id) {
            //     //            $qry->where('receptionist_id', $user_id);
            //     //        });
            //     //    });
            //     $receptionist = Receptionist::where('receptionist_id', $user_id)->first();
            //     if ($receptionist) {
            //         $clinicId = $receptionist->clinic_id;
            //         $query = $query->withCount(['doctorclinic' => function ($query) use ($clinicId) {
            //                 $query->where('clinic_id', $clinicId);
            //             },
            //             'doctorsession as doctorsession_count' => function ($query) use ($clinicId) {
            //                 $query->select(DB::raw('COUNT(DISTINCT clinic_id) as doctorsession_count'))->where('clinic_id', $clinicId);;
            //             }
            //         ])->whereHas('doctorclinic', function ($qry) use ($clinicId) {
            //             $qry->where('clinic_id', $clinicId);
            //         });

            //     }
            $receptionist = Receptionist::where('receptionist_id', $user_id)->first();
            if ($receptionist) {
                $clinicId = $receptionist->clinic_id;
                $query = $query->withCount([
                    'doctorclinic' => function ($query) use ($clinicId) {
                        $query->where('clinic_id', $clinicId);
                    },
                    'doctorsession as doctorsession_count' => function ($query) use ($clinicId) {
                        $query->select(DB::raw('COUNT(DISTINCT clinic_id) as doctorsession_count'))->where('clinic_id', $clinicId);
                        ;
                    }
                ])->whereHas('doctorclinic', function ($qry) use ($clinicId) {
                    $qry->where('clinic_id', $clinicId);
                });
            }

        }


        return $query;
    }

    public function blog(){
        return $this->hasMany(Blog::class, 'author_id','id');
    }
    public function otherPatients()
    {
        return $this->hasMany(\Modules\Customer\Models\OtherPatient::class);
    }

    /**
     * Check if user profile is complete
     * 
     * @return bool
     */
    public function isProfileComplete()
    {
        $requiredFields = [
            'first_name',
            'last_name', 
            'email',
            'mobile',
            'date_of_birth',
            'address',
            'gender'
        ];

        // Check new UK fields first, fallback to old fields
        $locationFields = [
            'city_or_town' => 'city',
            'county' => 'state', 
            'postcode' => 'pincode'
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        // Check location fields (new or old)
        foreach ($locationFields as $newField => $oldField) {
            if (empty($this->$newField) && empty($this->$oldField)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get missing profile fields
     * 
     * @return array
     */
    public function getMissingProfileFields()
    {
        $requiredFields = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'mobile' => 'Phone Number',
            'date_of_birth' => 'Date of Birth',
            'address' => 'Address',
            'gender' => 'Gender'
        ];

        $locationFields = [
            'city_or_town' => ['fallback' => 'city', 'label' => 'City/Town'],
            'county' => ['fallback' => 'state', 'label' => 'County'],
            'postcode' => ['fallback' => 'pincode', 'label' => 'Postcode']
        ];

        $missing = [];
        
        foreach ($requiredFields as $field => $label) {
            if (empty($this->$field)) {
                $missing[] = $label;
            }
        }

        foreach ($locationFields as $field => $config) {
            if (empty($this->$field) && empty($this->{$config['fallback']})) {
                $missing[] = $config['label'];
            }
        }

        return $missing;
    }

    /**
     * Get display value for city (new field or fallback to old)
     */
    public function getCityDisplayAttribute()
    {
        return $this->city_or_town ?: ($this->cities ? $this->cities->name : $this->city);
    }

    /**
     * Get display value for county (new field or fallback to old)
     */
    public function getCountyDisplayAttribute()
    {
        return $this->county ?: ($this->states ? $this->states->name : $this->state);
    }

    /**
     * Get display value for postcode (new field or fallback to old)
     */
    public function getPostcodeDisplayAttribute()
    {
        return $this->postcode ?: $this->pincode;
    }
}
