<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\DoctorClinicMappingFactory;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\DoctorSession;



class DoctorClinicMapping extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'doctor_clinic_mapping';

    protected $fillable = ['doctor_id', 'clinic_id'];

    protected static function newFactory(): DoctorClinicMappingFactory
    {
        //return DoctorClinicMappingFactory::new();
    }

    public function clinics()
    {
        return $this->belongsTo(Clinics::class,'clinic_id','id')->with('vendor');
    }

    public function users(){
        return $this->belongsTo(User::class, 'doctor_id','id');
    }

    public function doctor(){
        return $this->belongsTo(Doctor::class, 'doctor_id','doctor_id');
    }

    public function doctorsession(){

        return $this->hasMany(DoctorSession::class ,'doctor_id','doctor_id');
        
    }

    public function sessionsForDoctorAndClinic($doctorId, $clinicId)
    {
        return DoctorSession::where('doctor_id', $doctorId)->where('clinic_id', $clinicId)->get();
    }
    
    public function scopeCheckMultivendor($query){
        if(multiVendor() == "0") {
            $query = $query->whereHas('clinics', function ($q){
                $q->whereHas('vendor', function ($que){
                    $que->whereIn('user_type', ['admin','demo_admin']);
                });
            });
        }
    }
    
    public function scopeSetRole($query, $user)
    {
       $user_id = $user->id;
   
       if(auth()->user()->hasRole(['admin', 'demo_admin'])) {
           
           if (multiVendor() == "0") {

               $query= $query->whereHas('clinics', function ($q){
                    $q->whereHas('vendor', function ($que){
                        $que->whereIn('user_type', ['admin','demo_admin']);
                    });
                });

           }else{

               $query= $query;
              
           }

           return $query;
 
       }

        if($user->hasRole('vendor')) {  

             $query= $query->whereHas('clinics', function ($q) use ($user_id){
                $q->where('vendor_id',$user_id);
            });
             return $query;
        }

        if(auth()->user()->hasRole('doctor')) {
            
           if (multiVendor() == "0") {
            $doctor=Doctor::where('doctor_id',$user_id)->first();
            $vendorId = $doctor->vendor_id;
                $query= $query->whereHas('clinics', function ($q) use($vendorId){
                    $q->where('vendor_id',$vendorId);
                })->whereHas('doctor', function ($q) use($user_id){
                    $q->where('doctor_id',$user_id);
                });

           }else{
            $query= $query->whereHas('doctor', function ($q) use($user_id){
                $q->where('doctor_id',$user_id);
            });
            
           }

           return $query;
       }

       if (auth()->user()->hasRole('receptionist')) {
     
             $Receptionist=Receptionist::where('receptionist_id',$user_id)->first();

             $vendorId=$Receptionist->vendor_id;

           if (multiVendor() == "0") {
            
           $query = $query->whereHas('clinics', function ($q) use($user_id,$vendorId){
                        $q->whereHas('receptionist', function ($qry) use ($user_id) {
                            $qry->where('receptionist_id', $user_id);
                        })->where('vendor_id', $vendorId);
                    });

         }else{
            
            $query = $query->whereHas('clinics', function ($q) use($user_id){
                $q->whereHas('receptionist', function ($qry) use ($user_id) {
                    $qry->where('receptionist_id', $user_id);
                });
            });
         }

         return $query;
       }


       return $query;
    }
}
