<?php

namespace Modules\Earning\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Earning extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'earnings';

    const CUSTOM_FIELD_MODEL = 'Modules\Earning\Models\Earning';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Earning\database\factories\EarningFactory::new();
    }
}
