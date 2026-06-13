<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\ClinicFactory;
use App\Trait\CustomFieldsTrait;
use App\Models\BaseModel;
use App\Models\User;
use Auth;
use Modules\Service\Models\SystemServiceCategory;
use App\Models\Traits\HasSlug;
use Modules\World\Models\City;
use Modules\World\Models\Country;
use Modules\World\Models\State;
use Modules\Clinic\Models\ClinicServiceMapping;
use Modules\Clinic\Models\DoctorClinicMapping;
use Modules\Clinic\Models\ClinicSession;
use Modules\Clinic\Models\ClinicGallery;
use Modules\Appointment\Models\Appointment;
use Modules\Clinic\Models\Receptionist;
use Modules\Clinic\Models\DoctorSession;
use App\Models\Holiday;


class Clinics extends BaseModel
{
    use HasFactory;
    use CustomFieldsTrait;
    use HasSlug;
    const CUSTOM_FIELD_MODEL = 'Modules\Clinic\Models\Clinics';

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'clinic';


    protected $fillable = ['slug','name','email','time_slot','system_service_category','description', 'address', 'city','state', 'country', 'pincode','vendor_id', 'contact_number', 'latitude','longitude','status'];

    protected $appends = ['file_url'];


    protected static function newFactory(): ClinicFactory
    {
        //return ClinicFactory::new();
    }

    protected function getFileUrlAttribute()
    {
        $media = $this->getFirstMediaUrl('file_url');

        return isset($media) && ! empty($media) ? $media : default_file_url();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($clinic) {
            $clinic->clinicsessions()->delete();
            $clinic->cliniccategory()->delete();
            $clinic->clinicservices()->delete();
            $clinic->clinicgallery()->delete();
            $clinic->clinicappointment()->delete();
            $clinic->clinicdoctor()->delete();
    
        });
    }

   
    public function scopeSetVendor($query)
    {
        $vendorId = Auth::id();
        return $query->where('vendor_id', $vendorId);
    }


    public function scopeSetRole($query, $user)
     {
        $user_id = $user->id;


        
        if(auth()->user()->hasRole(['admin', 'demo_admin'])) {

            if (multiVendor() == "0") {
                
                $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');
               
                $query=$query->whereIn('vendor_id', $user_ids);
            }
            return $query; 
        }       
        
        if($user->hasRole('vendor')) {  

            $query=$query->where('vendor_id',  $user_id);
            return $query; 
         }

         if(auth()->user()->hasRole('doctor')) {

            if(multiVendor() == 0) {

                $doctor=Doctor::where('doctor_id',$user_id)->first();

                $vendorId=$doctor->vendor_id;

                $query=$query->where('vendor_id',$vendorId)->where('status',1)->whereHas('clinicdoctor.doctor', function ($qry) use ($user_id) {

                    $qry->where('doctor_id', $user_id);

                    });

            }else{

             $query=$query->where('status',1)->whereHas('clinicdoctor.doctor', function ($qry) use ($user_id) {
                    $qry->where('doctor_id', $user_id);
              });

           }

           return $query;
        }

        if (auth()->user()->hasRole('receptionist')) {

            if(multiVendor() == "0") {

                $Receptionist=Receptionist::where('receptionist_id',$user_id)->first();

                $vendorId=$Receptionist->vendor_id;
                
                $query=$query->where('status',1)->where('vendor_id',$vendorId)->whereHas('receptionist', function ($qry) use ($user_id) {
                    $qry->where('receptionist_id', $user_id);
                });

            }else{

               $query=$query->where('status',1)->whereHas('receptionist', function ($qry) use ($user_id) {
                $qry->where('receptionist_id', $user_id);
            });
         }

         return $query;
         
        }

         return $query;
     }



    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id','id');
    }
    public function specialty()
    {
        return $this->belongsTo(SystemServiceCategory::class, 'system_service_category','id');
    }
    public function countries()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function states()
    {
        return $this->belongsTo(State::class, 'state');
    }

    public function cities()
    {
        return $this->belongsTo(City::class, 'city');
    }


    public function clinicsessions() {

        return $this->hasMany( ClinicSession::class, 'clinic_id');
         
    }

    public function clinicservices() {

        return $this->hasMany(ClinicServiceMapping::class, 'clinic_id');
         
    }


    public function clinicgallery() {

        return $this->hasMany( ClinicGallery::class, 'clinic_id');
         
    }

    public function clinicappointment() {

        return $this->hasMany(Appointment::class, 'clinic_id')->with('appointmenttransaction','commissionsdata');
         
    }
  
   public function clinicdoctor() {

      return $this->hasMany( DoctorClinicMapping::class, 'clinic_id');
         
    }
    public function receptionist() {

        return $this->hasOne( Receptionist::class, 'clinic_id');
           
    }

    public function doctorsessions() {

        return $this->hasMany( DoctorSession::class, 'clinic_id');
         
    }

    public function clinicholiday() {
        return $this->hasMany(Holiday::class, 'clinic_id');
    }

    public function scopeCheckMultivendor($query){
        if(multiVendor() == "0") {
            $query = $query->where('status',1)->whereHas('vendor', function ($q){
                $q->whereIn('user_type', ['admin','demo_admin']);
            });
        }
        else{
            $user = auth()->user() ?? null;
            if($user == null) {
                $query = $query->where('status',1);
            }
            
        }
    }
}
