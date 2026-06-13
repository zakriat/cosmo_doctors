<?php

namespace Modules\CustomForm\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomForm extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customforms';

    const CUSTOM_FIELD_MODEL = 'Modules\CustomForm\Models\CustomForm';


    protected $fillable = [
        'formdata', 'module_type', 'fields', 'show_in', 'appointment_status', 'status',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\CustomForm\database\factories\CustomFormFactory::new();
    }
}

