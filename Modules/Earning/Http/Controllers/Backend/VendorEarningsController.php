<?php

namespace Modules\Earning\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use Modules\Earning\Models\Earning;
use App\Models\User;
use Currency;
use Modules\Commission\Models\CommissionEarning;
use Modules\Appointment\Models\Appointment;
use Modules\Earning\Models\EmployeeEarning;
use Modules\Clinic\Models\Clinics;

class VendorEarningsController extends Controller
{
    public function __construct()
    {
        // Page Title
        $this->module_title = 'earning.clinicadmin_earning';
        // module name
        $this->module_name = 'vendor-earnings';

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
        $export_url = route('backend.vendor-earnings.export');

        return view('earning::backend.vendor-earnings.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
    }

    public function index_data(DataTables $datatable)
    {
        // dd('test');
        $module_name = $this->module_name;

        $query = User::select('users.*')
                ->with('commission_earning')
                ->with('commissionData')
                ->whereHas('commission_earning', function ($q) {
                    $q->where('commission_status', 'unpaid')
                    ->where('user_type', 'vendor')
                    ->where('commissionable_type', 'Modules\Appointment\Models\Appointment');
                })->orderBy('updated_at', 'desc');

        return $datatable->eloquent($query)
            ->addColumn('action', function ($data) use ($module_name) {
                $commissionData = $data->commission_earning()
                    ->whereHas('getAppointment', function ($query) {
                        $query->where('status', 'checkout');
                    })
                    ->where('commission_status', 'unpaid')
                    ->where('user_type', 'vendor');

                
                $commissionAmount = $commissionData->sum('commission_amount');
                $totalAppointment = $commissionData->distinct('commissionable_id')->count();
                
                $data['total_pay'] = $commissionAmount;
                $data['commission'] = $commissionData->get();
                $data['total_appointment'] = $totalAppointment;
            
                return view('earning::backend.vendor-earnings.action_column', compact('module_name', 'data'));
            })

            ->editColumn('user_id', function ($data) {
                return view('earning::backend.vendor-earnings.user_id', compact('data'));
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', '%'.$keyword.'%')->orWhere('last_name', 'like', '%'.$keyword.'%')->orWhere('email', 'like', '%'.$keyword.'%');
                }
            })
            ->orderColumn('user_id', function ($query, $order) {
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) $order");
            }, 1)  

            ->editColumn('total_appointment', function ($data) {

                if($data->total_appointment >0){
                    $clinic_ids = Clinics::where('vendor_id', $data->id)->pluck('id')->toArray();
                    $clinic_ids_string = implode(',', $clinic_ids);

                    return "<b><a href='" . route('backend.appointments.index', ['clinic_id' => $clinic_ids_string]) . "' data-assign-module='".$data->id."'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='View Clinic Admin Appointments'>".$data->total_appointment."</a> </b>";
                }else{

                    return "<b><span  data-assign-module='".$data->id."'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='View Clinic Admin Appointments'>0</span>";
                }

            })
            ->editColumn('total_service_amount', function ($data) {
                $totalServiceAmount = 0;
                foreach($data['commission'] as $commission){
                    $appointmentData = Appointment::where('id', $commission->commissionable_id)->first();

                    $totalServiceAmount += $appointmentData->total_amount;
                }
                $data['totalServiceAmount'] = $totalServiceAmount;

                return Currency::format($totalServiceAmount);
            })

            ->editColumn('total_admin_earning', function ($data) {
                 
                $totalAdminEarning = 0;

                foreach($data['commission'] as $commission){ 

                    $commission_data = CommissionEarning::where('commissionable_id', $commission->commissionable_id)->where('user_type', 'admin')->where('commission_status', 'unpaid')->first();
                    $totalAdminEarning += $commission_data->commission_amount ?? 0;

                }

                return Currency::format($totalAdminEarning);
            })

            ->editColumn('total_pay', function ($data) {
                return Currency::format($data->total_pay);
            })
            
            ->orderColumn('total_service_amount', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT SUM(service_price) FROM booking_services WHERE employee_id = users.id)'), $order);
            }, 1)
           
            ->addIndexColumn()
            ->rawColumns(['action', 'image','user_id','total_commission_earn','total_appointment'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('earning::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('earning::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('earning::edit');
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
