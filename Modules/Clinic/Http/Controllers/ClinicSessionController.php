<?php

namespace Modules\Clinic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\ClinicSession;

class ClinicSessionController extends Controller
{
    public function __construct()
    {
        // Page clinic_name
        $this->module_title = 'clinic.clinic_session';
        // module name
        $this->module_name = 'clinic-session';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }


    public function index_list(Request $request)
    {
        $clinic_id = $request->input('clinic_id');

        $query_data = ClinicSession::where('clinic_id',$clinic_id)->get();

        return response()->json(['data' => $query_data , 'status' => true]);
    }
   
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clinic_id = $request->input('clinic_id') ?? 1; // Use request or default
        $clinic = Clinics::find($clinic_id);

        $sessions = ClinicSession::where('clinic_id', $clinic_id)->get();

        $weekdays = [];
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        foreach ($days as $day) {
            $session = $sessions->where('day', $day)->first();
            $breaks = [];
            if ($session) {
                if (is_string($session->breaks)) {
                    $breaks = json_decode($session->breaks, true) ?? [];
                } elseif (is_array($session->breaks)) {
                    $breaks = $session->breaks;
                }
            }
            $weekdays[] = [
                'day' => $day,
                'start_time' => $session->start_time ?? '09:00',
                'end_time' => $session->end_time ?? '18:00',
                'is_holiday' => $session->is_holiday ?? false,
                'breaks' => $breaks,
                'id' => $session->id ?? null,
            ];
        }

        return view('clinic::backend.clinic.index_datatable', compact('clinic', 'weekdays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clinic::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $clinic_id = $data['clinic_id'];
        $weekdays = $data['weekdays'];

        foreach ($weekdays as $key => $value) {
            $value['clinic_id'] = $clinic_id;

            if (!empty($value['is_holiday']) && $value['is_holiday'] == 1) {
                $value['start_time'] = null;
                $value['end_time']   = null;
                $value['breaks']     = null;
            } else {
                // Ensure breaks is stored as array, not as string
                if (isset($value['breaks'])) {
                    if (is_string($value['breaks'])) {
                        $decoded = json_decode($value['breaks'], true);
                        $value['breaks'] = is_array($decoded) ? $decoded : [];
                    }
                    if ($value['breaks'] === "" || $value['breaks'] === null) {
                        $value['breaks'] = [];
                    }
                } else {
                    $value['breaks'] = [];
                }
            }

            ClinicSession::updateOrCreate(
                ['clinic_id' => $clinic_id, 'id' => $value['id'] ?? -1],
                $value
            );
        }

        $data = ClinicSession::where('clinic_id', $clinic_id)->get();

        $message = __('clinic.clinic_session_added');
        if($request->is('api/*')){
            return response()->json(['message' => $message, 'data' => $data,  'status' => true], 200);
        }
        return redirect()->route('backend.clinics.index')->with('success', $message);
        // return response()->json(['message' => $message, 'data' => $data,  'status' => true], 200);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        
        $clinic = Clinics::find($id);
        $sessions = ClinicSession::where('clinic_id', $id)->get();

        $weekdays = [];
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        foreach ($days as $day) {
            $session = $sessions->where('day', $day)->first();
            $breaks = [];
            if ($session) {
                if (is_string($session->breaks)) {
                    $breaks = json_decode($session->breaks, true) ?? [];
                } elseif (is_array($session->breaks)) {
                    $breaks = $session->breaks;
                }
            }
            $weekdays[] = [
                'day' => $day,
                'start_time' => $session->start_time ?? '09:00',
                'end_time' => $session->end_time ?? '18:00',
                'is_holiday' => $session->is_holiday ?? false,
                'breaks' => $breaks,
                'id' => $session->id ?? null,
            ];
        }

        return view('clinic::backend.clinic.clinic_session_offcanvas', compact('clinic', 'weekdays'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('clinic::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    
}
