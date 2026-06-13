<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\ReceptionistFactory;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Clinic\Models\Clinics;


class Receptionist extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'receptionist';
    protected $fillable = ['receptionist_id', 'clinic_id','vendor_id'];
    protected static function newFactory(): ReceptionistFactory
    {
        //return ReceptionistFactory::new();
    }
    public function clinics()
    {
        return $this->belongsTo(Clinics::class,'clinic_id');
    }

    public function users(){
        return $this->belongsTo(User::class,'receptionist_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id','id');
    }

    public function scopeCheckMultivendor($query){
        if(multiVendor() == "0") {
            $query = $query->whereHas('vendor', function ($q){
                $q->whereIn('user_type', ['admin','demo_admin']);
            });
        }else{
            $query = $query->get();
        }
       
    }
}
