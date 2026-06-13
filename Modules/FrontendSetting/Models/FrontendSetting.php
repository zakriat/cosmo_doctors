<?php
namespace Modules\FrontendSetting\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\FrontendSetting\Models\Traits\HasSlug;
// use Spatie\MediaLibrary\InteractsWithMedia;

class FrontendSetting extends BaseModel
{
    use HasFactory;

    protected $table = 'frontend_settings';
    protected $fillable = [
        'type','key','status','value'
    ];

    protected $casts = [
        'status'     => 'integer',
    ];
    public static function getValueByKey($key)
    {
        $setting = self::where('key', $key)->first();

        return $setting ? json_decode($setting->value) : null;
    }
}
?>