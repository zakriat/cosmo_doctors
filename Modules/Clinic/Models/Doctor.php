<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\DoctorFactory;
use App\Models\Traits\HasSlug;
use App\Trait\CustomFieldsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use Modules\Clinic\Models\ClinicsService;
use App\Models\User;
use Modules\Clinic\Models\Clinics;
use Auth;
use Modules\Clinic\Models\DoctorServiceMapping;
use Modules\Clinic\Models\DoctorClinicMapping;
use Modules\Commission\Models\EmployeeCommission;
use Modules\Clinic\Models\Receptionist;
use Modules\Clinic\Models\DoctorCategoryMapping;

class Doctor extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use HasSlug;
    use CustomFieldsTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'doctors';
    protected $fillable = ['doctor_id','experience','signature','vendor_id'];
    protected $casts = [

        'doctor_id' => 'integer',
        'status' => 'boolean',
        'vendor_id' => 'integer',
    ];
    const CUSTOM_FIELD_MODEL = 'Modules\Clinic\Models\Doctor';
    protected static function newFactory(): DoctorFactory
    {
        //return DoctorFactory::new();
    }
    public function clinicservices()
    {
        return $this->belongsTo(ClinicsService::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }


    public function scopeSetVendor($query)
    {
        $vendorId = Auth::id();
        return $query->where('vendor_id', $vendorId);
    }
    public function doctorService()
    {
        return $this->hasMany(DoctorServiceMapping::class,'doctor_id','doctor_id')->with('clinicservice');
    }
    
    public function doctorclinic()
    {
        return $this->hasMany(DoctorClinicMapping::class, 'doctor_id','doctor_id');
    }

    /**
     * Get all category mappings for this doctor.
     */
    public function categoryMappings()
    {
        return $this->hasMany(DoctorCategoryMapping::class, 'doctor_id', 'doctor_id');
    }

    /**
     * Get all categories assigned to this doctor (many-to-many).
     */
    public function categories()
    {
        return $this->belongsToMany(
            ClinicsCategory::class,
            'doctor_category_mappings',
            'doctor_id',
            'category_id',
            'doctor_id',
            'id'
        )->withPivot('clinic_id', 'charges', 'status')
          ->withTimestamps();
    }

    public function doctorSessions()
    {
        return $this->hasMany(DoctorSession::class,'doctor_id','doctor_id');
    }

    public function doctorReviews()
    {
        return $this->hasMany(DoctorRating::class, 'doctor_id','doctor_id');
    }
    public function doctorDocuments()
    {
        return $this->hasMany(DoctorDocument::class, 'doctor_id','doctor_id');
    }

    public function doctorCommission()
    {
        return $this->hasMany(EmployeeCommission::class, 'employee_id','doctor_id');
    }

    public function scopeCheckMultivendor($query){
        if(multiVendor() == "0") {
            $query = $query->where('status',1)->whereHas('vendor', function ($q){
                $q->whereIn('user_type', ['admin','demo_admin']);
            });
        }
        else{
            $query = $query->where('status',1);
        }
    }

    public function scopeSetRole($query, $user)
    {
       $user_id = $user->id;
       
       if (auth()->user()->hasRole(['admin', 'demo_admin'])) {
           if (multiVendor() == "0") {

            $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');
            $query = $query->whereIn('vendor_id', $user_ids);
           }
           return $query; 
       }       
       
       if($user->hasRole('vendor')) {  

           $query=$query->where('vendor_id',  $user_id);

           return $query; 
        }

        if(auth()->user()->hasRole('doctor')) {


           $query=$query->where('doctor_id',$user_id);

           return $query;
        
        }

        if(auth()->user()->hasRole('receptionist')){

            $Receptionist=Receptionist::where('receptionist_id',$user_id)->first();

            $vendorId=$Receptionist->vendor_id;
            $clinic_id=$Receptionist->clinic_id;

            if(multiVendor() == "0"){

                $query->where('vendor_id', $vendorId)->with('doctorclinic')->whereHas('doctorclinic', function ($query) use ($clinic_id) {
                    $query->where('clinic_id', $clinic_id);
                 });

             }else{

                  $query->with('doctorclinic')->whereHas('doctorclinic', function ($query) use ($clinic_id) {
                      $query->where('clinic_id', $clinic_id);
                   });
              
               }
            // $query = $query->with('doctorService')->whereHas('doctorService', function ($query) use ($clinic_id) {
            //             $query->where('clinic_id', $clinic_id);
            //          });
               return $query;
        }


        return $query;
    }


    
}
