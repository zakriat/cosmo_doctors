<?php

namespace Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Wallet\Database\factories\WalletHistoryFactory;

class WalletHistory extends Model
{
    use HasFactory;

    protected $table = 'wallet_histories';
    protected $fillable = [
        'datetime', 'user_id', 'activity_type', 'activity_message', 'activity_data'
    ];

    protected $casts = [
        'user_id'   => 'integer',
    ];
    
    protected static function newFactory(): WalletHistoryFactory
    {
        //return WalletHistoryFactory::new();
    }

    public function wallet(){
        return $this->belongsTo(Wallet::class, 'user_id','user_id');
    }
}
