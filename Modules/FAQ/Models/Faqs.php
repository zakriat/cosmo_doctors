<?php

namespace Modules\FAQ\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\FAQ\Database\factories\FaqsFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class faqs extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['question', 'answer', 'status']; // Ensure all relevant attributes are included
    
    protected static function newFactory(): FaqsFactory
    {
        return \Modules\FAQ\database\factories\FAQFactory::new();
    }
}
