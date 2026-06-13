@if (
    $appointment->status == 'cancelled' &&
        optional($appointment->appointmenttransaction)->payment_status != 0 &&
        optional($appointment->appointmenttransaction)->transaction_type != 'cash')
    @php
        $refundAmount = $appointment->getRefundAmount();
    @endphp

    <div class="d-flex justify-content-between align-items-center px-4 py-2 rounded"
        style="background-color: {{ $refundAmount >= 0 ? '#e6f4ea' : '#fdecea' }};">

        <span class="fw-semibold {{ $refundAmount >= 0 ? 'text-success' : 'text-danger' }}">
            {{ $refundAmount >= 0 ? __('frontend.refund_completed') : __('frontend.wallet_deducted') }}
        </span>

        <span class="fw-semibold heading-color">
            {{ \Currency::format(abs($refundAmount)) }}
        </span>
    </div>
@endif

@php
    $apptDate    = \Carbon\Carbon::parse($appointment->appointment_date)->startOfDay();
    $today       = \Carbon\Carbon::today();
    $daysUntil   = $today->diffInDays($apptDate, false); // negative = past

    // Priority: 1) Red  2) Green  3) Yellow
    if (
        in_array($appointment->status, ['cancelled', 'rejected', 'checkout']) ||
        ($apptDate->lt($today) && !in_array($appointment->status, ['checkout', 'cancelled', 'rejected']))
    ) {
        // Red — completed, cancelled, rejected, or missed
        $cardBorderClass = 'border-start border-4 border-danger';
        $cardBgStyle     = 'background-color: #fff5f5;';
    } elseif (
        in_array($appointment->status, ['pending', 'confirmed', 'check_in']) &&
        $daysUntil >= 0 && $daysUntil <= 2
    ) {
        // Green — today or within 2 days
        $cardBorderClass = 'border-start border-4 border-success';
        $cardBgStyle     = 'background-color: #f0fdf4;';
    } else {
        // Yellow — future but more than 2 days away / incomplete
        $cardBorderClass = 'border-start border-4 border-warning';
        $cardBgStyle     = 'background-color: #fffdf0;';
    }
@endphp

