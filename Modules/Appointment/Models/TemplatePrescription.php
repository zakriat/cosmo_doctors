<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Appointment\Database\factories\TemplatePrescriptionFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TemplatePrescription extends BaseModel
{
    use HasFactory;
    use SoftDeletes,LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'template_prescription';
    
    protected $fillable = ['template_id','name','frequency','duration','instruction'];
    
    protected static function newFactory(): TemplatePrescriptionFactory
    {
        //return TemplatePrescriptionFactory::new();
    }
}
