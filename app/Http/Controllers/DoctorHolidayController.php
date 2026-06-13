<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoctorHoliday;
use Illuminate\Support\Facades\Auth;
class DoctorHolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        
        $holidaysData = collect($data['holidays']);
        $updatesData = collect($data['updates']);
    
        // Create new holidays
        $createdHolidays = $holidaysData->filter(fn($holiday) => !isset($holiday['id']))
            ->map(function ($holidayData) use ($data, $user) {
                return DoctorHoliday::create([
                    'doctor_id' => $data['doctor_id'],
                    'date' => $holidayData['date'],
                    'start_time' => $holidayData['start_time'],
                    'end_time' => $holidayData['end_time'],
                    'created_by' => $user->id,
                ]);
            });
    
        // Update existing holidays
        $updatedHolidays = $updatesData->map(function ($holidayData) use ($user) {
            $holiday = DoctorHoliday::find($holidayData['id']);
            if ($holiday) {
                $holiday->update([
                    'date' => $holidayData['date'],
                    'start_time' => $holidayData['start_time'],
                    'end_time' => $holidayData['end_time'],
                    'updated_by' => $user->id,
                ]);
                return $holiday;
            }
            return null;
        })->filter();  
    
        $message = '';
    
        if ($createdHolidays->isNotEmpty() && $updatedHolidays->isNotEmpty()) {
            $message .= 'Holiday created successfully. ';
        }
        elseif ($createdHolidays->isNotEmpty()) {
            $message .= 'Holiday created successfully. ';
        }
        elseif ($updatedHolidays->isNotEmpty()) {
            $message .= 'Holiday updated successfully.';
        }
    
        return response()->json([
            'message' => trim($message),
            'data' => $createdHolidays->merge($updatedHolidays)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(DoctorHoliday $doctorHoliday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DoctorHoliday $doctorHoliday)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DoctorHoliday $doctorHoliday)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $holiday = DoctorHoliday::find($id);
        if ($holiday) {
            $holiday->delete();
            return response()->json(['status' => true, 'message' => 'Holiday deleted successfully'], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Holiday not found'], 404);
        }
    }

    public function get_doctorpickers(Request $request)
    {
        $doctor_id = $request->doctor_id;

        $data = DoctorHoliday::where('doctor_id', $doctor_id)->get();
        return response()->json(['data' => $data, 'status' => true]);
    }
}
