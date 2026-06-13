<?php

namespace Modules\Earning\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Earning\Models\Earning;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use App\Models\User;
use Currency;
use Modules\Commission\Models\CommissionEarning;
use Modules\Appointment\Models\Appointment;
use Modules\Earning\Models\EmployeeEarning;

class EarningsController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'earning.lbl_doctor_earnings';
        // module name
        $this->module_name = 'earnings';

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
        $columns = CustomFieldGroup::columnJsonValues(new Earning());
        $customefield = CustomField::exportCustomFields(new Earning());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => ' Name',
            ]
        ];
        $export_url = route('backend.earnings.export');

        return view('earning::backend.earnings.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
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

        $query_data = Earning::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name.' (Slug: '.$row->slug.')',
            ];
        }
        return response()->json($data);
    }

    public function index_data(DataTables $datatable)
    {
        $module_name = $this->module_name;

        $query = User::SetRole(auth()->user())->select('users.*')
                ->with('commission_earning')
                ->with('commissionData')
                ->with('doctor')
                ->with('doctorclinic')
                ->whereHas('commission_earning', function ($q) {
                    $q->where('commission_status', 'unpaid')
                    ->where('user_type', 'doctor')
                    ->where('commissionable_type', 'Modules\Appointment\Models\Appointment');
                });

        return $datatable->eloquent($query)
            ->addColumn('action', function ($data) use ($module_name) {
                $commissionData = $data->commission_earning()
                    ->whereHas('getAppointment', function ($query) {
                        $query->where('status', 'checkout');
                    })
                    ->where('commission_status', 'unpaid')
                    ->where('user_type', 'doctor');

                $commissionAmount = $commissionData->sum('commission_amount');
                $totalAppointment = $commissionData->distinct('commissionable_id')->count();
                
                $data['total_pay'] = $commissionAmount;
                $data['commission'] = $commissionData->get();
                $data['total_appointment'] = $totalAppointment;
            
                return view('earning::backend.earnings.action_column', compact('module_name', 'data'));
            })

            ->editColumn('user_id', function ($data) {
                return view('earning::backend.earnings.user_id', compact('data'));
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', '%'.$keyword.'%')->orWhere('last_name', 'like', '%'.$keyword.'%')->orWhere('email', 'like', '%'.$keyword.'%');
                }
            })
            ->orderColumn('user_id', function ($query, $order) {
                $query->orderBy('first_name', $order)
                      ->orderBy('last_name', $order);
            }, 1)

            ->editColumn('total_appointment', function ($data) {

                if($data->total_appointment >0){

                    return "<b><a href='" . route('backend.appointments.index', ['doctor_id' => $data->id]) . "' data-assign-module='".$data->id."'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='View Doctor Appointments'>".$data->total_appointment."</a> </b>";
                }else{

                    return "<b><span  data-assign-module='".$data->id."'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='View Doctor Appointments'>0</span>";
                }

            })
            ->editColumn('total_service_amount', function ($data) {

          
                $totalServiceAmount = 0;
                foreach($data['commission'] as $commission){

                if($commission->commission_status != 'pending'){

                    $appointmentData = Appointment::where('id', $commission->commissionable_id)->with('appointmenttransaction')->first();

                    $totalServiceAmount += $appointmentData->appointmenttransaction->total_amount;

                }

                   

                }
                $data['totalServiceAmount'] = $totalServiceAmount;

        
           return Currency::format($totalServiceAmount);
            })
            
            ->editColumn('total_admin_earning', function ($data) {
                 
                $totalAdminEarning = 0;
                foreach($data['commission'] as $commission){
                    $commission_data = CommissionEarning::where('commissionable_id', $commission->commissionable_id)->where('user_type', 'admin')->where('commission_status', 'unpaid')->first();
                    $totalAdminEarning += $commission_data->commission_amount;
                }
             

                return Currency::format($totalAdminEarning);
            })


            ->editColumn('total_commission_earn', function ($data) {

               return "<b><span  data-assign-module='".$data->id."' data-assign-commission-type='doctor_commission' data-assign-target='#view_commission_list' data-assign-event='assign_commssions' class='btn text-primary p-0 fs-5' data-bs-toggle='tooltip' title='View'> <i class='ph ph-eye align-middle'></i></span>";
                
            })
            ->editColumn('total_pay', function ($data) {
         
                return Currency::format($data->total_pay);
            })
            
            ->orderColumn('total_service_amount', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT SUM(service_price) FROM booking_services WHERE employee_id = users.id)'), $order);
            }, 1)
            // ->orderColumn('total_commission_earn', function ($query, $order) {
            //     $query->orderBy(new Expression('(SELECT SUM(commission_amount) FROM commission_earnings WHERE employee_id = users.id)'), $order);
            // }, 1)
            ->addIndexColumn()
            ->rawColumns(['action', 'image','user_id','total_commission_earn','total_appointment'])
            ->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = Earning::create($request->all());

        $message = 'New Earning Added';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id, Request $request)
    {
        $commissionType = $request->commission_type;
        $userType = ($commissionType == 'doctor_commission') ? 'doctor' : 'vendor';
    
        $query = User::where('id', $id)
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.mobile')
            ->with('commission_earning')
            ->with('commissionData')
            ->whereHas('commission_earning', function ($q) use ($userType) {
                $q->where('commission_status', 'unpaid')
                    ->where('user_type', $userType)
                    ->where('commissionable_type', 'Modules\Appointment\Models\Appointment');
            })
            ->orderBy('updated_at', 'desc')
            ->first();
    
        $commissionData = $query->commission_earning()
            ->whereHas('getAppointment', function ($query) {
                $query->where('status', 'checkout');
            })
            ->where('commission_status', 'unpaid')
            ->where('user_type', $userType);
    
        $commissionAmount = $commissionData->sum('commission_amount');
        
        $data = [
            'id' => $query->id,
            'full_name' => $query->full_name,
            'email' => $query->email,
            'mobile' => $query->mobile,
            'profile_image' => $query->profile_image,
            'description' => '',
            'commission_earn' => Currency::format($commissionAmount),
            'amount' => Currency::format($commissionAmount),
            'payment_method' => '',
        ];
    
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
        $data = $request->all();

        $commissionType = $request->commission_type;
        $userType = ($commissionType == 'doctor_commission') ? 'doctor' : 'vendor';

        $query = User::where('id', $id)
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.mobile')
            ->with('commission_earning')
            ->with('commissionData')
            ->whereHas('commission_earning', function ($q) use ($userType) {
                $q->where('commission_status', 'unpaid')
                    ->where('user_type', $userType)
                    ->where('commissionable_type', 'Modules\Appointment\Models\Appointment');
            })
            ->orderBy('updated_at', 'desc')
            ->first();
    
        $commissionData = $query->commission_earning()
            ->whereHas('getAppointment', function ($query) {
                $query->where('status', 'checkout');
            })
            ->where('commission_status', 'unpaid')
            ->where('user_type', $userType);
    
        $commissionAmount = $commissionData->sum('commission_amount');
        $total_commission_earn = $commissionAmount;
        $total_pay = $total_commission_earn;


        $earning_data = [
            'employee_id' => $id,
            'total_amount' => $total_pay,
            'payment_date' => Carbon::now(),
            'payment_type' => $data['payment_method'],
            'description' => $data['description'],
            'commission_amount' => $total_commission_earn,
            'user_type' => $userType,
        ];

        $earning_data = EmployeeEarning::create($earning_data);

        CommissionEarning::where('employee_id', $id)->where('commission_status','unpaid')->update(['commission_status' => 'paid']);

        $message = __('messages.payment_done');

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
        $data = Earning::findOrFail($id);

        $data->delete();

        $message = 'Earnings Deleted Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function get_employee_commissions(Request $request){

        if($request->has('type') && $request->type !='' && $request->has('id') && $request->id !='' ){
 
         $type = $request->type;
         $data =  User::where('id', $request->id)->with(['commissionData' => function($query) use ($type) {
                     $query->whereHas('mainCommission', function($subQuery) use ($type) {
                         $subQuery->where('type', $type);
                     });
                 }])->first();
        }   
 
        return response()->json(['data' => $data, 'status' => true]);
     
         
     }
}
