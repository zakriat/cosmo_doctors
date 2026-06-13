<?php

namespace Modules\RequestService\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Modules\RequestService\Models\RequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Models\SystemService;

class RequestServicesController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'service.request_service';
        // module name
        $this->module_name = 'requestservices';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new RequestService());
        $customefield = CustomField::exportCustomFields(new RequestService());

        return view('requestservice::backend.requestservices.index_datatable', compact('module_action', 'filter', 'columns', 'customefield'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $term = trim($request->q);

        if (empty($term)) {
            return response()->json([]);
        }

        $query_data = RequestService::where('title', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->title . ' (Slug: ' . $row->slug . ')',
            ];
        }
        return response()->json($data);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $module_name = $this->module_name;
        $query = RequestService::query();
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
            $query->where('is_status', 'pending');
        }else if (auth()->user()->hasRole('vendor')){
            $query->where('vendor_id', auth()->id());
        }
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }
                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.requestservices.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
                    </div>
                ';
            })
            ->editColumn('type', function ($data) {
                $typeKey = 'type_' . $data->type;
                return __('service.' . $typeKey);
            })
            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->addColumn('action', function ($data) {
                $module_name = $this->module_name;
                return view('requestservice::backend.requestservices.action_column', compact('module_name', 'data'));
            })
            ->orderColumns(['id'], '-:column $1');

        $customFieldColumns = CustomField::customFieldData($datatable, RequestService::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge([ 'status', 'type', 'check', 'action'], $customFieldColumns))
            ->toJson();
    }


    public function update_status(Request $request, RequestService $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' =>  __('service.request_service_status')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {

        $data = $request->all();
        if ($request->has('vendor_id')) {
            $data['vendor_id'] = $request->input('vendor_id');
        } else {
            $user = Auth::user();
            $data['vendor_id'] = $user->id;
        }
        $data['is_status'] = 'pending';
        $data = RequestService::create($data);
        $message = __('messages.create_form', ['form' => __('service.request_title')]);

        if ($request->is('api/*')) {
            $notification_data = [
                'id' => $data->id,
                'name' => $data->name,
                'description' => $data->description,
                'type' => $data->type,
                'vendor_id' => $data->vendor_id,
            ];
            sendNotificationOnBookingUpdate('new_request_service', $notification_data);
            return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
        } else {
            $notification_data = [
                'id' => $data->id,
                'name' => $data->name,
                'description' => $data->description,
                'type' => $data->type,
                'vendor_id' => $data->vendor_id,
            ];
            sendNotificationOnBookingUpdate('new_request_service', $notification_data);
            return response()->json(['message' => $message, 'status' => true], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = RequestService::findOrFail($id);

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = RequestService::findOrFail($id);

        $data->update($request->all());

        $message = 'RequestServices Updated Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = RequestService::findOrFail($id);
        if ($data !== null) {
            $data->is_status = 'rejected';
            $data->save();
        }
        $notification_data = [
            'id' => $id,
            'name' => $data->name,
            'description' => $data->description,
            'type' => $data->type,
            'vendor_id' => $data->vendor_id,
        ];
        sendNotificationOnBookingUpdate('reject_request_service', $notification_data);
        $message = 'Request Service has been deleted successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
