<?php

namespace Modules\Vital\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
class Vital extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'vitals';

    protected $fillable = ['customer_id', 'height_cm', 'weight_kg', 'height_inch', 'weight_pound', 'bmi','tbf','vfi','bmc','bmr','sm','tbw'];

    const CUSTOM_FIELD_MODEL = 'Modules\Vital\Models\Vital';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Vital\database\factories\VitalFactory::new();
    }

    public function customer(){

            return $this->belongsTo(User::class);
        }
}
