<?php

namespace Modules\Commission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Appointment\Models\Appointment;

class CommissionEarning extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id','commissionable','commissions','user_type','model_id', 'commission_amount','commission_status', 'payment_date'];

    protected $casts = [

        'employee_id' => 'integer',
        'commissionable_id' => 'integer',
        'commission_amount' => 'double',
    ];

    protected static function newFactory()
    {
        return \Modules\Commission\Database\factories\CommissionEarningFactory::new();
    }

    public function getAppointment()
    {
        return $this->belongsTo(Appointment::class, 'commissionable_id');
    }
}
