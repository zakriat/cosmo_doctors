<?php

namespace App\Http\Controllers\Backend;


use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Appointment\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use App\Models\Incidence;
use App\Models\Setting;
use Modules\NotificationTemplate\Models\NotificationTemplate;
use App\Mail\ReplyIncidenceMail;
use Illuminate\Support\Facades\Mail;
use Modules\Appointment\Trait\AppointmentTrait;


class IncidenceReportController extends Controller
{
    use AppointmentTrait;
    protected string $exportClass = '\App\Exports\CustomerExport';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'frontend.incidence';
        // module name
        $this->module_name = 'incidence';

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

    $module_action = '';
    $columns = CustomFieldGroup::columnJsonValues(new Appointment());
    $customefield = CustomField::exportCustomFields(new Appointment());

    $export_import = true;
    $export_columns = [
        [
            'value' => 'name',
            'text' => ' Name',
        ]
    ];
    $export_url = route('backend.incidence.export');

    $data = Incidence::first(); 
    return view('backend.incidence.index_datatable', compact(
        'module_action',
        'filter',
        'columns',
        'customefield',
        'export_import',
        'export_columns',
        'export_url',
        'data'
    ));
}




    public function index_data(Datatables $datatable, Request $request)
    {

        $query = Incidence::query();

        $filter = $request->filter;

        if (isset($filter) && isset($filter['column_status'])) {

            $query->where('incident_type', $filter['column_status']);
        }


        $appointment_status =  [__('messages.lbl_open') => '1', __('messages.lbl_closed') => '2', __('messages.lbl_reject') => '3'];

        // Remove the default orderBy as it conflicts with DataTable sorting
        // $query->orderBy('created_at', 'desc');

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })

            ->editColumn('image', function ($data) {
                return '<img src="' . $data->file_url . '" class="avatar avatar-50 rounded-pill me-3" style="cursor:pointer;" onclick="setPreview(\'' . addslashes($data->file_url) . '\')">';
            })
            ->addColumn('name', function ($data) {
                $createdBy = User::selectRaw('CONCAT(first_name," ",last_name) as name')->where('id', $data->user_id)->pluck('name')->first();
                return $createdBy;
            })
            ->filterColumn('name', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('name', function ($query, $order) {
                $query->leftJoin('users', 'incidences.created_by', '=', 'users.id')
                    ->orderByRaw('CONCAT(users.first_name, " ", users.last_name) ' . $order);
            })
            ->addColumn('action', function ($data) {
                return view('backend.incidence.datatable.action_column', compact('data'));
            })
            ->editColumn('status', function ($data) use ($appointment_status) {


                if ($data->incident_type == 1) {
                    return view('backend.incidence.datatable.select_column', compact('data', 'appointment_status'));
                } elseif ($data->incident_type == 2) {
                    $status = '<span class="badge bg-success">' . __('messages.lbl_closed') . '</span>';
                } elseif ($data->incident_type == 3) {
                    $status = '<span class="badge bg-danger">' . __('messages.lbl_reject') . '</span>';
                } else {
                    $status = '<span class="badge bg-secondary">' . __('messages.unknown') . '</span>';
                }
                return $status;
            })
            ->editColumn('incident_date', function ($data) {
                $setting = Setting::where('name', 'date_formate')->first();
                $dateformate = $setting ? $setting->val : 'Y-m-d';
                return $data->incident_date ? Carbon::parse($data->incident_date)->format($dateformate) : '';
            })
            ->editColumn('updated_at', function ($data) {

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->editColumn('description', function ($data) {
                $maxLength = 50; 
                $fullDescription = e($data->description);
                $shortDescription = Str::limit($fullDescription, $maxLength);

                return '<span title="' . $fullDescription . '">' . $shortDescription . '</span>';
            })
            ->rawColumns(['status', 'check', 'action', 'image','description'])
            ->orderColumns(['id'], '-:column $1');
        return $datatable->toJson();
    }

    public function updateStatus($id, Request $request)
    {
        $status = $request->value;
        $data = Incidence::find($id)->update(['incident_type' => $status]);
        if ($data) {
            $message = __('appointment.status_update');
            return response()->json(['message' => $message, 'status' => true]);
        } else {
            return response()->json(['message' => 'Somthing went wrong', 'status' => false]);
        }
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $status = $request->status;


        $message = __('messages.bulk_update');
        $data = Incidence::whereIn('id', $ids)->update(['incident_type' => $status]);
        if ($data) {
            $message = __('appointment.status_update');
            return response()->json(['status' => true, 'message' => $message]);
        } else {
            return response()->json(['message' => 'Somthing went wrong', 'status' => false]);
        }
    }

    public function reply(Request $request)
    {
        $id = $request->incidence_id;
        $Reply = $request->Reply;

        $message = __('messages.bulk_update');
        $data = Incidence::where('id', $id)->update(['reply' => $Reply]);

        $incidence_data = Incidence::where('id', $id)->first();

        self::sendNotificationOnIncidence($incidence_data);

        if ($data) {
            $message = __('appointment.reply_status');
            flash('<i class="fas fa-check"></i> ' . $message . '')->success()->important();
            return redirect()->route('backend.incidence.index')->with('success', $message);
        } else {
            $message = __('appointment.reply_fail');
            flash('<i class="fas fa-check"></i> Somthing went wrong')->error()->important();
            return redirect()->route('backend.incidence.index')->with('error', $message);
        }
    }

    public function sendNotificationOnIncidence($data)
    {
        $createdBy = User::selectRaw('CONCAT(first_name," ",last_name) as name')->where('id', $data->created_by)->pluck('name')->first();

        $notification_data = [
            'id' => $data->id,
            'user_id' => $data->created_by,
            'phone' => $data->phone,
            'email' => $data->email,
            'reply' => $data->reply,
            'user_name' => $createdBy
        ];

        $template = NotificationTemplate::where('type', 'incidence_reply')->with('defaultNotificationTemplateMap')->firstOrFail();

        $mail_template = $template->defaultNotificationTemplateMap->mail_template_detail ?? '<p>Incidence report reply from Admin.</p><p>Your reply: [[ reply ]]</p>';

        // Replace [[ reply ]] and other keys in the template with values from $notification_data
        $bodyData = $mail_template;
        foreach ($notification_data as $key => $value) {
            $bodyData = str_replace('[[ ' . $key . ' ]]', $value, $bodyData);
            $bodyData = str_replace('[[ ' . $key . ' ]]', $value, $bodyData); // handle both with and without space
        }

        try {
            Mail::to($data->email)->send(new ReplyIncidenceMail($bodyData));
        } catch (\Exception $e) {
            \Log::error('Mail not sent: ' . $e->getMessage());
        }

        $this->sendNotificationOnIncidenceCreate('incidence_reply', $notification_data);
    }

    public function getReply($id)
    {
        $incidence = Incidence::find($id);

        if (! $incidence) {
            return response()->json(['reply' => null]);
        }

        return response()->json([
            'reply' => $incidence->reply,
        ]);
    }
}
