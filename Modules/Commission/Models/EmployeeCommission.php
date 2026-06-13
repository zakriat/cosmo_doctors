<?php

namespace Modules\Commission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasSlug;
use App\Trait\CustomFieldsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class EmployeeCommission extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use HasSlug;
    use CustomFieldsTrait;

    protected $fillable = ['employee_id', 'commission_id'];

    protected $casts = [

        'employee_id' => 'integer',
        'commission_id' => 'integer',

    ];
    const CUSTOM_FIELD_MODEL = 'Modules\Commission\Models\EmployeeCommission';
    protected static function newFactory()
    {
        return \Modules\Commission\Database\factories\EmployeeCommissionFactory::new();
    }

    public function mainCommission()
    {
        return $this->belongsTo(Commission::class, 'commission_id');
    }
}
