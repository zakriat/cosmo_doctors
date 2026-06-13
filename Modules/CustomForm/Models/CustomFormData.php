<?php

namespace Modules\CustomForm\Models;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomFormData extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'customformdata';

    const CUSTOM_FIELD_MODEL = 'Modules\CustomForm\Models\CustomFormData';

    protected $fillable = [
        'form_id', 'module', 'module_id', 'form_data'
    ];
    


}
