<?php

namespace Modules\Appointment\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Medicine extends BaseModel
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'medicines';

    protected $fillable = [
        'name',
        'formulae',
        'side_effects',
        'url',
        'generic_name',
        'brand_name',
        'strength',
        'dosage_form',
        'manufacturer',
        'category',
        'indication',
        'contraindication',
        'drug_interactions',
        'pregnancy_category',
        'storage_conditions',
        'price',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['*']);
    }

    // Relationship with prescriptions
    public function prescriptions()
    {
        return $this->hasMany(EncounterPrescription::class, 'medicine_id');
    }

    // Scope for active medicines
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope for search
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('generic_name', 'like', "%{$search}%")
              ->orWhere('brand_name', 'like', "%{$search}%");
        });
    }

    // Get formatted name for display
    public function getDisplayNameAttribute()
    {
        $name = $this->name;
        if ($this->strength) {
            $name .= ' (' . $this->strength . ')';
        }
        if ($this->dosage_form) {
            $name .= ' - ' . $this->dosage_form;
        }
        return $name;
    }

    // Get BNF URL if available
    public function getBnfUrlAttribute()
    {
        if ($this->url && str_contains(strtolower($this->url), 'bnf')) {
            return $this->url;
        }
        return null;
    }
}