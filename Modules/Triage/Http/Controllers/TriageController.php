<?php

namespace Modules\Triage\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Appointment\Models\Appointment;
use Modules\Triage\Models\PatientTriage;
use Modules\Triage\Models\TriageCategory;
use Modules\Triage\Models\TriageItem;
use Modules\Triage\Models\TriagePreCheck;
use Yajra\DataTables\DataTables;

class TriageController extends Controller
{
    public function __construct()
    {
        $this->module_title = 'triage.menu_title';
        $this->module_name  = 'triage';

        view()->share([
            'module_title' => $this->module_title,
            'module_name'  => $this->module_name,
            'module_icon'  => 'ph ph-clipboard-text',
        ]);

        $this->middleware(['permission:view_triage_queue'])->only('index', 'index_data');
        $this->middleware(['permission:add_triage'])->only('store');
        $this->middleware(['permission:edit_triage'])->only('update', 'show');
        $this->middleware(['permission:escalate_triage'])->only('escalate');
    }

    public function index()
    {
        $categories = TriageCategory::active()->get();
        return view('triage::backend.triage.index', compact('categories'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $user  = auth()->user();
        $query = PatientTriage::with(['patient', 'nurse', 'category', 'item', 'appointment'])
            ->forNurse($user);

        // Status tab filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return $datatable->eloquent($query)
            ->addColumn('check', fn($row) =>
                '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">'
            )
            ->addColumn('action', fn($row) =>
                view('triage::backend.triage.action_column', ['data' => $row, 'module_name' => $this->module_name])
            )
            ->editColumn('patient_id', fn($row) =>
                optional($row->patient)->full_name ?? '—'
            )
            ->addColumn('patient_dob', fn($row) =>
                optional($row->patient)->date_of_birth
                    ? \Carbon\Carbon::parse($row->patient->date_of_birth)->format('d M Y') . ' (' . \Carbon\Carbon::parse($row->patient->date_of_birth)->age . 'y)'
                    : '—'
            )
            ->addColumn('appointment_type', fn($row) =>
                optional(optional($row->appointment)->clinicservice)->name ?? '—'
            )
            ->editColumn('urgency_level', fn($row) =>
                $this->urgencyBadge($row->urgency_level)
            )
            ->editColumn('status', fn($row) =>
                $this->statusBadge($row->status)
            )
            ->editColumn('nurse_id', fn($row) =>
                optional($row->nurse)->full_name ?? '—'
            )
            ->editColumn('created_at', fn($row) =>
                Carbon::now()->diffInHours($row->created_at) < 25
                    ? $row->created_at->diffForHumans()
                    : $row->created_at->isoFormat('llll')
            )
            ->rawColumns(['check', 'action', 'urgency_level', 'status'])
            ->toJson();
    }

    public function show($id)
    {
        $triage     = PatientTriage::with(['patient', 'nurse', 'appointment.clinicservice', 'category', 'item', 'clinicianEscalatedTo'])->findOrFail($id);
        $categories = TriageCategory::active()->get();
        $items      = $triage->category_id
            ? TriageItem::where('category_id', $triage->category_id)->active()->get()
            : collect();
        $doctors    = User::role('doctor')->where('status', 1)->get(['id', 'first_name', 'last_name']);

        // Update status to in_progress if still new
        if ($triage->status === 'new') {
            $triage->update(['status' => 'in_progress']);
        }

        return view('triage::backend.triage.detail', compact('triage', 'categories', 'items', 'doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id'     => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
        ]);

        $triage = PatientTriage::create([
            'patient_id'     => $request->patient_id,
            'appointment_id' => $request->appointment_id,
            'nurse_id'       => auth()->id(),
            'status'         => 'new',
        ]);

        // Link triage to appointment
        if ($request->appointment_id) {
            Appointment::where('id', $request->appointment_id)->update(['triage_id' => $triage->id]);
        }

        return response()->json([
            'status'  => true,
            'message' => __('messages.create_form', ['form' => __('triage.singular_title')]),
            'data'    => $triage,
            'redirect' => route('backend.triage.show', $triage->id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $triage = PatientTriage::findOrFail($id);

        $data = $request->only([
            'category_id', 'item_id', 'urgency_level', 'outcome',
            'nurse_notes', 'onset_bucket', 'trend', 'fever_flag',
            'severity_score', 'function_impacted', 'hydration_concern',
            'risk_flags', 'meds_text', 'allergy_text', 'recent_antibiotics',
            'identity_confirmed', 'red_flag_triggered', 'red_flag_action_taken',
            'redirect_service',
        ]);

        // Cast booleans from form
        foreach (['fever_flag', 'function_impacted', 'hydration_concern', 'recent_antibiotics', 'identity_confirmed', 'red_flag_triggered'] as $bool) {
            if (isset($data[$bool])) {
                $data[$bool] = filter_var($data[$bool], FILTER_VALIDATE_BOOLEAN);
            }
        }

        // risk_flags comes as array from checkboxes
        if ($request->has('risk_flags') && is_array($request->risk_flags)) {
            $data['risk_flags'] = $request->risk_flags;
        }

        // If red flag triggered, force E1 + emergency outcome
        if (!empty($data['red_flag_triggered'])) {
            $data['urgency_level'] = 'E1';
            $data['outcome']       = 'emergency';
        }

        // Auto-generate handover if escalating
        if ($request->filled('clinician_escalated_to')) {
            $data['clinician_escalated_to'] = $request->clinician_escalated_to;
            $data['status']                 = 'escalated';
            $data['handover_summary']       = $request->handover_summary ?? $this->buildHandoverSummary($triage, $data);
        } else {
            $data['status'] = 'in_progress';
        }

        $triage->update($data);

        return response()->json([
            'status'  => true,
            'message' => __('messages.update_form', ['form' => __('triage.singular_title')]),
        ]);
    }

    public function escalate(Request $request, $id)
    {
        $request->validate(['clinician_escalated_to' => 'required|exists:users,id']);

        $triage = PatientTriage::findOrFail($id);

        $summary = $request->handover_summary ?? $this->buildHandoverSummary($triage, []);

        $triage->update([
            'clinician_escalated_to' => $request->clinician_escalated_to,
            'handover_summary'       => $summary,
            'status'                 => 'escalated',
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('triage.escalated_success'),
        ]);
    }

    public function close($id)
    {
        PatientTriage::findOrFail($id)->update(['status' => 'closed']);
        return response()->json(['status' => true, 'message' => __('triage.closed_success')]);
    }

    public function getItems(Request $request)
    {
        $items = TriageItem::where('category_id', $request->category_id)
            ->active()
            ->get(['id', 'label', 'is_red_flag']);

        return response()->json($items);
    }

    public function appointmentSearch(Request $request)
    {
        $term = trim($request->q);
        $user = auth()->user();

        $query = Appointment::with(['user', 'clinicservice'])
            ->where(function ($q) use ($term) {
                if ($term) {
                    $q->where('id', 'like', "%{$term}%")
                      ->orWhereHas('user', function ($uq) use ($term) {
                          $uq->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]);
                      })
                      ->orWhereHas('clinicservice', function ($sq) use ($term) {
                          $sq->where('name', 'like', "%{$term}%");
                      });
                }
            });

        // Scope to nurse's clinic
        if ($user->hasRole('nurse')) {
            $nurse = \App\Models\Nurse::where('nurse_id', $user->id)->first();
            if ($nurse) {
                $query->where('clinic_id', $nurse->clinic_id);
            }
        } elseif ($user->hasRole('vendor')) {
            $query->whereHas('cliniccenter', fn($q) => $q->where('vendor_id', $user->id));
        }

        $results = $query->latest()->limit(30)->get();

        // return response()->json($results->map(fn($a) => [
        //     'id'   => $a->id,
        //     'text' => '#' . $a->id
        //         . ' — ' . (optional($a->user)->full_name ?? '—')
        //         . ' (' . (optional($a->clinicservice)->name ?? 'No service') . ')'
        //        . ($a->appointment_date
        //         ? ' · ' . \Carbon\Carbon::parse($a->appointment_date)->format(setting('date_formate') ?? 'd/m/Y')
        //         : ''),
        //                     // . ($a->appointment_date ? ' · ' . $a->appointment_date : ''),
        
        //     ]));

        return response()->json($results->map(fn($a) => [
            'id' => $a->id,
            'text' => '#' . $a->id
                . ' — ' . (optional($a->user)->full_name ?? '—')
                . ' (' . (optional($a->clinicservice)->name ?? 'No service') . ')'
                . ($a->appointment_date
                    ? ' · ' . \Carbon\Carbon::parse($a->appointment_date)->format(setting('date_formate') ?? 'd/m/Y')
                    : '')
                . ($a->appointment_time
                    ? ' at ' . \Carbon\Carbon::parse($a->appointment_time)->format(setting('time_formate') ?? 'h:i A')
                    : ''),
        ]));
    }

    public function preCheckStore(Request $request)
    {
        $answers = $request->answers ?? [];

        // Blocker questions: Q1–Q6
        $blockerMap = [
            'q1' => 'Severe chest pain / pressure',
            'q2' => 'Severe difficulty breathing',
            'q3' => 'Stroke symptoms',
            'q4' => 'Severe allergic reaction',
            'q5' => 'Collapse / seizure / severe bleeding',
            'q6' => 'Immediate self-harm risk',
        ];

        $blockerTriggered = false;
        $blockerQuestion  = null;

        foreach ($blockerMap as $key => $label) {
            if (!empty($answers[$key]) && $answers[$key] === 'yes') {
                $blockerTriggered = true;
                $blockerQuestion  = $label;
                break;
            }
        }

        // Routing logic for non-blockers
        $recommendedUrgency = 'routine';
        $recommendedPath    = 'acute_same_day';

        if (!$blockerTriggered) {
            $urgentKeys = ['q7', 'q8', 'q9', 'q10', 'q11', 'q12'];
            foreach ($urgentKeys as $key) {
                if (!empty($answers[$key]) && $answers[$key] === 'yes') {
                    $recommendedUrgency = 'urgent';
                    break;
                }
            }
            if (!empty($answers['q13']) && $answers['q13'] === 'yes') {
                $recommendedPath = 'home_visit_request';
            }
            if (!empty($answers['q14']) && $answers['q14'] === 'yes') {
                $recommendedPath = 'medical_reports';
            }
        }

        $preCheck = TriagePreCheck::create([
            'appointment_id'     => $request->appointment_id,
            'user_id'            => auth()->id() ?? $request->user_id,
            'answers'            => $answers,
            'blocker_triggered'  => $blockerTriggered,
            'blocker_question'   => $blockerQuestion,
            'recommended_urgency' => $recommendedUrgency,
            'recommended_path'   => $recommendedPath,
        ]);

        return response()->json([
            'status'              => true,
            'block'               => $blockerTriggered,
            'blocker_question'    => $blockerQuestion,
            'recommended_urgency' => $recommendedUrgency,
            'recommended_path'    => $recommendedPath,
            'pre_check_id'        => $preCheck->id,
        ]);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    private function buildHandoverSummary(PatientTriage $triage, array $newData): string
    {
        $category   = optional($triage->category)->name ?? ($newData['category_id'] ? TriageCategory::find($newData['category_id'])?->name : '—');
        $item       = optional($triage->item)->label ?? ($newData['item_id'] ? TriageItem::find($newData['item_id'])?->label : '—');
        $onset      = $newData['onset_bucket'] ?? $triage->onset_bucket ?? '—';
        $trend      = $newData['trend'] ?? $triage->trend ?? '—';
        $severity   = $newData['severity_score'] ?? $triage->severity_score ?? '—';
        $function   = isset($newData['function_impacted']) ? ($newData['function_impacted'] ? 'Yes' : 'No') : ($triage->function_impacted ? 'Yes' : 'No');
        $risks      = $newData['risk_flags'] ?? $triage->risk_flags ?? [];
        $meds       = $newData['meds_text'] ?? $triage->meds_text ?? '—';
        $allergies  = $newData['allergy_text'] ?? $triage->allergy_text ?? '—';
        $urgency    = $newData['urgency_level'] ?? $triage->urgency_level ?? '—';
        $outcome    = $newData['outcome'] ?? $triage->outcome ?? '—';
        $notes      = $newData['nurse_notes'] ?? $triage->nurse_notes ?? '—';

        return "Main issue: {$category} → {$item}\n"
            . "Onset/trend: {$onset}, {$trend}\n"
            . "Severity: {$severity}/10, Function impacted: {$function}\n"
            . "Risk factors: " . (is_array($risks) ? implode(', ', $risks) : $risks) . "\n"
            . "Meds: {$meds} | Allergies: {$allergies}\n"
            . "Nurse recommendation: {$urgency} — {$outcome}\n"
            . "Notes: {$notes}";
    }

    private function urgencyBadge(?string $level): string
    {
        $map = [
            'E1' => ['danger',  'E1 Emergency'],
            'U2' => ['warning', 'U2 Urgent'],
            'S3' => ['info',    'S3 Soon'],
            'R4' => ['success', 'R4 Routine'],
        ];
        if (!$level || !isset($map[$level])) {
            return '<span class="badge bg-secondary">—</span>';
        }
        [$colour, $label] = $map[$level];
        return "<span class=\"badge bg-{$colour}\">{$label}</span>";
    }

    private function statusBadge(?string $status): string
    {
        $map = [
            'new'         => ['primary',   'New'],
            'in_progress' => ['warning',   'In Progress'],
            'escalated'   => ['danger',    'Escalated'],
            'closed'      => ['secondary', 'Closed'],
        ];
        if (!$status || !isset($map[$status])) {
            return '<span class="badge bg-secondary">—</span>';
        }
        [$colour, $label] = $map[$status];
        return "<span class=\"badge bg-{$colour}\">{$label}</span>";
    }
}
