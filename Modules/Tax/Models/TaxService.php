<?php

namespace Modules\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Models\ClinicsService;

class TaxService extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'tax_service';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['tax_id', 'service_id'];

    /**
     * Define the relationship with the Tax model.
     */
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    /**
     * Define the relationship with the Service model.
     */
    public function service()
    {

        return $this->belongsTo(ClinicsService::class, 'service_id');
    }

    /**
     * Define the factory for this model.
     */
    protected static function newFactory()
    {
        return \Modules\Tax\Database\factories\TaxServiceFactory::new();
    }
}
