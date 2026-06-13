<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\ClinicHoursFactory;
use App\Models\BaseModel;

class ClinicHours extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'clinichours';

    protected $fillable = ['clinic_id', 'vendor_id', 'day', 'start_time','end_time','is_holiday','breaks'];
    
    protected static function newFactory(): ClinicHoursFactory
    {
        //return ClinicHoursFactory::new();
    }

}  
