<?php

namespace App\Services;

use Modules\Appointment\Models\Appointment;

class CrmNotificationService
{
    public function __construct(
        protected EvolutionWhatsAppService $whatsapp
    ) {}

    public function appointmentBooked(Appointment $appointment): void
    {
        $appointment->loadMissing([
            'user',
            'doctor',
            'cliniccenter',
            'clinicservice',
            'appointmenttransaction',
        ]);

        $patient = $appointment->user;
        $doctor = $appointment->doctor;
        $clinic = $appointment->cliniccenter;
        $service = $appointment->clinicservice;

        $patientPhone = $patient->mobile ?? $patient->contact_number ?? $patient->phone ?? null;

        $date = $appointment->appointment_date
            ? \Carbon\Carbon::parse($appointment->appointment_date)->format(setting('date_formate') ?? 'd/m/Y')
            : '';

        $time = $appointment->appointment_time
            ? \Carbon\Carbon::parse($appointment->appointment_time)->format(setting('time_formate') ?? 'h:i A')
            : '';

        $doctorName = trim(($doctor->first_name ?? '') . ' ' . ($doctor->last_name ?? ''));
        $clinicName = $clinic->name ?? 'Cosmo Doctors';
        $serviceName = $service->name ?? 'appointment';

        $patientMessage = "Hello {$patient->first_name}, your {$serviceName} appointment with Dr. {$doctorName} at {$clinicName} is confirmed for {$date} at {$time}. Booking ID: #{$appointment->id}.";

        $this->whatsapp->sendText($patientPhone, $patientMessage);

        $adminPhone = env('ADMIN_WHATSAPP_NUMBER');

        if ($adminPhone) {
            $patientName = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));

            $adminMessage = "New appointment booked.\nBooking ID: #{$appointment->id}\nPatient: {$patientName}\nService: {$serviceName}\nDoctor: Dr. {$doctorName}\nDate: {$date} at {$time}.";

            $this->whatsapp->sendText($adminPhone, $adminMessage);
        }
    }
}
