@extends('backend.layouts.app')

@section('title')
    {{ __('triage.singular_title') }} #{{ $triage->id }}
@endsection

@section('content')
<div class="container-fluid">

    {{-- Safety Banner --}}
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
        <i class="ph ph-warning-circle fs-5 flex-shrink-0"></i>
        <span>{{ __('triage.safety_banner') }}</span>
    </div>

    {{-- Red Flag Alert (shown if triggered) --}}
    @if($triage->red_flag_triggered)
    <div class="alert alert-danger border-danger d-flex align-items-center gap-2 mb-3">
        <i class="ph ph-siren fs-4 flex-shrink-0"></i>
        <strong>{{ __('triage.red_flag_warning') }}</strong>
    </div>
    @endif

    <form id="triageForm">
        @csrf
        @method('PUT')
        <div class="row g-4">

            {{-- ── LEFT PANEL: Patient Summary ─────────────────────────────── --}}
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="patient-card-header d-flex align-items-center gap-2">
                        <i class="ph ph-user-circle fs-4 text-primary"></i>
                        <span class="fw-bold fs-6">{{ __('triage.lbl_patient') }}</span>
                        <span class="badge bg-{{ $triage->status === 'escalated' ? 'danger' : ($triage->status === 'closed' ? 'secondary' : ($triage->status === 'in_progress' ? 'warning' : 'primary')) }} ms-auto px-3 py-2">
                            {{ __('triage.status_' . $triage->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        @php $patient = $triage->patient; @endphp
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ $patient?->profile_image ?? asset('images/default-avatar.png') }}"
                                 class="avatar avatar-60 rounded-pill" alt="">
                            <div>
                                <div class="fw-semibold fs-6">{{ $patient?->full_name ?? '—' }}</div>
                                <div class="text-muted small">{{ $patient?->email ?? '' }}</div>
                            </div>
                        </div>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted small">{{ __('triage.lbl_dob') }}</td>
                                <td class="small fw-medium">
                                    {{ $patient?->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('d M Y') . ' (' . \Carbon\Carbon::parse($patient->date_of_birth)->age . 'y)' : '—' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">{{ __('customer.lbl_phone_number') }}</td>
                                <td class="small fw-medium">{{ $patient?->mobile ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">{{ __('messages.address') }}</td>
                                <td class="small fw-medium">{{ $patient?->address ?? '—' }}</td>
                            </tr>
                        </table>

                        @if($triage->appointment)
                        <hr>
                        <div class="small fw-semibold mb-2"><i class="ph ph-calendar-check me-1"></i>{{ __('triage.lbl_appointment') }}</div>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted small">ID</td>
                                <td class="small">#{{ $triage->appointment->id }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">{{ __('appointment.lbl_services') }}</td>
                                <td class="small">{{ optional($triage->appointment->clinicservice)->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">{{ __('appointment.lbl_date_time') }}</td>
                                <td class="small">{{ $triage->appointment->appointment_date ?? '—' }}</td>
                            </tr>
                            @if($triage->appointment->appointment_extra_info)
                            <tr>
                                <td class="text-muted small" colspan="2">
                                    <div class="fw-medium mb-1">{{ __('appointment.lbl_notes') }}</div>
                                    <div class="text-dark">{{ $triage->appointment->appointment_extra_info }}</div>
                                </td>
                            </tr>
                            @endif
                        </table>
                        @endif

                        @if($triage->urgency_level || $triage->outcome)
                        <hr>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($triage->urgency_level)
                            <span class="badge bg-{{ $triage->urgency_level === 'E1' ? 'danger' : ($triage->urgency_level === 'U2' ? 'warning' : ($triage->urgency_level === 'S3' ? 'info' : 'success')) }} fs-6 px-3 py-2">
                                {{ __('triage.urgency_' . strtolower($triage->urgency_level)) }}
                            </span>
                            @endif
                        </div>
                        @endif

                        <div class="mt-3">
                            <a href="{{ route('backend.customers.patient_detail', $triage->patient_id) }}"
                               class="btn btn-sm btn-outline-primary w-100" target="_blank">
                                <i class="ph ph-arrow-square-out me-1"></i>{{ __('messages.view') }} {{ __('triage.lbl_patient') }} Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── RIGHT PANEL: Intake Form ─────────────────────────────────── --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="ph ph-clipboard-text fs-5"></i>
                        <span class="fw-semibold">Nurse Triage Intake</span>
                        <span class="ms-auto text-muted small">Triage #{{ $triage->id }}</span>
                    </div>
                    <div class="card-body">

                        {{-- Progress Steps --}}
                        <div class="triage-steps" id="triage-steps">
                            @foreach(['Q1 Identity','Q2 Age','Q3 Red Flags','Q4–5 Category','Q6 Onset','Q7 Severity','Q8 Risk','Q9 Meds','Q10 Decision'] as $i => $step)
                            <div class="triage-step {{ $i === 0 ? 'active' : '' }}" data-step="{{ $i }}">{{ $step }}</div>
                            @endforeach
                        </div>

                        {{-- Q1: Identity Confirmation --}}
                        <div class="triage-band triage-section" data-section="0">
                            <div class="section-label"><span class="badge bg-dark me-2">Q1</span>Identity Confirmation</div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label small">{{ __('customer.lbl_name') }}</label>
                                    <input type="text" class="form-control form-control-sm"
                                           value="{{ $triage->patient?->full_name }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">{{ __('triage.lbl_dob') }}</label>
                                    <input type="text" class="form-control form-control-sm"
                                           value="{{ $triage->patient?->date_of_birth ? \Carbon\Carbon::parse($triage->patient->date_of_birth)->format('d M Y') : '—' }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">{{ __('customer.lbl_phone_number') }}</label>
                                    <input type="text" class="form-control form-control-sm"
                                           value="{{ $triage->patient?->mobile }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">{{ __('messages.address') }} / Postcode</label>
                                    <input type="text" class="form-control form-control-sm"
                                           value="{{ $triage->patient?->address }}" readonly>
                                </div>
                                <div class="col-12">
                                    <div class="triage-check-card">
                                        <input type="checkbox" name="identity_confirmed"
                                               id="identity_confirmed" value="1"
                                               {{ $triage->identity_confirmed ? 'checked' : '' }}>
                                        <label for="identity_confirmed">
                                            <i class="ph ph-check-circle"></i>
                                            {{ __('triage.lbl_identity') }} — I confirm the patient's identity has been verified
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Q2: Age / Safeguarding --}}
                        @php
                            $age = $triage->patient?->date_of_birth ? \Carbon\Carbon::parse($triage->patient->date_of_birth)->age : null;
                            $minorLimit = config('triage.minor_age_limit', 16);
                            $allowMinors = config('triage.allow_minors', false);
                        @endphp
                        <div class="triage-band triage-section" data-section="1">
                        <div class="section-label"><span class="badge bg-dark me-2">Q2</span>Age / Safeguarding</div>
                            @if($age !== null && $age < $minorLimit && !$allowMinors)
                                <div class="alert alert-warning mt-2">
                                    <i class="ph ph-warning me-2"></i>
                                    <strong>Minor Policy:</strong> {{ __('triage.minor_policy_block') }}
                                </div>
                            @elseif($age !== null && $age < $minorLimit && $allowMinors)
                                <div class="alert alert-info mt-2">
                                    <i class="ph ph-info me-2"></i>Patient is under {{ $minorLimit }}. Guardian details required.
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-md-4">
                                        <label class="form-label small">Guardian Name</label>
                                        <input type="text" name="guardian_name" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Relationship</label>
                                        <input type="text" name="guardian_relationship" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Guardian Phone</label>
                                        <input type="text" name="guardian_phone" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12">
                                        <div class="triage-check-card">
                                            <input type="checkbox" name="guardian_consent" id="guardian_consent">
                                            <label for="guardian_consent">
                                                <i class="ph ph-check-circle"></i>
                                                I confirm I am the parent/guardian and consent to care
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mt-2 text-muted small">
                                    Patient age: <strong>{{ $age !== null ? $age . ' years' : 'Unknown' }}</strong>
                                    @if($age !== null && $age >= $minorLimit)
                                        <span class="badge bg-success ms-2">Adult</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <hr>

                        {{-- Q3: Red Flag Safety Screen --}}
                        <div class="triage-band triage-section section-danger" id="red-flag-section" data-section="2">
                            <div class="section-label text-danger">
                                <span class="badge bg-danger me-2">Q3</span>Red Flag Safety Screen
                                <small class="text-muted fw-normal ms-2">— select all that apply</small>
                            </div>
                            <div id="red-flag-alert" class="alert alert-danger mt-2 d-none">
                                <i class="ph ph-siren me-2"></i><strong>{{ __('triage.red_flag_warning') }}</strong>
                            </div>
                            <div class="row g-2 mt-1">
                                @php
                                $redFlags = [
                                    'rf_chest'    => 'Severe chest pain/pressure',
                                    'rf_breath'   => 'Severe difficulty breathing / blue lips / cannot speak',
                                    'rf_stroke'   => 'Stroke symptoms concern (face/arm/speech)',
                                    'rf_collapse' => 'Collapse / loss of consciousness / seizure not stopping',
                                    'rf_allergy'  => 'Severe allergic reaction (face/tongue swelling, breathing difficulty, fainting)',
                                    'rf_bleeding' => 'Heavy uncontrolled bleeding',
                                    'rf_selfharm' => 'Immediate self-harm risk / not safe right now',
                                ];
                                @endphp
                                @foreach($redFlags as $key => $label)
                                <div class="col-md-6">
                                    <div class="triage-check-card is-red-flag">
                                        <input class="red-flag-check" type="checkbox"
                                               name="red_flags[]" id="{{ $key }}" value="{{ $key }}">
                                        <label for="{{ $key }}">
                                            <i class="ph ph-warning text-danger"></i>{{ $label }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div id="red-flag-action-wrap" class="{{ $triage->red_flag_triggered ? '' : 'd-none' }} mt-3">
                                <label class="form-label fw-semibold text-danger">{{ __('triage.lbl_action_taken') }}</label>
                                <textarea name="red_flag_action_taken" class="form-control" rows="2"
                                          placeholder="e.g. Advised patient to call 999 immediately. Confirmed they have someone with them.">{{ $triage->red_flag_action_taken }}</textarea>
                                <input type="hidden" name="red_flag_triggered" id="red_flag_triggered" value="{{ $triage->red_flag_triggered ? '1' : '0' }}">
                            </div>
                        </div>

                        <hr>

                        {{-- Q4 + Q5: Category & Reason --}}
                        <div class="triage-band triage-section" data-section="3">
                        <div class="section-label"><span class="badge bg-dark me-2">Q4–Q5</span>{{ __('triage.lbl_category') }} & {{ __('triage.lbl_item') }}</div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label small">{{ __('triage.lbl_category') }}</label>
                                    <select name="category_id" id="category_select" class="select2 form-select form-select-sm" style="width:100%">
                                        <option value="">{{ 'Select' }}...</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $triage->category_id == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">{{ __('triage.lbl_item') }}</label>
                                    <select name="item_id" id="item_select" class="select2 form-select form-select-sm" style="width:100%">
                                        <option value="">{{ 'Select' }}...</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}"
                                                    data-red-flag="{{ $item->is_red_flag ? '1' : '0' }}"
                                                    {{ $triage->item_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->is_red_flag ? '⚠️ ' : '' }}{{ $item->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Q6: Onset & Pattern --}}
                        <div class="triage-band triage-section" data-section="5">
                        <div class="section-label"><span class="badge bg-dark me-2">Q6</span>Onset & Pattern</div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-4">
                                    <label class="form-label small">{{ __('triage.lbl_onset') }}</label>
                                    <select name="onset_bucket" class="form-select form-select-sm">
                                        <option value="">{{ 'Select' }}...</option>
                                        @foreach(['Today','1-2 days','3-7 days','1-4 weeks','1-3 months','>3 months'] as $onset)
                                            <option value="{{ $onset }}" {{ $triage->onset_bucket === $onset ? 'selected' : '' }}>{{ $onset }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">{{ __('triage.lbl_trend') }}</label>
                                    <div class="btn-group w-100" role="group">
                                        @foreach(['worse' => 'Getting worse', 'same' => 'Same', 'improving' => 'Improving'] as $val => $label)
                                        <input type="radio" class="btn-check" name="trend" id="trend_{{ $val }}"
                                               value="{{ $val }}" {{ $triage->trend === $val ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary btn-sm" for="trend_{{ $val }}">{{ $label }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">{{ __('triage.lbl_fever') }}</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="fever_flag" id="fever_yes" value="1" {{ $triage->fever_flag ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary btn-sm" for="fever_yes">Yes</label>
                                        <input type="radio" class="btn-check" name="fever_flag" id="fever_no" value="0" {{ $triage->fever_flag === false && $triage->fever_flag !== null ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary btn-sm" for="fever_no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Q7: Severity & Function --}}
                        <div class="triage-band triage-section" data-section="6">
                        <div class="section-label"><span class="badge bg-dark me-2">Q7</span>Severity & Function</div>
                            <div class="row g-3 mt-1">
                                <div class="col-12">
                                    <label class="form-label small">{{ __('triage.lbl_severity') }}: <strong id="severity-label">{{ $triage->severity_score ?? 5 }}</strong>/10</label>
                                    <input type="range" class="form-range" name="severity_score" id="severity_range"
                                           min="0" max="10" step="1" value="{{ $triage->severity_score ?? 5 }}"
                                           oninput="document.getElementById('severity-label').textContent = this.value">
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span>0 — None</span><span>5 — Moderate</span><span>10 — Severe</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">{{ __('triage.lbl_function') }}</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="function_impacted" id="func_yes" value="1" {{ $triage->function_impacted ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary btn-sm" for="func_yes">Yes</label>
                                        <input type="radio" class="btn-check" name="function_impacted" id="func_no" value="0" {{ $triage->function_impacted === false && $triage->function_impacted !== null ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary btn-sm" for="func_no">No</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">{{ __('triage.lbl_hydration') }}</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="hydration_concern" id="hydration_yes" value="1" {{ $triage->hydration_concern ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary btn-sm" for="hydration_yes">Yes</label>
                                        <input type="radio" class="btn-check" name="hydration_concern" id="hydration_no" value="0" {{ $triage->hydration_concern === false && $triage->hydration_concern !== null ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary btn-sm" for="hydration_no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Q8: High-Risk Factors --}}
                        <div class="triage-band triage-section" data-section="7">
                        <div class="section-label"><span class="badge bg-dark me-2">Q8</span>{{ __('triage.lbl_risk_flags') }}</div>
                            @php
                            $riskOptions = [
                                'age65'         => __('triage.risk_age65'),
                                'pregnancy'     => __('triage.risk_pregnancy'),
                                'diabetes'      => __('triage.risk_diabetes'),
                                'heart'         => __('triage.risk_heart'),
                                'asthma'        => __('triage.risk_asthma'),
                                'immuno'        => __('triage.risk_immuno'),
                                'kidney'        => __('triage.risk_kidney'),
                                'blood_thinners'=> __('triage.risk_blood_thinners'),
                                'surgery'       => __('triage.risk_surgery'),
                                'none'          => __('triage.risk_none'),
                            ];
                            $savedRisks = $triage->risk_flags ?? [];
                            @endphp
                            <div class="row g-2 mt-1">
                                @foreach($riskOptions as $val => $label)
                                <div class="col-md-6">
                                    <div class="triage-check-card">
                                        <input class="risk-flag-check" type="checkbox"
                                               name="risk_flags[]" id="risk_{{ $val }}" value="{{ $val }}"
                                               {{ in_array($val, $savedRisks) ? 'checked' : '' }}
                                               {{ $val === 'none' ? 'data-exclusive="1"' : '' }}>
                                        <label for="risk_{{ $val }}">{{ $label }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        {{-- Q9: Medications & Allergies --}}
                        <div class="triage-band triage-section" data-section="8">
                        <div class="section-label"><span class="badge bg-dark me-2">Q9</span>Medications & Allergies</div>
                            <div class="row g-3 mt-1">
                                <div class="col-12">
                                    <label class="form-label small">{{ __('triage.lbl_meds') }}</label>
                                    <textarea name="meds_text" class="form-control form-control-sm" rows="2"
                                              placeholder="List current medications...">{{ $triage->meds_text }}</textarea>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small">{{ __('triage.lbl_allergies') }}</label>
                                    <textarea name="allergy_text" class="form-control form-control-sm" rows="2"
                                              placeholder="None / list allergies...">{{ $triage->allergy_text }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">{{ __('triage.lbl_antibiotics') }}</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="recent_antibiotics" id="ab_yes" value="1" {{ $triage->recent_antibiotics ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary btn-sm" for="ab_yes">Yes</label>
                                        <input type="radio" class="btn-check" name="recent_antibiotics" id="ab_no" value="0" {{ $triage->recent_antibiotics === false && $triage->recent_antibiotics !== null ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary btn-sm" for="ab_no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Q10: Nurse Decision --}}
                        <div class="triage-band triage-section" data-section="9">
                        <div class="section-label"><span class="badge bg-dark me-2">Q10</span>Nurse Decision</div>

                            {{-- Suggested urgency (read-only hint) --}}
                            <div class="alert alert-light border mt-2 mb-3 d-flex align-items-center gap-2">
                                <i class="ph ph-lightbulb text-warning fs-5"></i>
                                <span class="small">
                                    <strong>{{ __('triage.lbl_suggested') }}:</strong>
                                    @php
                                        $suggested = $triage->getSuggestedUrgency();
                                        $suggestedColour = ['E1'=>'danger','U2'=>'warning','S3'=>'info','R4'=>'success'][$suggested] ?? 'secondary';
                                        $suggestedLabels = ['E1'=>'🚨 E1 Emergency','U2'=>'⚡ U2 Urgent','S3'=>'🕐 S3 Soon','R4'=>'✅ R4 Routine'];
                                    @endphp
                                    <span id="suggested-urgency-badge" class="badge bg-{{ $suggestedColour }} ms-1">
                                        {{ $suggestedLabels[$suggested] ?? '—' }}
                                    </span>
                                </span>
                            </div>

                            {{-- Urgency Buttons --}}
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">{{ __('triage.lbl_urgency') }}</label>
                                <div class="btn-group w-100 urgency-btn-group" role="group" id="urgency-group">
                                    @foreach(['E1'=>['danger','🚨','E1 Emergency'],'U2'=>['warning','⚡','U2 Urgent'],'S3'=>['info','🕐','S3 Soon'],'R4'=>['success','✅','R4 Routine']] as $val=>[$colour,$icon,$label])
                                    <input type="radio" class="btn-check" name="urgency_level" id="urgency_{{ $val }}"
                                           value="{{ $val }}" {{ $triage->urgency_level === $val ? 'checked' : '' }}
                                           {{ $triage->red_flag_triggered ? 'disabled' : '' }}>
                                    <label class="btn btn-outline-{{ $colour }}" for="urgency_{{ $val }}">{{ $icon }} {{ $label }}</label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Outcome Buttons --}}
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">{{ __('triage.lbl_outcome') }}</label>
                                <div class="row g-2 outcome-btn-group" id="outcome-group">
                                    @foreach([
                                        'emergency'  => ['danger',    '🚨', __('triage.outcome_emergency')],
                                        'urgent'     => ['warning',   '⚡', __('triage.outcome_urgent')],
                                        'soon'       => ['info',      '🕐', __('triage.outcome_soon')],
                                        'routine'    => ['success',   '✅', __('triage.outcome_routine')],
                                        'redirect'   => ['secondary', '↪️', __('triage.outcome_redirect')],
                                        'home_visit' => ['primary',   '🏠', __('triage.outcome_home_visit')],
                                    ] as $val => [$colour, $icon, $label])
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="outcome" id="outcome_{{ $val }}"
                                               value="{{ $val }}" {{ $triage->outcome === $val ? 'checked' : '' }}
                                               {{ $triage->red_flag_triggered && $val !== 'emergency' ? 'disabled' : '' }}>
                                        <label class="btn btn-outline-{{ $colour }} w-100 text-start" for="outcome_{{ $val }}">
                                            {{ $icon }} {{ $label }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Redirect service (shown when redirect selected) --}}
                            <div id="redirect-service-wrap" class="{{ $triage->outcome === 'redirect' ? '' : 'd-none' }} mb-3">
                                <label class="form-label small">Redirect to Service</label>
                                <input type="text" name="redirect_service" class="form-control form-control-sm"
                                       placeholder="e.g. Women's Health, Travel Clinic..."
                                       value="{{ $triage->redirect_service }}">
                            </div>

                            {{-- Home visit note --}}
                            <div id="home-visit-note" class="{{ $triage->outcome === 'home_visit' ? '' : 'd-none' }} alert alert-info mb-3">
                                <i class="ph ph-house me-2"></i>Home visit request will be sent for clinician approval.
                            </div>

                            {{-- Nurse Notes --}}
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">{{ __('triage.lbl_nurse_notes') }}</label>
                                <textarea name="nurse_notes" class="form-control" rows="3"
                                          placeholder="Free text notes...">{{ $triage->nurse_notes }}</textarea>
                            </div>

                            {{-- Escalate to Clinician --}}
                            <div class="mb-3">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="escalate_toggle"
                                           {{ $triage->clinician_escalated_to ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="escalate_toggle">
                                        {{ __('triage.lbl_escalate') }}
                                    </label>
                                </div>
                                <div id="escalate-wrap" class="{{ $triage->clinician_escalated_to ? '' : 'd-none' }}">
                                    <div class="mb-2">
                                        <label class="form-label small">Select Clinician</label>
                                        <select name="clinician_escalated_to" id="clinician_select" class="select2 form-select form-select-sm" style="width:100%">
                                            <option value="">{{ 'Select' }}...</option>
                                            @foreach($doctors as $doc)
                                                <option value="{{ $doc->id }}" {{ $triage->clinician_escalated_to == $doc->id ? 'selected' : '' }}>
                                                    {{ $doc->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label small">{{ __('triage.lbl_handover') }}</label>
                                        <textarea name="handover_summary" id="handover_summary" class="form-control form-control-sm" rows="6">{{ $triage->handover_summary }}</textarea>
                                        <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="generateHandover()">
                                            <i class="ph ph-magic-wand me-1"></i>Auto-generate
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="d-flex gap-2 justify-content-end pt-2 border-top">
                            <a href="{{ route('backend.triage.index') }}" class="btn btn-secondary">
                                <i class="ph ph-arrow-left me-1"></i>{{ __('messages.back') }}
                            </a>
                            @if($triage->status !== 'closed')
                            <button type="button" class="btn btn-outline-secondary" onclick="closeTriage({{ $triage->id }})">
                                <i class="ph ph-check-circle me-1"></i>{{ __('triage.status_closed') }}
                            </button>
                            <button type="submit" class="btn btn-primary" id="save-btn">
                                <i class="ph ph-floppy-disk me-1"></i>{{ __('messages.save') }} Triage
                            </button>
                            @endif
                        </div>

                    </div>{{-- /card-body --}}
                </div>{{-- /card --}}
            </div>{{-- /col --}}
        </div>{{-- /row --}}
    </form>
</div>
@endsection

@push('after-styles')
<style>
/* ── Section labels ─────────────────────────────────────────── */
.triage-section .section-label {
    font-weight: 700;
    font-size: 1rem;
    color: #1a1a2e;
    margin-bottom: .5rem;
    letter-spacing: .01em;
}

/* ── All form labels larger + darker ───────────────────────── */
.triage-section .form-label {
    font-size: .95rem;
    font-weight: 600;
    color: #1a1a2e;
    margin-bottom: .35rem;
}

/* ── Inputs / selects / textareas ───────────────────────────── */
.triage-section .form-control,
.triage-section .form-select {
    font-size: .95rem;
    color: #1a1a2e;
    min-height: 2.6rem;
}

/* ── Pill-card checkboxes (Q3 red flags + Q8 risk factors) ─── */
.triage-check-card {
    position: relative;
}
.triage-check-card input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
.triage-check-card label {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .65rem 1rem;
    border: 2px solid #dee2e6;
    border-radius: .5rem;
    cursor: pointer;
    font-size: .95rem;
    font-weight: 500;
    color: #1a1a2e;
    background: #fff;
    transition: border-color .15s, background .15s, color .15s;
    user-select: none;
    min-height: 3rem;
    line-height: 1.4;
}
.triage-check-card label::before {
    content: '';
    flex-shrink: 0;
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid #adb5bd;
    border-radius: .25rem;
    background: #fff;
    transition: background .15s, border-color .15s;
}
.triage-check-card input:checked + label {
    border-color: #0d6efd;
    background: #e8f0fe;
    color: #0a3880;
    font-weight: 600;
}
.triage-check-card input:checked + label::before {
    background: #0d6efd;
    border-color: #0d6efd;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='white' d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
    background-size: .85rem;
}
/* Red flag variant — checked = red */
.triage-check-card.is-red-flag input:checked + label {
    border-color: #dc3545;
    background: #fde8ea;
    color: #7b0d14;
}
.triage-check-card.is-red-flag input:checked + label::before {
    background: #dc3545;
    border-color: #dc3545;
}
.triage-check-card label:hover {
    border-color: #6ea8fe;
    background: #f0f5ff;
}

/* ── Btn-group buttons larger ───────────────────────────────── */
.triage-section .btn-group .btn {
    font-size: .9rem;
    padding: .5rem .9rem;
    font-weight: 500;
}

/* ── Range slider label ─────────────────────────────────────── */
#severity-label {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a1a2e;
}

/* ── Severity slider coloured track ────────────────────────── */
#severity_range { height: .5rem; border-radius: .5rem; }
#severity_range.severity-low  { accent-color: #198754; }
#severity_range.severity-mid  { accent-color: #fd7e14; }
#severity_range.severity-high { accent-color: #dc3545; }

/* ── Section left-border accent ────────────────────────────── */
.triage-section {
    padding-left: 1rem;
    border-left: 4px solid #e9ecef;
    transition: border-color .2s;
}
.triage-section:focus-within {
    border-left-color: #0d6efd;
}
.triage-section.section-danger { border-left-color: #dc3545 !important; }

/* ── Section band background ────────────────────────────────── */
.triage-band {
    background: #f8f9fa;
    border-radius: .5rem;
    padding: 1.25rem 1.25rem 1rem;
    margin-bottom: 1.25rem;
}

/* ── Progress steps ─────────────────────────────────────────── */
.triage-steps {
    display: flex;
    gap: .25rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}
.triage-step {
    flex: 1;
    min-width: 2.5rem;
    text-align: center;
    padding: .35rem .25rem;
    border-radius: .4rem;
    font-size: .75rem;
    font-weight: 600;
    background: #e9ecef;
    color: #6c757d;
    cursor: default;
    transition: background .2s, color .2s;
}
.triage-step.active {
    background: #0d6efd;
    color: #fff;
}
.triage-step.done {
    background: #d1e7dd;
    color: #0a3622;
}

/* ── Pill-card focus ring ───────────────────────────────────── */
.triage-check-card input:focus + label {
    outline: 3px solid rgba(13,110,253,.4);
    outline-offset: 2px;
}
.triage-check-card input:checked + label {
    box-shadow: 0 0 0 3px rgba(13,110,253,.18);
}
.triage-check-card.is-red-flag input:checked + label {
    box-shadow: 0 0 0 3px rgba(220,53,69,.2);
}

/* ── Urgency / outcome buttons with icons ───────────────────── */
.urgency-btn-group .btn, .outcome-btn-group .btn {
    font-size: .9rem;
    font-weight: 600;
    padding: .6rem .75rem;
    display: flex;
    align-items: center;
    gap: .4rem;
    justify-content: center;
}

/* ── Suggested urgency badge ────────────────────────────────── */
#suggested-urgency-badge {
    font-size: .9rem;
    padding: .4rem .85rem;
    border-radius: 2rem;
    font-weight: 700;
    letter-spacing: .02em;
}

/* ── Patient card gradient header ───────────────────────────── */
.patient-card-header {
    background: linear-gradient(135deg, #e8f0fe 0%, #f8f9fa 100%);
    border-bottom: 1px solid #dee2e6;
    border-radius: calc(.375rem - 1px) calc(.375rem - 1px) 0 0;
    padding: 1rem 1.25rem;
}
</style>
@endpush

@push('after-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Select2 init
    ['#category_select','#item_select','#clinician_select'].forEach(sel => {
        if ($(sel).length) $(sel).select2({ width: '100%' });
    });

    // Category → Items AJAX
    $('#category_select').on('change', function () {
        const catId = $(this).val();
        const itemSel = $('#item_select');
        itemSel.empty().append('<option value="">Loading...</option>');
        if (!catId) { itemSel.empty().append('<option value="">Select...</option>'); return; }
        fetch(`{{ route("backend.triage.get_items") }}?category_id=${catId}`)
            .then(r => r.json())
            .then(items => {
                itemSel.empty().append('<option value="">Select...</option>');
                items.forEach(item => {
                    const prefix = item.is_red_flag ? '⚠️ ' : '';
                    itemSel.append(`<option value="${item.id}" data-red-flag="${item.is_red_flag ? 1 : 0}">${prefix}${item.label}</option>`);
                });
                itemSel.trigger('change.select2');
            });
    });

    // Red flag checkboxes
    document.querySelectorAll('.red-flag-check').forEach(cb => {
        cb.addEventListener('change', updateRedFlagState);
    });

    // Risk flags — "none" is exclusive
    document.querySelectorAll('.risk-flag-check').forEach(cb => {
        cb.addEventListener('change', function () {
            if (this.dataset.exclusive === '1' && this.checked) {
                document.querySelectorAll('.risk-flag-check:not([data-exclusive])').forEach(o => o.checked = false);
            } else if (this.checked) {
                const noneBox = document.querySelector('.risk-flag-check[data-exclusive="1"]');
                if (noneBox) noneBox.checked = false;
            }
        });
    });

    // Outcome radio → show/hide redirect & home visit
    document.querySelectorAll('[name="outcome"]').forEach(r => {
        r.addEventListener('change', function () {
            document.getElementById('redirect-service-wrap').classList.toggle('d-none', this.value !== 'redirect');
            document.getElementById('home-visit-note').classList.toggle('d-none', this.value !== 'home_visit');
        });
    });

    // Escalate toggle
    document.getElementById('escalate_toggle').addEventListener('change', function () {
        document.getElementById('escalate-wrap').classList.toggle('d-none', !this.checked);
        if (!this.checked) {
            document.getElementById('clinician_select').value = '';
            $('#clinician_select').trigger('change.select2');
        }
    });

    // Form submit
    document.getElementById('triageForm').addEventListener('submit', function (e) {
        e.preventDefault();
        saveTriage();
    });

    // Severity live update + colour
    const severityRange = document.getElementById('severity_range');
    severityRange.addEventListener('input', function() {
        updateSuggestedUrgency();
        updateSeverityColour(this.value);
    });
    updateSeverityColour(severityRange.value);

    document.querySelectorAll('[name="trend"], [name="function_impacted"]').forEach(el => {
        el.addEventListener('change', updateSuggestedUrgency);
    });

    // Scroll-spy progress
    const sections = document.querySelectorAll('[data-section]');
    const steps = document.querySelectorAll('.triage-step');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const sectionIdx = parseInt(entry.target.dataset.section);
                steps.forEach((step, i) => {
                    step.classList.remove('active', 'done');
                    if (i === sectionIdx) step.classList.add('active');
                    else if (i < sectionIdx) step.classList.add('done');
                });
            }
        });
    }, { threshold: 0.5 });
    sections.forEach(s => observer.observe(s));
});

function updateSeverityColour(val) {
    const slider = document.getElementById('severity_range');
    slider.classList.remove('severity-low', 'severity-mid', 'severity-high');
    if (val <= 3) slider.classList.add('severity-low');
    else if (val <= 6) slider.classList.add('severity-mid');
    else slider.classList.add('severity-high');
}

function updateRedFlagState() {
    const anyChecked = document.querySelectorAll('.red-flag-check:checked').length > 0;
    document.getElementById('red-flag-alert').classList.toggle('d-none', !anyChecked);
    document.getElementById('red-flag-action-wrap').classList.toggle('d-none', !anyChecked);
    document.getElementById('red_flag_triggered').value = anyChecked ? '1' : '0';

    if (anyChecked) {
        // Lock urgency to E1 and outcome to emergency
        const e1 = document.getElementById('urgency_E1');
        if (e1) { e1.checked = true; }
        const em = document.getElementById('outcome_emergency');
        if (em) { em.checked = true; }
        document.querySelectorAll('[name="urgency_level"],[name="outcome"]').forEach(el => {
            el.disabled = el.value !== 'E1' && el.value !== 'emergency';
        });
    } else {
        document.querySelectorAll('[name="urgency_level"],[name="outcome"]').forEach(el => el.disabled = false);
    }
}

function updateSuggestedUrgency() {
    const severity = parseInt(document.getElementById('severity_range').value) || 0;
    const trend = document.querySelector('[name="trend"]:checked')?.value;
    const funcImpacted = document.querySelector('[name="function_impacted"]:checked')?.value === '1';
    const redFlag = document.getElementById('red_flag_triggered').value === '1';

    let key = 'R4', label = '✅ R4 Routine', colour = 'success';
    if (redFlag) {
        key = 'E1'; label = '🚨 E1 Emergency'; colour = 'danger';
    } else if (severity >= 7 || (funcImpacted && trend === 'worse')) {
        key = 'U2'; label = '⚡ U2 Urgent'; colour = 'warning';
    } else if (severity >= 4) {
        key = 'S3'; label = '🕐 S3 Soon'; colour = 'info';
    }

    const badge = document.getElementById('suggested-urgency-badge');
    if (badge) {
        badge.textContent = label;
        badge.className = `badge bg-${colour} ms-1`;
    }
}

function generateHandover() {
    const category = document.getElementById('category_select').options[document.getElementById('category_select').selectedIndex]?.text || '—';
    const item = document.getElementById('item_select').options[document.getElementById('item_select').selectedIndex]?.text || '—';
    const onset = document.querySelector('[name="onset_bucket"]')?.value || '—';
    const trend = document.querySelector('[name="trend"]:checked')?.value || '—';
    const severity = document.getElementById('severity_range').value;
    const funcImpacted = document.querySelector('[name="function_impacted"]:checked')?.value === '1' ? 'Yes' : 'No';
    const risks = [...document.querySelectorAll('[name="risk_flags[]"]:checked')].map(c => c.value).join(', ') || 'None';
    const meds = document.querySelector('[name="meds_text"]')?.value || '—';
    const allergies = document.querySelector('[name="allergy_text"]')?.value || '—';
    const urgency = document.querySelector('[name="urgency_level"]:checked')?.value || '—';
    const outcome = document.querySelector('[name="outcome"]:checked')?.value || '—';
    const notes = document.querySelector('[name="nurse_notes"]')?.value || '—';

    document.getElementById('handover_summary').value =
        `Main issue: ${category} → ${item}\n` +
        `Onset/trend: ${onset}, ${trend}\n` +
        `Severity: ${severity}/10, Function impacted: ${funcImpacted}\n` +
        `Risk factors: ${risks}\n` +
        `Meds: ${meds} | Allergies: ${allergies}\n` +
        `Nurse recommendation: ${urgency} — ${outcome}\n` +
        `Notes: ${notes}`;
}

function saveTriage() {
    const btn = document.getElementById('save-btn');
    if (btn) btn.disabled = true;

    const form = document.getElementById('triageForm');
    const data = {};
    new FormData(form).forEach((v, k) => {
        if (k.endsWith('[]')) {
            const key = k.slice(0, -2);
            if (!data[key]) data[key] = [];
            data[key].push(v);
        } else {
            data[k] = v;
        }
    });

    fetch('{{ route("backend.triage.update", $triage->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-HTTP-Method-Override': 'PUT',
        },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        if (res.status) {
            window.successSnackbar(res.message);
        } else {
            window.errorSnackbar(res.message || 'Error saving triage');
        }
    })
    .catch(() => window.errorSnackbar('Network error'))
    .finally(() => { if (btn) btn.disabled = false; });
}

function closeTriage(id) {
    if (!confirm('{{ __("messages.action_warning_message") }}')) return;
    fetch(`{{ url('app/triage') }}/${id}/close`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
    })
    .then(r => r.json())
    .then(res => {
        if (res.status) {
            window.successSnackbar(res.message);
            window.location.href = '{{ route("backend.triage.index") }}';
        }
    });
}
</script>
@endpush
