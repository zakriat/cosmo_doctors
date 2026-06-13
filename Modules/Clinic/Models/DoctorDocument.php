<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\DoctorDocumentFactory;
use App\Models\Traits\HasSlug;
use App\Trait\CustomFieldsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class DoctorDocument extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use HasSlug;
    use CustomFieldsTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'doctor_documents';
    protected $fillable = ['doctor_id', 'degree', 'university', 'year'];
    const CUSTOM_FIELD_MODEL = 'Modules\Clinic\Models\DoctorDocument';
    protected static function newFactory(): DoctorDocumentFactory
    {
        //return DoctorDocumentFactory::new();
    }
}
