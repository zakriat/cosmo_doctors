<?php

namespace Modules\Earning\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Earning\Database\factories\EmployeeEarningFactory;
use App\Models\User;
use Modules\Clinic\Models\Doctor;

class EmployeeEarning extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'description', 'total_amount', 'payment_date', 'payment_type', 'commission_amount', 'tip_amount', 'user_type'];
    protected $casts = [
        'employee_id' => 'integer',
        'total_amount' => 'double',
        'commission_amount' => 'double',
        'tip_amount' => 'double',
        'created_by'=> 'integer',
        'updated_by'=> 'integer',
        'deleted_by' => 'integer',
      ];
    
    protected static function newFactory(): EmployeeEarningFactory
    {
        //return EmployeeEarningFactory::new();
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }


    public function Doctor()
    {
        return $this->belongsTo(Doctor::class, 'employee_id', 'doctor_id');
    }
 
 
    public function scopedoctorRole($query, $user){

        $user_id = $user->id;

        if (auth()->user()->hasRole(['admin', 'demo_admin'])) {
            // if (multiVendor() == "0") {
 
            //    $query = $query->whereHas('doctor', function ($query) use ($user_id) {
            //       $query->where('vendor_id', $user_id);
            //   });
            // }
            return $query; 
        }       
        
        if($user->hasRole('vendor')) {  

            $query = $query->whereHas('doctor', function ($query) use ($user_id) {
                $query->where('vendor_id', $user_id);
            });

            return $query; 
         }

         if($user->hasRole('doctor')) {  

            $query = $query->where('employee_id', $user_id);
            
            return $query; 
         }

         return $query; 
    }


}
