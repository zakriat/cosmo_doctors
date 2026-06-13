<?php

namespace Modules\Customer\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Modules\Customer\Models\OtherPatient;
use Illuminate\Support\Facades\Auth;
use Modules\Customer\Http\Requests\OtherRequest;



class CustomersController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function getPatientList(Request $request): JsonResponse
    {
        $user = auth()->user();

        $patients = [
            [
                'name' => 'Myself',
                'image' => $user->profile_image ? asset('storage/' . $user->profile_image) : asset('default-avatar.png'),
            ]
        ];

        $otherPatients = OtherPatient::where('user_id', $user->id)->get();

        foreach ($otherPatients as $patient) {
            $patients[] = [
                'name' => $patient->first_name . ' ' . $patient->last_name,
                'image' => $patient->profile_image ? asset('storage/' . $patient->profile_image) : asset('default-avatar.png'),
            ];
        }

        return response()->json(['patients' => $patients], 200);
    }

    public function customerDetails(): JsonResponse
    {

        if (auth()->check()) {
            $user = auth()->user();

            $otherPatients = OtherPatient::where('user_id', $user->id)->get();

            return response()->json([
                'user' => $user,
                'patientdetails' => $otherPatients
            ]);
        }

        return response()->json(['error' => 'User not authenticated'], 401);
    }

    public function getOtherMembersList(Request $request)
    {
        try {
            // Get the authenticated user ID or fallback to request user_id

            if ($request->has('patient_id')) {
                $otherMember = OtherPatient::where('user_id', $request->patient_id)
                    ->get();
    
                if (!$otherMember) {
                    return response()->json([
                        'status'  => false,
                        'code'    => 404,
                        'message' => __('messages.patient_not_found'),
                    ], 404);
                }
    
                return response()->json([
                    'status'  => true,
                    'code'    => 200,
                    'message' => __('messages.record_fetched_successfully'),
                    'data'    => $otherMember,
                ], 200);
            }
            $userId = auth()->id() ?? $request->user_id;

            // Fetch the user and ensure it exists
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'code'    => 404,
                    'message' => __('messages.user_not_found'),
                ], 404);
            }

            // Fetch other members for the given user, ordered by latest first
            $otherMembers = OtherPatient::where('user_id', $userId)
                ->latest()  // This orders by created_at DESC
                ->get();

            // Return success response
            return response()->json([
                'status'  => true,
                'code'    => 200,
                'message' => __('messages.records_fetched_successfully'),
                'data'    => $otherMembers,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'code'    => 500,
                'message' => __('messages.something_went_wrong'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function addOtherMember(Request $request)
    {
        // Retrieve all input data
        $data = $request->all();

        // Set the user ID, either from the authenticated user or from the request
        $data['user_id'] = auth()->id() ?? $request->user_id;

        // Check if the user ID is null
        if (is_null($data['user_id'])) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => __('messages.user_not_found'),
            ], 404);
        }
        $user = User::where('id',$data['user_id'])->first();
        if (is_null($user)) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => __('messages.user_not_found'),
            ], 404);
        }
        // Determine if it's an update or a new record
        if (!empty($data['id'])) {
            $otherMemberList = OtherPatient::find($data['id']);
            if (!$otherMemberList) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => __('messages.record_not_found'),
                ], 404);
            }
            $otherMemberList->update($data);
            $message = __('messages.member_updated');
        } else {
            $otherMemberList = OtherPatient::create($data);
            $message = __('messages.member_added');
        }
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            storeMediaFile($otherMemberList, $request->file('profile_image'), 'profile_image');
        }
        // Return success response
        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $otherMemberList,
            'message' => $message,
        ], 200);
    }

    public function deleteOtherMember($id)
    {
        
        // Retrieve the record by ID
        $otherMember = OtherPatient::find($id);

        // Check if the record exists
        if (!$otherMember) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => __('messages.record_not_found'),
            ], 404);
        }

        // Delete the record
        $otherMember->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => __('messages.record_deleted_successfully'),
        ], 200);
       
    }

}