<li class="appointments-card section-bg rounded p-5 {{ $cardBorderClass }}" style="{{ $cardBgStyle }}">
    <div class="d-flex justify-content-between align-items-center gap-5 flex-wrap">
        <div class="appointments-badge d-flex column-gap-5 row-gap-2 flex-wrap rounded-pill bg-primary-subtle">
            <span class="appointments-detail">{{ DateFormate($appointment->appointment_date) }}</span>
            <span class="appointments-detail">
                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format(setting('time_formate') ?? 'h:i A') }}</span>
        </div>
        <ul class="list-inline m-0 appointments-meta d-flex column-gap-4 row-gap-3 align-items-center flex-wrap">
            <li>
                <div class="d-flex flex-wrap align-items-center gap-3 ">
                    <p class="mb-0">Appointment ID:</p>
                    <p class="mb-0 font-size-14 text-primary">#{{ $appointment->id }}</p>
                </div>
            </li>
            @if (optional($appointment->clinicservice)->is_video_consultancy)
                <li>
                    <a class="appointments-videocall"
                        href="{{ $appointment->join_video_link ?? $appointment->meet_link }}">
                        <i class="ph ph-video-camera align-middle"></i></a>
                </li>
            @endif
        </ul>
    </div>
    <div class="mt-3">
        @php
            $serviceId = optional($appointment->clinicservice)->id;
        @endphp

        @if ($serviceId)
            <a href="{{ route('service-details', ['id' => $serviceId]) }}">
                <!-- Link content here -->
            </a>
        @endif
        <h5 class="mb-0">{{ optional($appointment->clinicservice)->name }}</h5></a>
    </div>
    <div class="appointments-card-content border-top border-bottom">
        <div class="row gy-3">
            <div class="col-lg-4 col-12">
                <div class="row gy-2 gx-3">
                    <div class="col-md-5">
                        <p class="mb-0">{{ __('frontend.appointment_type') }}
                        </p>
                    </div>
                    <div class="col-md-7">
                        <h6 class="mb-0">
                            {{ \Illuminate\Support\Str::title(str_replace('_', ' ', optional($appointment->clinicservice)->type)) }}
                        </h6>

                    </div>
                    <div class="col-md-5">
                        <p class="mb-0">{{ __('frontend.clinic_name') }}
                        </p>
                    </div>
                    <div class="col-md-7">
                        <h6 class="mb-0">{{ optional($appointment->cliniccenter)->name }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="row gy-2 gx-3">
                    <div class="col-md-5">
                        <p class="mb-0">{{ __('frontend.booking_status') }}
                        </p>
                    </div>
                    <div class="col-md-7">
                        <h6
                            class="mb-0
                            @if ($appointment->status === 'cancelled') text-danger
                            @elseif($appointment->status === 'checkout')
                                text-success
                            @else
                                text-muted @endif
                        ">
                            {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $appointment->status === 'checkout' ? 'Complete' : $appointment->status)) }}
                        </h6>
                    </div>
                    <div class="col-md-5">
                        <p class="mb-0">{{ __('frontend.doctor_name') }}
                        </p>
                    </div>
                    <div class="col-md-7">
                        <h6 class="mb-0">

                            {{ getDisplayName($appointment->doctor) }}

                            <!-- {{ optional($appointment->doctor)->first_name . ' ' . optional($appointment->doctor)->last_name }} -->
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="row gy-2 gx-3">
                    <div class="col-md-5">
                        <p class="mb-0">{{ __('frontend.price') }}
                        </p>
                    </div>

                    @php
                        $total_amount = 0;
                        if ($appointment->patientEncounter != null) {
                            if (!empty(optional($appointment->patientEncounter->billingrecord)->final_total_amount)) {
                                $total_amount = optional($appointment->patientEncounter->billingrecord)
                                    ->final_total_amount;
                            } else {
                                $total_amount = $appointment->total_amount;
                            }
                        } else {
                            $total_amount = $appointment->total_amount;
                        }
                    @endphp

                    <div class="col-md-7">
                        <h6 class="mb-0">{{ Currency::format($total_amount) ?? '--' }}</h6>
                    </div>
                    <div class="col-md-5">
                        <p class="mb-0">{{ __('frontend.payment_status') }}
                        </p>
                    </div>
                    <div class="col-md-7">
                        <h6 class="mb-0 text-danger">
                            @php
                                $transaction = optional($appointment->appointmenttransaction);
                                $isCancelled = $appointment->status == 'cancelled';
                                $isFullyPaid = $transaction->payment_status == 1;
                                $isAdvancePaid =
                                    $appointment->advance_paid_amount > 0 && $transaction->advance_payment_status == 1;
                            @endphp

                            {{-- Cancelled Cases --}}
                            @if ($isCancelled)
                                @if ($isFullyPaid)
                                    <span class="text-success">{{ __('frontend.payment_refunded') }}</span>
                                @elseif($isAdvancePaid)
                                    <span class="text-warning">{{ __('frontend.advance_refunded') }}</span>
                                @else
                                    <span class="text-danger">{{ __('frontend.cancelled') }}</span>
                                @endif

                                {{-- Active or Completed Cases --}}
                            @else
                                @if ($isFullyPaid)
                                    <span class="text-success">{{ __('frontend.paid') }}</span>
                                @elseif($isAdvancePaid)
                                    <span class="text-info">{{ __('frontend.advance_paid') }}</span>
                                @else
                                    <span class="text-danger">{{ __('frontend.pending') }}</span>
                                @endif
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        @if ($appointment->otherPatient)
            <div class="d-flex align-items-center gap-2">
                <span class="font-size-14">{{ __('frontend.booked_for') }}</span>
                <div class="d-flex align-items-center">
                    <img src="{{ optional($appointment->otherPatient)->profile_image ?: asset('images/default-avatar.png') }}"
                        class="rounded-circle me-2" alt="{{ optional($appointment->otherPatient)->first_name }}"
                        style="width: 32px; height: 32px; object-fit: cover;">
                    <span class="fw-medium">{{ optional($appointment->otherPatient)->first_name }}
                        {{ optional($appointment->otherPatient)->last_name }}</span>
                </div>
            </div>
        @endif
    </div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-5 mt-5">
        <div class="d-flex align-items-center flex-wrap gap-4">
            @if ($appointment->status == 'pending' && optional($appointment->appointmenttransaction)->transaction_type == 'cash')
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#cancel-appointment"
                    data-appointment-id="{{ $appointment->id }}" data-charge="0">{{ __('frontend.cancel') }}
                </button>
            @elseif($appointment->status == 'pending' || $appointment->status == 'confirmed')
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#cancel-appointment"
                    data-appointment-id="{{ $appointment->id }}"
                    data-charge="{{ $appointment->getCancellationCharges() }}">{{ __('frontend.cancel') }}
                </button>
            @endif
            @if ($appointment->status == 'checkout' ?? $appointment->status == 'check_in')
                <button data-bs-toggle="modal" data-bs-target="#encounter-details-view-{{ $appointment->id }}"
                    class="btn btn-secondary"><i
                        class="ph ph-gauge align-middle me-2"></i>{{ __('frontend.encounter') }}
                </button>
            @endif
        </div>
        <div class="d-flex align-items-center flex-wrap gap-4">
            <a href="{{ route('appointment-details', ['id' => $appointment->id]) }}"
                class="btn-link text-secondary fw-semibold font-size-14">{{ __('frontend.view_detail') }}
            </a>
            @php
                $serviceRating = optional($appointment->clinicservice)->serviceRating;
            @endphp

            @if (!is_null($serviceRating) && $serviceRating->isEmpty() && $appointment->status == 'checkout')
                <button class="btn btn-light" data-bs-toggle="modal"
                    data-service-id="{{ optional($appointment->clinicservice)->id }}"
                    data-doctor-id="{{ optional($appointment->doctor)->id }}" data-bs-target="#review-service">
                    <i class="ph-fill ph-star text-warning me-2"></i>{{ __('frontend.rate_us') }}
                </button>
            @endif
        </div>
    </div>
