<?php

namespace Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Wallet\Database\factories\WalletFactory;

class Wallet extends Model
{
    use HasFactory;
    
    protected $table = 'wallets';
    protected $fillable = [
        'user_id', 'title', 'amount','status'
    ];
    protected $casts = [
        'user_id'  =>'integer',
        'amount'   => 'double',
        'status'   => 'integer',
    ];
    
    protected static function newFactory(): WalletFactory
    {
        //return WalletFactory::new();
    }
}
