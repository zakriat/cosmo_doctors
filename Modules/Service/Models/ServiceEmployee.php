<?php

namespace Modules\Service\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\ServiceProviderEmployee;

class ServiceEmployee extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'employee_id'];

    protected $casts = [

        'service_id' => 'integer',
        'employee_id' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    public function service_providers()
    {
        return $this->hasMany(ServiceProviderEmployee::class, 'employee_id');
    }
}
