<?php

namespace Modules\MultiVendor\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MultiVendor extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'multivendors';

    protected $fillable = ['name', 'mobile'];
    const CUSTOM_FIELD_MODEL = 'Modules\MultiVendor\Models\MultiVendor';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\MultiVendor\database\factories\MultiVendorFactory::new();
    }
}
