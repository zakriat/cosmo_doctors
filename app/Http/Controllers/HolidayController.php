<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
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
                return Holiday::create([
                    'clinic_id' => $data['clinic_id'],
                    'date' => $holidayData['date'],
                    'start_time' => $holidayData['start_time'],
                    'end_time' => $holidayData['end_time'],
                    'created_by' => $user->id,
                ]);
            });
    
        // Update existing holidays
        $updatedHolidays = $updatesData->map(function ($holidayData) use ($user) {
            $holiday = Holiday::find($holidayData['id']);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $holiday = Holiday::where('id',$id)->first();

        $clinic_id = $holiday->clinic_id;

        if ($holiday) {
            $holiday->delete();

            $data=Holiday::where('clinic_id',$clinic_id)->get();

            return response()->json(['status' => true, 'data'=> $data]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function get_pickers(Request $request)
    {
        $clinic_id = $request->clinic_id;

        $data = Holiday::where('clinic_id', $clinic_id)->get();
        return response()->json(['data' => $data, 'status' => true]);
    }
}