</li>


{{-- Encounter modal --}}
<div class="modal  modal-xl fade" id="encounter-details-view-{{ $appointment->id }}">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content section-bg position-relative rounded">
            <div class="close-modal-btn" data-bs-dismiss="modal">
                <i class="ph ph-x align-middle"></i>
            </div>
            <div class="modal-body modal-body-inner modal-enocunter-detail">
                <div class="encounter-info">
                    <h6>{{ __('frontend.basic_information') }}
                    </h6>
                    <div class="encounter-basic-info rounded">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <p class="mb-0 font-size-14">{{ __('frontend.appointment_id') }}
                                    </p>
                                    <span class="text-primary font-size-14 fw-bold">#{{ $appointment->id }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <p class="mb-0 font-size-14">{{ __('frontend.doctor_name') }}
                                    </p>
                                    <span
                                        class="encounter-desc font-size-14 fw-bold">{{ getDisplayName($appointment->doctor) }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <p class="mb-0 font-size-14">{{ __('frontend.clinic_name') }}
                                    </p>
                                    <span
                                        class="encounter-desc font-size-14 fw-bold">{{ optional($appointment->cliniccenter)->name ?? '-' }}</span>
                                </div>
                            </div>
                            <span
                                class="bg-success-subtle badge rounded-pill text-uppercase text-uppercase font-size-10">{{ optional($appointment->patientEncounter)->status ? 'Active' : 'Closed' }}</span>
                        </div>
                        <div class="encounter-descrption border-top">
                            <div class="d-flex gap-2 align-items-center">
                                <span class="font-size-14 flex-shrink-0">{{ __('frontend.description') }}
                                </span>
                                <p class="font-size-14 fw-semibold detail-desc mb-0">
                                    {{ optional($appointment->patientEncounter)->descrtiption ?? 'No records found' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @php
                        $problems = $medical_history->get('encounter_problem', collect());
                        $observations = $medical_history->get('encounter_observations', collect());
                        $notes = $medical_history->get('encounter_notes', collect());
                    @endphp

                    <div class="encounter-box mt-5">
                        <a class="d-flex gap-4 mb-2 encounter-list  justify-content-between"
                            href="#problem-{{ $appointment->id }}" data-bs-toggle="collapse">
                            <p class="mb-0 h6">Problem</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="problem-{{ $appointment->id }}" class="collapse rounded encounter-inner-box">
                            @if ($problems->isNotEmpty())
                                @foreach ($problems as $problem)
                                    <p class="font-size-14">{{ $loop->iteration }}. {{ $problem->title }}</p>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">No problems found</p>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list"
                            href="#observation-{{ $appointment->id }}" data-bs-toggle="collapse">
                            <p class="mb-0 h6">Observation</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="observation-{{ $appointment->id }}" class="collapse  encounter-inner-box rounded">
                            @if ($observations->isNotEmpty())
                                @foreach ($observations as $observation)
                                    <p class="font-size-14">{{ $loop->iteration }}. {{ $observation->title }}</p>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">No observation found</p>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list"
                            href="#notes-{{ $appointment->id }}" data-bs-toggle="collapse">
                            <p class="mb-0 h6">Notes</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="notes-{{ $appointment->id }}" class="collapse  encounter-inner-box rounded">
                            @if ($observations->isNotEmpty())
                                @foreach ($notes as $note)
                                    <p class="font-size-14 mb-0">{{ $loop->iteration }}. {{ $note->title }}</p>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">No note found</p>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list"
                            href="#medical-report-{{ $appointment->id }}" data-bs-toggle="collapse">
                            <p class="mb-0 h6">Medical Report</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="medical-report-{{ $appointment->id }}" class="collapse encounter-inner-box rounded">
                            @if ($appointment->media->isNotEmpty())
                                @foreach ($appointment->media as $media)
                                    <a href="{{ asset($media->getUrl()) }}" download class="btn btn-primary">
                                        Download Report
                                    </a>
                                @endforeach
                            @elseif (!$medical_report || !$medical_report->file_url)
                                <p class="font-size-12 mb-0 text-danger text-center">No medical report found</p>
                            @endif

                            @if ($medical_report && $medical_report->file_url)
                                <a href="{{ asset($medical_report->file_url) }}" download class="btn btn-primary">
                                    Download Report
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list"
                            href="#body_chart-{{ $appointment->id }}" data-bs-toggle="collapse">
                            <p class="mb-0 h6">Body chart</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="body_chart-{{ $appointment->id }}" class="collapse  encounter-inner-box rounded">
                            @if ($bodychart->isNotEmpty())
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach ($bodychart as $chart)
                                        @foreach ($chart->media as $media)
                                            <!-- Iterate through the media collection -->
                                            <div class="body-chart-content text-center">
                                                <div class="image mb-2">
                                                    <img src="{{ asset($media->getUrl()) }}"
                                                        alt="{{ $media->name }}" class="img-fluid" width="100"
                                                        height="100">
                                                </div>
                                                <a href="{{ asset($media->getUrl()) }}" download>
                                                    Download
                                                </a>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">No report found</p>
                            @endif
                        </div>
                    </div>
                    <div class="encounter-box mt-5">
                        <a class="d-flex justify-content-between gap-3 mb-2 encounter-list"
                            href="#prescription-{{ $appointment->id }}" data-bs-toggle="collapse">
                            <p class="mb-0 h6">Prescription</p>
                            <i class="ph ph-caret-down"></i>
                        </a>
                        <div id="prescription-{{ $appointment->id }}" class="collapse  encounter-inner-box rounded">
                            @if ($prescriptions->isNotEmpty())
                                @foreach ($prescriptions as $prescription)
                                    <div class="encounter-prescription-box">
                                        <h6>{{ $prescription->name }}</h6>
                                        @if ($prescription->instruction)
                                            <p class="font-size-14 mb-0">{{ $prescription->instruction }}</p>
                                        @endif
                                        <div class="mt-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <span class="font-size-14 mb-2">Frequency:</span>
                                                    <h6 class="font-size-14 mb-0">{{ $prescription->frequency }}</h6>
                                                </div>
                                                <div class="col-md-6 mt-md-0 mt-4">
                                                    <span class="font-size-14 mb-2">Days:</span>
                                                    <h6 class="font-size-14 mb-0">{{ $prescription->duration }} Days
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="font-size-12 mb-0 text-danger text-center">No prescription found</p>
                            @endif
                        </div>


                        <div class="encounter-box mt-5">
                            <a class="d-flex justify-content-between gap-3 mb-2 encounter-list" href="#prescription"
                                data-bs-toggle="collapse">
                                <p class="mb-0 h6">{{ __('frontend.soap') }}
                                </p>
                                <i class="ph ph-caret-down"></i>
                            </a>
                            <div id="prescription" class="collapse  encounter-inner-box rounded">
                                @if ($soap)
                                    <div class="border-top mb-3">
                                        <div class="row">
                                            <div class="col-md-6 ">

                                                <h6 class="font-size-14">{{ __('frontend.subjective') }} </h6>

                                                <span class="font-size-14 mb-2">{{ $soap->subjective }}</span>

                                            </div>
                                            <div class="col-md-6 ">
                                                <h6 class="font-size-14 mb-2">{{ __('frontend.objective') }}
                                                </h6>
                                                <span class="font-size-14">{{ $soap->objective }}</span>

                                            </div>

                                            <div class="col-md-6 ">
                                                <h6 class="font-size-14">{{ __('frontend.assessment') }}
                                                </h6>
                                                <span class="font-size-14 mb-2">
                                                    {{ $soap->assessment }}
                                                </span>

                                            </div>
                                            <div class="col-md-6 ">
                                                <h6 class="font-size-14">{{ __('frontend.plan') }}
                                                </h6>
                                                <span class="font-size-14 mb-2">
                                                    {{ $soap->plan }}
                                                </span>

                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="font-size-12 mb-0 text-danger text-center">
                                        {{ __('frontend.no_soap_found') }}
                                    </p>
                                @endif
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
