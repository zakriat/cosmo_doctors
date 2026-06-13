<?php

namespace Modules\FAQ\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\FAQ\Http\Requests\FAQRequest;
use Modules\FAQ\Models\Faqs;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Response;

class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected string $exportClass = '\App\Exports\FAQExport';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'FAQ';

        // module name
        $this->module_name = 'faqs';
    }

     public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = 'List';
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $export_import = true;
        $export_columns = [
            [
                'value' => 'question',
                'text' => 'Question',
            ]
        ];
        $export_url = route('backend.faqs.export');

        return view('faq::backend.faq.index', compact('module_action','module_title','export_url','module_name', 'filter', 'export_import', 'export_columns'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'FAQ'; // Adjust as necessary for dynamic use
        $messageKey = __('FAQ.Post_status'); // You might want to adjust this based on the action

        return $this->performBulkAction(faqs::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function performBulkAction($model, $ids, $actionType, $moduleName)
{
    $message = __('messages.bulk_update');

    switch ($actionType) {
        case 'change-status':
            $model::whereIn('id', $ids)->update(['status' => request()->status]);
            $message = trans('messages.status_updated');
            break;
        case 'delete':
            if (env('IS_DEMO')) {
                return response()->json(['message' => __('messages.permission_denied'), 'status' => false]);
            }
            $model::whereIn('id', $ids)->delete();
            $message = trans('messages.delete_form');
            break;
        case 'restore':
            $model::whereIn('id', $ids)->restore();
            $message = trans('messages.restore_form');
            break;
                
        case 'permanently-delete':
            $model::whereIn('id', $ids)->forceDelete();
            $message = trans('messages.permanent_delete_form');
            break;
        default:
            return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
    }

    return response()->json(['status' => true, 'message' => $message]);
}
    public function update_status(Request $request, faqs $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = faqs::query()->withTrashed();

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        return $datatable->eloquent($query)
            ->editColumn('question', fn($data) => $data->question)
            ->editColumn('answer', fn($data) => $data->answer)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-'.$data->id.'" name="datatable_ids[]" value="'.$data->id.'" data-type="faqs" onclick="dataTableRowCheck('.$data->id.',this)">';
            })
            ->addColumn('action', function ($data) {
                return view('faq::backend.faq.action', compact('data'));
            })
            ->editColumn('status', function ($row) {
                $checked = $row->status ? 'checked="checked"' : '';
                $disabled = $row->trashed() ? 'disabled' : ''; // Disable if soft deleted
            
                return '
                <div class="form-check form-switch ">
                    <input type="checkbox" data-url="'.route('backend.faqs.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$row->id.'" name="status" value="'.$row->id.'" '.$checked.' '.$disabled.'>
                </div>
                ';
            })          
            ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
            ->rawColumns(['action', 'status', 'check'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $module_title = __('messages.add_title');
        return view('faq::backend.faq.create',compact('module_title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FAQRequest $request)
    {
        $data = $request->all();
        $data['answer'] = strip_tags($data['answer']);
        $faq = faqs::create($data);

        $message = __('messages.create_form', ['form' => __('faq')]);
        return redirect()->route('backend.faqs.index')->with('success', $message);

    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('faq::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = faqs::findOrFail($id);
        $module_title = __('messages.edit_faq');
        return view('faq::backend.faq.edit', compact('data','module_title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FAQRequest $request, faqs $faq)
    {
        $requestData = $request->all();
        $requestData['answer'] = strip_tags($requestData['answer']);
        $faq->update($requestData);

        $message = __('messages.update_form', ['form' => __('FAQ')]);
        return redirect()->route('backend.faqs.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = faqs::findOrFail($id);
        $data->delete();

        $message = __('messages.delete_form', ['form' => __('FAQ')]);
        return response()->json(['message' => $message, 'status' => true], 200);
    }
    public function restore($id)
    {
        $data = faqs::withTrashed()->findOrFail($id);
        $data->restore();
        $message = __('messages.restore_form', ['form' => __('FAQ')]);
        return response()->json(['message' => $message, 'status' => true], 200);
    }
    public function forceDelete($id)
    {
        $data = faqs::withTrashed()->findOrFail($id);
        $data->forceDelete();

        $message = __('messages.permanently_deleted', ['form' => __('FAQ')]);
        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
