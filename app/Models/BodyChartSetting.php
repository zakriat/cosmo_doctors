<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodyChartSetting extends BaseModel
{
    use HasFactory;

    protected $table='bodychart_setting';
    protected $fillable = [
        'name',
        'full_url',
        'default',
        'uniqueId',
        'image_name',
    ];
}
