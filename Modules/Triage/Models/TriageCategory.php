<?php

namespace Modules\Triage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriageCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'triage_categories';

    protected $fillable = ['name', 'display_order', 'is_active'];

    protected $casts = [
        'is_active'     => 'boolean',
        'display_order' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(TriageItem::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('display_order');
    }

    public function allItems()
    {
        return $this->hasMany(TriageItem::class, 'category_id')->orderBy('display_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }
}
