<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Appointment\Database\factories\BillingRecordFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use Modules\Clinic\Models\Clinics;
use App\Models\User;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Models\Receptionist;
use Modules\Clinic\Models\Doctor;
use Modules\Appointment\Models\BillingItem;


class BillingRecord extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'billing_record';

    const CUSTOM_FIELD_MODEL = 'Modules\Appointment\Models\BillingRecord';
    
    protected $fillable = ['encounter_id','user_id','clinic_id','doctor_id','service_id','total_amount','service_amount','discount_type','discount_value','discount_amount','tax_data','date','status','payment_status','final_discount','final_discount_value','final_discount_type','final_tax_amount','final_total_amount'];

    protected $casts = [
        'user_id' => 'integer',
        'clinic_id' => 'integer',
        'doctor_id' => 'integer',
        'encounter_id' =>'integer',
        'service_id' => 'integer',
        'total_amount' => 'double',
        'service_amount' => 'double',
        'discount_amount' =>'double',
        'discount_value'=>'double',
        'status' => 'integer',
    ];
    
    protected static function newFactory(): BillingRecordFactory
    {
        //return BillingRecordFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinics::class, 'clinic_id','id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id','id');
    }

    public function clinicservice()
    {
        return $this->belongsTo(ClinicsService::class, 'service_id','id')->with('doctor_service');
    }

    public function patientencounter()
    {
        return $this->belongsTo(PatientEncounter::class, 'encounter_id','id')->with('appointmentdetail');
    }

    public function billingItem()
    {
        return $this->hasMany(BillingItem::class, 'billing_id','id')->with('clinicservice');
    }

    public function appointmentTransaction(){
        return $this->hasOne(AppointmentTransaction::class,'appointment_id','appointment_id');
    }

    public function scopeSetRole($query, $user)
    {
       $user_id = $user->id;
       
       if (auth()->user()->hasRole(['admin', 'demo_admin'])) {
           if (multiVendor() == "0") {

            $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');
              
              $query->with('clinic')->whereHas('clinic', function ($query) use ($user_ids) {
                 $query->whereIn('vendor_id', $user_ids);
              });
            }

           return $query; 
      }       
       
       if($user->hasRole('vendor')) {  

            $query->with('clinic')->whereHas('clinic', function ($query) use ($user_id) {

              $query->where('vendor_id', $user_id);

           });
           return $query; 
        }

        if(auth()->user()->hasRole('doctor')) {

           if(multiVendor() == 0) {

               $doctor=Doctor::where('doctor_id',$user_id)->first();

               $vendorId=$doctor->vendor_id;

               $query=$query->where('doctor_id',$user_id)->whereHas('clinic', function ($qry) use ($vendorId) {

                   $qry->where('vendor_id', $vendorId);

                   });

           }else{

             $query=$query->where('doctor_id',$user_id);

          }

          return $query;
       }

       if (auth()->user()->hasRole('receptionist')) {


           $Receptionist=Receptionist::where('receptionist_id',$user_id)->first();
   
           $vendorId=$Receptionist->vendor_id;
           $clinic_id=$Receptionist->clinic_id;
   
           if(multiVendor() == "0") {

              $query=$query->where('clinic_id',$clinic_id)->whereHas('clinic', function ($qry) use ($vendorId) {

                  $qry->where('vendor_id', $vendorId);

                });    

           }else{

            $query=$query->where('clinic_id',$clinic_id);
            
           }

        return $query;
        
       }

        return $query;
    }



}
