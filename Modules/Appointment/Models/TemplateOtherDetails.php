<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Appointment\Database\factories\TemplateOtherDetailsFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TemplateOtherDetails extends BaseModel
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
    protected $table = 'template_other_details';
    
    protected $fillable = ['template_id','other_details'];
    
    protected static function newFactory(): TemplateOtherDetailsFactory
    {
        //return TemplateOtherDetailsFactory::new();
    }
}

