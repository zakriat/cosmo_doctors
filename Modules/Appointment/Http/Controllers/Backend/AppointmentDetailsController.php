<?php

namespace Modules\Appointment\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Models\DraftAppointment;
use App\Models\AudioTranscription;

class AppointmentDetailsController extends Controller
{
    /**
     * Get full appointment details including medical history, recordings, and documents
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $appointment = Appointment::with([
                'user',
                'doctor',
                'clinicservice.systemservice',
                'cliniccenter',
                'category',
                'appointmenttransaction',
                'otherPatient'
            ])->findOrFail($id);

            // Try to get draft appointment data (may have been deleted after booking)
            $draftData = DraftAppointment::where('user_id', $appointment->user_id)
                ->where('service_id', $appointment->service_id)
                ->first();

            // Extract booking data from draft or appointment
            $bookingData = null;
            if ($draftData && $draftData->booking_data) {
                $bookingData = is_string($draftData->booking_data) 
                    ? json_decode($draftData->booking_data, true) 
                    : $draftData->booking_data;
            }

            // Get medical history text
            $medicalHistoryText = null;
            if ($bookingData) {
                $medicalHistoryText = $bookingData['medicalHistoryText'] ?? 
                                     $bookingData['medical_history_text'] ?? 
                                     $bookingData['medicalHistory'] ?? null;
            }

            // Get audio recordings from audio_transcriptions table
            // First try to get audio linked to this appointment
            $audioRecordings = [];
            $audioTranscriptions = AudioTranscription::where('appointment_id', $appointment->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // If no audio found with appointment_id, try to get by user_id and time range
            // This handles old appointments where audio wasn't linked
            if ($audioTranscriptions->isEmpty()) {
                $appointmentTime = \Carbon\Carbon::parse($appointment->created_at);
                $audioTranscriptions = AudioTranscription::where('user_id', $appointment->user_id)
                    ->whereNull('appointment_id')
                    ->where('created_at', '>=', $appointmentTime->copy()->subHours(2))
                    ->where('created_at', '<=', $appointmentTime->copy()->addHours(1))
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            foreach ($audioTranscriptions as $transcription) {
                $audioRecordings[] = [
                    'id' => $transcription->id,
                    'url' => $transcription->audio_url,
                    'file_path' => $transcription->audio_file_path,
                    'transcription' => $transcription->best_transcription,
                    'original_text' => $transcription->original_text,
                    'medical_text' => $transcription->medical_text,
                    'final_text' => $transcription->final_text,
                    'duration' => $transcription->duration_seconds,
                    'created_at' => $transcription->created_at,
                    'medical_categories' => $transcription->medical_categories,
                ];
            }

            // Also check booking_data for any additional recordings
            if ($bookingData && isset($bookingData['audioRecordings'])) {
                foreach ($bookingData['audioRecordings'] as $recording) {
                    $audioRecordings[] = $recording;
                }
            }

            // Get transcriptions
            $transcriptions = [];
            if ($bookingData) {
                $transcriptions = $bookingData['transcriptions'] ?? 
                                 $bookingData['transcription'] ?? [];
            }

            // Prepare response data
            $data = [
                'appointment' => [
                    'id' => $appointment->id,
                    // 'appointment_date' => $appointment->appointment_date,
                    // 'appointment_time' => $appointment->appointment_time,
                    'appointment_date' => $appointment->appointment_date
                    ? \Carbon\Carbon::parse($appointment->appointment_date)->format(setting('date_formate') ?? 'd/m/Y')
                    : '',

                    'appointment_time' => $appointment->appointment_time
                    ? \Carbon\Carbon::parse($appointment->appointment_time)->format(setting('time_formate') ?? 'h:i A')
                    : '',
                    
                    'start_date_time' => $appointment->start_date_time,
                    'duration' => $appointment->duration,
                    'status' => $appointment->status,
                    'service_amount' => $appointment->service_amount,
                    'total_amount' => $appointment->total_amount,
                    'advance_payment_amount' => $appointment->advance_payment_amount,
                    'advance_paid_amount' => $appointment->advance_paid_amount,
                    'appointment_extra_info' => $appointment->appointment_extra_info,
                    'created_at' => $appointment->created_at,
                ],
                'patient' => [
                    'id' => $appointment->user_id,
                    'name' => optional($appointment->user)->first_name . ' ' . optional($appointment->user)->last_name,
                    'email' => optional($appointment->user)->email,
                    'phone' => optional($appointment->user)->mobile,
                    'profile_image' => optional($appointment->user)->profile_image,
                ],
                'other_patient' => $appointment->otherPatient ? [
                    'id' => $appointment->otherPatient->id,
                    'name' => $appointment->otherPatient->first_name . ' ' . $appointment->otherPatient->last_name,
                    'email' => $appointment->otherPatient->email,
                    'phone' => $appointment->otherPatient->mobile,
                ] : null,
                'doctor' => [
                    'id' => $appointment->doctor_id,
                    'name' => optional($appointment->doctor)->first_name . ' ' . optional($appointment->doctor)->last_name,
                    'email' => optional($appointment->doctor)->email,
                    'profile_image' => optional($appointment->doctor)->profile_image,
                ],
                'service' => [
                    'id' => $appointment->service_id,
                    'name' => optional($appointment->clinicservice)->name,
                    'system_service_name' => optional(optional($appointment->clinicservice)->systemservice)->name,
                    'description' => optional($appointment->clinicservice)->description,
                ],
                'category' => [
                    'id' => $appointment->category_id,
                    'name' => optional($appointment->category)->name,
                ],
                'clinic' => [
                    'id' => $appointment->clinic_id,
                    'name' => optional($appointment->cliniccenter)->name,
                    'address' => optional($appointment->cliniccenter)->address,
                    'phone' => optional($appointment->cliniccenter)->contact_number,
                ],
                'payment' => [
                    'payment_status' => optional($appointment->appointmenttransaction)->payment_status,
                    'payment_method' => optional($appointment->appointmenttransaction)->payment_method,
                    'transaction_id' => optional($appointment->appointmenttransaction)->transaction_id,
                ],
                'medical_data' => [
                    'medical_history_text' => $medicalHistoryText,
                    'audio_recordings' => $audioRecordings,
                    'transcriptions' => $transcriptions,
                    'booking_data_raw' => $bookingData, // For debugging
                ],
                'documents' => $this->getAppointmentDocuments($appointment),
                'video_links' => [
                    'start_video_link' => $appointment->start_video_link,
                    'join_video_link' => $appointment->join_video_link,
                    'meet_link' => $appointment->meet_link,
                ],
            ];

            return response()->json([
                'status' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('AppointmentDetailsController error', [
                'appointment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch appointment details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get appointment documents/files
     *
     * @param Appointment $appointment
     * @return array
     */
    private function getAppointmentDocuments($appointment)
    {
        $documents = [];

        // Get media files attached to appointment
        $mediaFiles = $appointment->getMedia('file_url');

        foreach ($mediaFiles as $media) {
            $documents[] = [
                'id' => $media->id,
                'name' => $media->file_name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
                'url' => $media->getUrl(),
                'download_url' => $media->getUrl(),
                'uploaded_at' => $media->created_at,
            ];
        }

        return $documents;
    }
}
