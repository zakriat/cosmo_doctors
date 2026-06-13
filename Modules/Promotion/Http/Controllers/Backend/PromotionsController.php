<?php

namespace Modules\Promotion\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Promotion\Models\Coupon;
use Modules\Promotion\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;

class PromotionsController extends Controller
{
    // use Authorizable;
    protected string $exportClass = '\App\Exports\CustomerExport';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'promotion.title';
        // module name
        $this->module_name = 'promotions';

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
        $columns = CustomFieldGroup::columnJsonValues(new Promotion());
        $customefield = CustomField::exportCustomFields(new Promotion());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => ' Name',
            ], [
                'value' => 'start_date',
                'text' => ' start date',
            ],
            [
                'value' => 'promo_end_date',
                'text' => ' end date',
            ]

        ];

        $export_url = route('backend.promotions.export');

        return view('promotion::backend.promotions.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
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

        $query_data = Promotion::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

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
        $module_name = $this->module_name;
        $query = Promotion::query();

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        $query->orderBy('created_at', 'desc');

        return Datatables::of($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('promotion::backend.promotions.action_column', compact('data'));
            })
            ->editColumn('status', function ($data) {
                // return $data->getStatusLabelAttribute();
                $checked = '';
                if ($data->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.promotions.update_status', $data->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $data->id . '"  name="status" value="' . $data->id . '" ' . $checked . '>
                    </div>
                ';
            })
            ->editColumn('updated_at', function ($data) {


                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->rawColumns(['action', 'status', 'check'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    public function update_status(Request $request, Promotion $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => 'Status Updated']);
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

        $promotion = Promotion::create($request->all());

        $couponData = $data;
        $couponData['promotion_id'] = $promotion->id;

        if ($request->coupon_type == "custom") {
            $couponData['coupon_code'] = $request->coupon_code;
            $this->createCoupon($couponData);
        } else {
            for ($i = 1; $i <= $request->number_of_coupon ?? 1; $i++) {
                $couponData['coupon_code'] = strtoupper(randomString(8));
                $this->createCoupon($couponData);
            }
        }

        $message = 'New Promotion Added';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    protected function createCoupon($data) {
        return Coupon::create($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {

        $data = Promotion::with('coupon')->findOrFail($id);

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
        $data = Promotion::findOrFail($id);
        $data->update($request->all());

        $coupon = Coupon::where('promotion_id', $id)->first();

        $couponData = [
            "discount_type" => $request->discount_type,
        ];

        if($coupon->used_by==null){
            $couponData = [
                "start_date_time" => $request->start_date_time,
                "end_date_time" => $request->end_date_time,
                "use_limit"=>$request->use_limit,
            ];

        }


        if ($request->discount_type == "percent") {
            $couponData['discount_amount'] = 0;
            $couponData['discount_percentage'] = $request->discount_percentage;
        } else {
            $couponData['discount_amount'] = $request->discount_amount;
            $couponData['discount_percentage'] = 0;
        }

        Coupon::where('promotion_id', $id)->update($couponData);
        $message = 'Promotions Updated Successfully';

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
        $data = Promotion::findOrFail($id);

        $coupon = Coupon::where('promotion_id', $id);
        $coupon->delete();

        $data->delete();

        $message = 'Promotions Deleted Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }


    public function couponValidate(Request $request)
    {
        // dd($request->all());
        $now = now();
        $coupon = Coupon::where('coupon_code', $request->coupon_code)
            ->where('end_date_time', '>=', $now)
            ->where('is_expired', '!=', '1')
            ->first();

        if (!$coupon) {

            $message = 'coupon not valid';
            return ['valid' => false, 'message' => $message,'status' => false ];
        }



        $data = [
            'coupon_code'=>$coupon->coupon_code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_amount,
            'discount_percentage' => $coupon->discount_percentage,
        ];
        $message = 'coupon valid';
        return response()->json(['message' => $message, 'data'=>$data, 'status' => true ,'valid'=>true], 200);
    }


    public function couponsview($id)
    {

       $promotion_id=$id;
        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new Promotion());
        $customefield = CustomField::exportCustomFields(new Promotion());

        $export_import = true;
        $va=Coupon::where('promotion_id',$promotion_id)->pluck('coupon_code')->toArray();
        // $export_columns = [];


        $export_columns = [
            [
                'value' => 'coupon_code'.','.$promotion_id,
                'text' => 'coupon code',
            ],
        ];

        $export_url = route('backend.coupons.export', $promotion_id);



        return view('promotion::backend.promotions.coupon_datatable', compact('module_action', 'export_import', 'export_columns', 'export_url','promotion_id'));

    }

public function coupon_data(Request $request,$id)
{
    $module_name = $this->module_name;

    $query = Coupon::where('promotion_id',$id);



    $filter = $request->filter;

    if (isset($filter)) {
        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }
    }

    return Datatables::of($query)
        ->addColumn('check', function ($data) {
            return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
        })
        ->addColumn('action', function ($data) {
            return view('promotion::backend.promotions.action_column', compact('data'));
        })
        ->editColumn('value', function ($data) {

            if ($data->discount_type === 'fixed') {
                $value = \Currency::format($data->discount_amount ?? 0);

                return $value;
            }
            if ($data->discount_type === 'percent') {
                $value = $data->discount_percentage.'%';

                return $value;
            }
        })
        ->editColumn('is_expired', function ($data) {

            return $data->is_expired === 1 ? 'Yes' : 'No';

        })
        ->rawColumns(['action', 'status', 'check'])
        ->orderColumns(['id'], '-:column $1')
        ->make(true);
}



public function couponExport(Request $request, $id)
{
    $this->exportClass = '\App\Exports\couponsExport';





    return $this->export($request);
}

}



