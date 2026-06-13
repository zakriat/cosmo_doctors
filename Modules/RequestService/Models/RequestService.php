<?php

namespace Modules\RequestService\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestService extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'requestservices';

    protected $fillable = ['name','description','type','vendor_id','status','is_status'];

    const CUSTOM_FIELD_MODEL = 'Modules\RequestService\Models\RequestService';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\RequestService\database\factories\RequestServiceFactory::new();
    }
}
