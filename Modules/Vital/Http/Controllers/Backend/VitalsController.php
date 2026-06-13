<?php

namespace Modules\Vital\Http\Controllers\Backend;
use App\Models\User;
use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Vital\Models\Vital;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;

class VitalsController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'vital.title';
        // module name
        $this->module_name = 'vitals';

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
        $columns = CustomFieldGroup::columnJsonValues(new Vital());
        $customefield = CustomField::exportCustomFields(new Vital());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => ' Name',
            ]
        ];
        $export_url = route('backend.vitals.export');

        return view('vital::backend.vitals.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
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

        $query_data = Vital::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name.' (Slug: '.$row->slug.')',
            ];
        }
        return response()->json($data);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Vital::with('customer');
        $module_name = $this->module_name;
    //     $filter = $request->filter;
    //     if (isset($filter)) {
    //     if (isset($filter['height_cm'])) {
    //             $query->where('height_cm',$filter['height_cm']);

    //     }
    // }
    $query->orderBy('created_at', 'desc');

        return Datatables::of($query)
                        ->addColumn('check', function ($data) {
                            return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" onclick="dataTableRowCheck('.$data->id.')">';
                        })->addColumn('action', function ($data) {
                            return view('vital::backend.vitals.action_column', compact('data'));
                        })
                        ->editColumn('image', function ($data) {
                        //
                        return "<img src='".$data->customer->profile_image."'class='avatar avatar-50 rounded-pill me-3'>"."  ".$data->customer->first_name." ".$data->customer->last_name;

                        })
                        ->editColumn('status', function ($data) {
                            return $data->getStatusLabelAttribute();
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
                        ->rawColumns(['action', 'status', 'check','image','first_name','last_name'])
                        ->orderColumns(['id'], '-:column $1')
                        ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $vital = Vital::create($request->all());

        $message = 'New Vital Added';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Vital::findOrFail($id);

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

        $data = Vital::findOrFail($id);

        $data->update($request->all());

        $message = 'Vitals Updated Successfully';

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
        $data = Vital::findOrFail($id);

        $data->delete();

        $message = 'Vitals Deleted Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'delete':
                Vital::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_booking_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('booking.booking_action_invalid')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }
}
