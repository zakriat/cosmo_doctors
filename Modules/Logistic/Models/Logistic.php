<?php

namespace Modules\Logistic\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logistic extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'logistics';

    const CUSTOM_FIELD_MODEL = 'Modules\Logistic\Models\Logistic';

    protected $appends = ['file_url'];

    protected $casts = [

        'status' => 'integer',

    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Logistic\database\factories\LogisticFactory::new();
    }

    protected function getFileUrlAttribute()
    {
        $media = $this->getFirstMediaUrl('file_url');

        return isset($media) && ! empty($media) ? $media : default_file_url();
    }
}
