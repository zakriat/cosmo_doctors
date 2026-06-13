<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });
    }

    public function createdUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function deletedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }
}