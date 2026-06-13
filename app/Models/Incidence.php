<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use App\Models\BaseModel;
use App\Models\User;

class Incidence extends BaseModel
{
    protected $table = 'incidences';
    protected $guarded = ['id'];
    protected $appends = ['status_name', 'incidence_type_name', 'file_url'];

    protected function getFileUrlAttribute()
    {
        $media = $this->getFirstMediaUrl('file_url');

        return isset($media) && ! empty($media) ? $media : null;
    }

    public function getStatusNameAttribute()
    {
        return self::getStatusdata($this->status);
    }

    public function getIncidenceTypeNameAttribute()
    {
        return self::getIncidenceType($this->incident_type);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    protected function getStatusdata($value)
    {
        $status = "";
        ($value == 1) && $status = 'pending';
        ($value == 2) && $status = 'Resolved';
        ($value == 3) && $status = 'Rejected';
        return $status;
    }

    protected function getIncidenceType($value)
    {
        $status = "";
        ($value == 1) && $status = 'open';
        ($value == 2) && $status = 'closed';
        ($value == 3) && $status = 'reject';
        return $status;
    }
}
