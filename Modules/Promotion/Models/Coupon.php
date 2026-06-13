<?php

namespace Modules\Promotion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Booking\Models\Booking;
use Modules\Promotion\Database\factories\CouponFactory;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'promotions_coupon';
    protected $fillable = ['coupon_code','timezone','discount_type','discount_percentage','discount_amount','start_date_time','end_date_time','promotion_id','use_limit'];

    protected static function newFactory(): CouponFactory
    {
        //return CouponFactory::new();
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }



}
