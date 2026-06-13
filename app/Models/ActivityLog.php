<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    const CUSTOM_FIELD_MODEL = 'App\Models\ActivityLog';
    protected $table='activity_log';
    protected $guarded = ['id'];

    // protected function properties(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn (string $value) => (isset($value) && !empty($value)) ? self::modifyOldAndNewData($value) : '',           
    //     );
    // }

    // private function modifyOldAndNewData($value)
    // {
    //     print_r($value);
    //     exit();
    // }

}
