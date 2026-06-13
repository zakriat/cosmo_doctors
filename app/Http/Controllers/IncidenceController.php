<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncidenceRequest;
use App\Mail\IncidenceMail;
use App\Models\Incidence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\NotificationTemplate\Models\NotificationTemplate;

class IncidenceController extends Controller
{
    use AppointmentTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // print_r(Auth::user()->profile_image);
        $user_id = $request->user_id ?? Auth::user()->id;
        $perPage = isset(request()->per_page) ? request()->per_page : 25;
        $data = Incidence::selectRaw('incidences.*,CONCAT(first_name," ",last_name) as name')
            ->leftJoin('users', 'users.id', '=', 'incidences.user_id')->where('incidences.user_id', $user_id)
            ->paginate($perPage);
        return response()->json(['data' => $data, 'status' => true]);
        // if (request()->wantsJson()) {
        //     return response()->json(['data' => $data, 'status' => true]);
        // }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IncidenceRequest $request)
    {
        $reqdata = $request->all();
        $reqdata['user_id'] = Auth::user()->id;
        $reqdata['profile_image'] = Auth::user()->profile_image;
        $reqdata['created_by'] = Auth::user()->id;
        $reqdata['incident_date'] = date('Y-m-d');

        $reqdata['phone'] = $reqdata['country_code'] . ' ' . $reqdata['phone'];
        $data = Incidence::create($reqdata);
        if ($request->hasFile('file_url')) {
            storeMediaFile($data, $request->file('file_url'));
        }

        self::sendNotificationOnIncidence($data);

        return response()->json(['status' => true, 'message' => 'Successfully created!']);
    }

    public function sendNotificationOnIncidence($data)
    {
        $createdBy = User::selectRaw('CONCAT(first_name," ",last_name) as name')->where('id', $data->created_by)->pluck('name')->first();

        $notification_data = [
            'title' => $data->title,
            'description' => $data->description,
            'phone' => $data->phone,
            'email' => $data->email,
            'user_name' => $createdBy
        ];


        $template = NotificationTemplate::where('type', 'new_incidence')->with('defaultNotificationTemplateMap')->firstOrFail();

        $mail_template = $template->defaultNotificationTemplateMap->mail_template_detail ?? '<p>New incidence report created.</p> <p>Title:  [[ title ]]  ,  Description: [[ description ]] and Phone: [[ phone ]] , Email: [[ email ]] and Created By: [[ user_name ]] </p>';

        // Replace [[ reply ]] and other keys in the template with values from $notification_data
        $bodyData = $mail_template;
        foreach ($notification_data as $key => $value) {
            $bodyData = str_replace('[[ ' . $key . ' ]]', $value, $bodyData);
            $bodyData = str_replace('[[ ' . $key . ' ]]', $value, $bodyData); // handle both with and without space
        }

        try {
            Mail::to($data->email)->send(new IncidenceMail($bodyData));
        } catch (\Exception $e) {
            \Log::error('Mail not sent: ' . $e->getMessage());
        }

        $this->sendNotificationOnIncidenceCreate('new_incidence', $notification_data);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(IncidenceRequest $request, $id)
    {
        $reqdata = $request->all();
        $data = Incidence::find($id);
        if (!$data) {
            return response()->json(['message' => 'Invalid incidence referance!', 'status' => false]);
        }
        $reqdata['user_id'] = Auth::user()->id;
        $reqdata['updated_by'] = Auth::user()->id;
        $reqdata['profile_image'] = Auth::user()->profile_image;
        $reqdata['phone'] = $reqdata['country_code'] . $reqdata['phone'];

        $data->update($reqdata);
        return response()->json(['message' => 'Successfully updated!', 'status' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $data = Incidence::find($id);
        if (!$data) {
            return response()->json(['message' => 'Invalid incidence referance!', 'status' => false]);
        }
        $data->delete();

        return response()->json(['message' => 'Successfully deleted!', 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function changeStatus(Request $request, $id)
    {
        $reqdata = $request->all();
        // $validator = Validator::make($reqdata, [
        //     'status' => [
        //         'required'
        //     ],
        //     'incident_type' => 'nullable'
        // ]);

        // if ($validator->fails())
        // {
        //     return response()->json(['errors'=>$validator->errors()]);
        // }

        $data = Incidence::find($id);


        if (!$data) {
            return response()->json(['message' => 'Invalid incidence referance!', 'status' => false]);
        }

        if (!isset($reqdata['status']) && !isset($reqdata['incident_type'])) {
            return response()->json(['message' => 'Invalid request!', 'status' => false]);
        }

        isset($reqdata['status']) && $attributes['status'] = $reqdata['status'];
        isset($reqdata['incident_type']) && $attributes['incident_type'] = $reqdata['incident_type'];
        (isset($reqdata['incident_type']) && $reqdata['incident_type'] == 2) && $attributes['incident_closed_date'] = date('Y-m-d');

        $data->update($attributes);
        return response()->json(['message' => 'Successfully status changed!', 'status' => true]);
    }

    public function updateIncidentStatus(Request $request, $id)
    {
        $reqdata = $request->all();

        $data = Incidence::find($id);

        if (! $data) {
            return response()->json(['message' => 'Invalid incidence reference!', 'status' => false]);
        }

        // Update the incident_type if provided
        if (isset($reqdata['incident_type'])) {
            $data->incident_type = $reqdata['incident_type'];
        }

        // Update the status if provided
        if (isset($reqdata['status'])) {
            $data->status = $reqdata['status'];
        }

        $data->save();

        return response()->json(['message' => 'Successfully updated!', 'status' => true]);
    }
}
