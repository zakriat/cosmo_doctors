<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Appointment\Models\DraftAppointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DraftAppointmentController extends Controller
{
    /**
     * Save or update draft appointment
     */
    public function saveDraft(Request $request)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $data = [
                'user_id' => $userId,
                'service_id' => $request->service_id,
                'category_id' => $request->category_id,
                'clinic_id' => $request->clinic_id,
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'current_step' => $request->current_step ?? 0,
                'booking_data' => $request->booking_data ?? [],
            ];

            // Update or create draft for this user and service
            $draft = DraftAppointment::updateOrCreate(
                [
                    'user_id' => $userId,
                    'service_id' => $request->service_id
                ],
                $data
            );

            Log::info('Draft appointment saved', ['draft_id' => $draft->id, 'user_id' => $userId]);

            return response()->json([
                'success' => true,
                'message' => 'Draft saved successfully',
                'draft_id' => $draft->id,
                'data' => $draft
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving draft appointment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific draft
     */
    public function getDraft($id)
    {
        try {
            $userId = Auth::id();

            $draft = DraftAppointment::with(['service', 'category', 'clinic', 'doctor'])
                ->where('id', $id)
                ->where('user_id', $userId)
                ->active()
                ->first();

            if (!$draft) {
                return response()->json([
                    'success' => false,
                    'message' => 'Draft not found or expired'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $draft
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching draft', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch draft'
            ], 500);
        }
    }

    /**
     * Get all active drafts for current user
     */
    public function getUserDrafts()
    {
        try {
            $userId = Auth::id();

            $drafts = DraftAppointment::with(['service', 'category', 'clinic', 'doctor'])
                ->where('user_id', $userId)
                ->active()
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $drafts
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user drafts', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch drafts'
            ], 500);
        }
    }

    /**
     * Delete a draft
     */
    public function deleteDraft($id)
    {
        try {
            $userId = Auth::id();

            $draft = DraftAppointment::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$draft) {
                return response()->json([
                    'success' => false,
                    'message' => 'Draft not found'
                ], 404);
            }

            $draft->delete();

            Log::info('Draft deleted', ['draft_id' => $id, 'user_id' => $userId]);

            return response()->json([
                'success' => true,
                'message' => 'Draft deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting draft', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete draft'
            ], 500);
        }
    }

    /**
     * Delete draft after successful appointment creation
     */
    public function deleteDraftAfterBooking(Request $request)
    {
        try {
            $userId = Auth::id();
            $serviceId = $request->service_id;

            if (!$serviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service ID required'
                ], 400);
            }

            $deleted = DraftAppointment::where('user_id', $userId)
                ->where('service_id', $serviceId)
                ->delete();

            Log::info('Draft deleted after booking', [
                'user_id' => $userId,
                'service_id' => $serviceId,
                'deleted_count' => $deleted
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Draft cleaned up successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting draft after booking', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup draft'
            ], 500);
        }
    }
}
