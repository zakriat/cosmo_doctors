<?php

namespace Modules\CustomForm\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\CustomForm\Models\CustomForm;
use Modules\CustomForm\Models\CustomFormData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use Modules\CustomForm\Transformers\CustomFormResource;
use Modules\CustomForm\Transformers\CustomFormDataResource;



class CustomFormsController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'customform.title';
        // module name
        $this->module_name = 'customform';

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
        $columns = CustomFieldGroup::columnJsonValues(new CustomForm());
        $customefield = CustomField::exportCustomFields(new CustomForm());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => ' Name',
            ]
        ];
        $export_url = route('backend.customforms.export');

        return view('customform::backend.customforms.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
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

        $query_data = CustomForm::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name . ' (Slug: ' . $row->slug . ')',
            ];
        }
        return response()->json($data);
    }

    public function index_data(Request $request)
    {
        $perPage = $request->get('perPage', 1);
        $currentPage = $request->get('page', 1);
        $data = CustomForm::paginate($perPage, ['*'], 'page', $currentPage);

        $form_data = [];

        $form_data = $data->map(function ($d) {
            $decode_data = json_decode($d->formdata);
            return [
                'id' => $d->id,
                'name' => $decode_data->form_title ?? null,
                'status' => $d->status,
                'type' => $d->module_type,
            ];
        });

        return response()->json([
            'data' => $form_data,
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
            'links' => [
                'prev' => $data->previousPageUrl(),
                'next' => $data->nextPageUrl(),
            ],
            'status' => true,
            'message' => __('messages.custome_form_list'),
        ]);
    }


    public function getCustomForm(Request $request)
    {

        $from_id = $request->form_id;
        $appointent_id = $request->appointment_id;
        $type = $request->type;


        $data = CustomForm::find($from_id);

        if (!$data) {
            return response()->json(['data' => null, 'status' => false, 'message' => __('messages.custom_form_not_found')]);
        }

        $form_data = json_decode($data->formdata, true);
        $field_data = json_decode($data->fields, true);

        $formdata = CustomFormData::where('form_id', $from_id)->where('module_id', $appointent_id)->where('module', $type)->first();
        // dd(json_decode($formdata->form_data));
        if ($formdata) {
            $field_data = json_decode($formdata->form_data);

        }

        $custom_form = [
            'form_title' => $form_data['form_title'] ?? '',
            'form_icon' => $form_data['form_icon'] ?? null,
            'title_alignment' => $form_data['title_alignment'] ?? 'center',
            'title_color' => $form_data['title_color'] ?? 'text-primary',
            'field_data' => $field_data ?? []
        ];

        return response()->json(['data' => $custom_form, 'status' => true, 'message' => __('messages.custome_form_list')]);
    }

    public function getCustomFormList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $items = CustomForm::query();

        $items = $items->orderBy('updated_at', 'desc');
        $items = $items->paginate($perPage);

        $responseData = CustomFormResource::collection($items);

        $data = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'custom_form_data' => $responseData,
        ];

        return response()->json(
            [
                'status' => true,
                'message' => __('messages.custom_form_list'),
                'data' => $data,
            ]
        );
        // dd($data);
    }


    public function getCustomFormDataList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $items = CustomFormData::query();

        if ($request->has('form_id') && isset($request->form_id)) {
            $items = $items->where('form_id', $request->form_id);
        }

        $items = $items->orderBy('updated_at', 'desc');
        $items = $items->paginate($perPage);

        $responseData = CustomFormDataResource::collection($items);

        $data = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'custom_form_submited_data' => $responseData,
        ];

        return response()->json(
            [
                'status' => true,
                'message' => __('messages.custom_form_list'),
                'data' => $data,
            ]
        );
        // dd($data);
    }



    public function StoreFormData(Request $request)
    {

        $data = $request->all();

        $from_data = CustomFormData::updateOrCreate(
            [
                'form_id' => $data['form_id'],
                'module_id' => $data['module_id'],
                'module' => $data['module'],
            ],
            [
                'form_data' => $data['form_data'],

            ]
        );


        $message = __('messages.create_form', ['form' => __('messages.custom_form')]);

        return response()->json(['message' => $message, 'data' => $from_data, 'status' => true], 200);

    }





    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {

        $formdata = [

            'form_title' => $request->form_title,
            'form_icon' => $request->form_icon,
            'title_alignment' => $request->title_alignment,
            'form_icon' => $request->form_icon,
            'title_color' => $request->title_color,

        ];

        $data['formdata'] = json_encode($formdata);
        $data['module_type'] = $request->module_type;
        $data['fields'] = $request->formFields;
        $data['show_in'] = $request->show_in;
        $data['appointment_status'] = $request->appointment_status;
        $data['status'] = $request->status;

        $data = CustomForm::create($data);

        $message = 'New CustomForm Added';

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

        $data = CustomForm::findOrFail($id);

        $form_data = [];

        if ($data) {

            $formDetails = json_decode($data->formdata);

            $form_data['form_title'] = $formDetails->form_title ?? null;
            $form_data['form_icon'] = $formDetails->form_icon ?? null;
            $form_data['title_alignment'] = $formDetails->title_alignment ?? null;
            $form_data['title_color'] = $formDetails->title_color ?? null;
            $form_data['module_type'] = $data->module_type ?? null;
            $form_data['fields'] = $data->fields ?? [];
            $form_data['show_in'] = json_decode($data->show_in) ?? [];
            $form_data['appiontment_status'] = json_decode($data->appointment_status) ?? null;
            $form_data['status'] = json_decode($data->status) ?? null;
        }

        // Return the response as JSON
        return response()->json(['data' => $form_data, 'status' => true]);
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
        // Find the existing CustomForm by ID
        $data = CustomForm::findOrFail($id);

        // Prepare the form data for JSON encoding
        $formdata = [
            'form_title' => $request->form_title,
            'form_icon' => $request->form_icon,
            'title_alignment' => $request->title_alignment,
            'title_color' => $request->title_color,
        ];

        // Update the fields
        $data->formdata = json_encode($formdata);
        $data->module_type = $request->module_type;
        $data->fields = $request->formFields;
        $data->show_in = $request->show_in;
        $data->appointment_status = $request->appointment_status;
        $data->status = $request->status;

        // Save the updated data
        $data->save();

        // Prepare the success message
        $message = 'CustomForm Updated Successfully';

        // Return a success response
        return response()->json(['message' => $message, 'status' => true]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

        CustomFormData::where('form_id', $id)->delete();

        $data = CustomForm::find($id);

        $data->delete();

        $message = 'CustomForms Deleted Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
