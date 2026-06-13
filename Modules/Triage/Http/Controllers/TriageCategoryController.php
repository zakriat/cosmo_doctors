<?php

namespace Modules\Triage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Triage\Models\TriageCategory;
use Modules\Triage\Models\TriageItem;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class TriageCategoryController extends Controller
{
    public function __construct()
    {
        $this->module_title = 'triage.categories';
        $this->module_name  = 'triage-category';

        view()->share([
            'module_title' => $this->module_title,
            'module_name'  => $this->module_name,
            'module_icon'  => 'ph ph-tag',
        ]);

        $this->middleware(['permission:view_triage_category'])->only('index');
        $this->middleware(['permission:add_triage_category'])->only('store');
        $this->middleware(['permission:edit_triage_category'])->only('update', 'update_status');
        $this->middleware(['permission:delete_triage_category'])->only('destroy');
    }

    public function index()
    {
        return view('triage::backend.triage_category.index');
    }

    public function index_data(DataTables $datatable)
    {
        $query = TriageCategory::withCount('allItems')->orderBy('display_order');

        return $datatable->eloquent($query)
            ->addColumn('check', fn($row) =>
                '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">'
            )
            ->addColumn('action', fn($row) =>
                view('triage::backend.triage_category.action_column', ['data' => $row, 'module_name' => $this->module_name])
            )
            ->editColumn('is_active', fn($row) =>
                '<div class="form-check form-switch">
                    <input type="checkbox" data-url="' . route('backend.triage-category.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input" name="is_active" value="' . $row->id . '" ' . ($row->is_active ? 'checked' : '') . '>
                </div>'
            )
            ->editColumn('updated_at', fn($row) =>
                Carbon::now()->diffInHours($row->updated_at) < 25
                    ? $row->updated_at->diffForHumans()
                    : $row->updated_at->isoFormat('llll')
            )
            ->rawColumns(['check', 'action', 'is_active'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'display_order' => 'nullable|integer',
        ]);

        $category = TriageCategory::create([
            'name'          => $request->name,
            'display_order' => $request->display_order ?? 0,
            'is_active'     => true,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.create_form', ['form' => __('triage.category_singular')]),
            'data'    => $category,
        ]);
    }

    public function edit($id)
    {
        $data = TriageCategory::findOrFail($id);
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'display_order' => 'nullable|integer',
        ]);

        $category = TriageCategory::findOrFail($id);
        $category->update([
            'name'          => $request->name,
            'display_order' => $request->display_order ?? $category->display_order,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.update_form', ['form' => __('triage.category_singular')]),
        ]);
    }

    public function destroy($id)
    {
        TriageCategory::findOrFail($id)->delete();
        return response()->json([
            'status'  => true,
            'message' => __('messages.delete_form', ['form' => __('triage.category_singular')]),
        ]);
    }

    public function update_status(Request $request, $id)
    {
        $category = TriageCategory::findOrFail($id);
        $category->update(['is_active' => $request->status]);
        return response()->json(['status' => true, 'message' => __('service_providers.status_update')]);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        switch ($request->action_type) {
            case 'change-status':
                TriageCategory::whereIn('id', $ids)->update(['is_active' => $request->status]);
                break;
            case 'delete':
                TriageCategory::whereIn('id', $ids)->delete();
                break;
            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
        }

        return response()->json(['status' => true, 'message' => __('triage.bulk_update')]);
    }
}
