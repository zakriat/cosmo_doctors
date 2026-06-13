<?php

namespace Modules\Triage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriageItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'triage_items';

    protected $fillable = ['category_id', 'label', 'is_red_flag', 'display_order', 'is_active'];

    protected $casts = [
        'is_red_flag'   => 'boolean',
        'is_active'     => 'boolean',
        'display_order' => 'integer',
        'category_id'   => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(TriageCategory::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }
}
