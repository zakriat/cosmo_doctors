<?php

namespace Modules\Triage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\Appointment\Models\Appointment;

class TriagePreCheck extends Model
{
    use HasFactory;

    protected $table = 'triage_pre_checks';

    protected $fillable = [
        'appointment_id', 'user_id', 'answers',
        'blocker_triggered', 'blocker_question',
        'recommended_urgency', 'recommended_path',
    ];

    protected $casts = [
        'answers'           => 'array',
        'blocker_triggered' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
