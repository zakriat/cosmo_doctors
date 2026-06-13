<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Modules\Clinic\Models\Clinics;

class Holiday extends Model
{
    use HasFactory;
    use HasRoles;
    const CUSTOM_FIELD_MODEL = 'App\Models\Holiday';
    protected $fillable = ['clinic_id', 'date', 'start_time', 'end_time'];

    public function clinic()
    {
        return $this->belongsTo(Clinics::class);
    }
}
