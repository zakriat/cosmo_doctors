<?php

namespace Modules\Clinic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clinic\Database\factories\ClinicGalleryFactory;
use App\Models\BaseModel;

class ClinicGallery extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'clinic_gallery';

    protected $fillable = ['clinic_id', 'full_url', 'status'];
    
    protected static function newFactory(): ClinicGalleryFactory
    {
        //return ClinicGalleryFactory::new();
    }
}
