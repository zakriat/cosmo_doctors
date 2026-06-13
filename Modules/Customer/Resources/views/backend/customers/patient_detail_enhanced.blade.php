@extends('backend.layouts.app')
@section('title')
  {{ __($module_title) }}
@endsection

@push('after-styles')
<style>
/* Enhanced Patient Detail Page Styles */
.patient-overview-tab .nav-link {
    border-radius: 50px;
    padding: 12px 20px;
    margin-right: 8px;
    border: 1px solid transparent;
    transition: all 0.3s ease;
}

.patient-overview-tab .nav-link:hover {
    background-color: var(--bs-primary-bg-subtle);
    color: var(--bs-primary);
}

.patient-overview-tab .nav-link.active {
    background-color: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

.patient-overview-tab .nav-link i {
    margin-right: 8px;
    font-size: 1.1em;
}

.timeline {
    position: relative;
}

.timeline .card {
    position: relative;
    margin-bottom: 1rem;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

@media (max-width: 768px) {
    .patient-overview-tab {
        flex-wrap: wrap;
    }
    
    .patient-overview-tab .nav-link {
        margin-bottom: 8px;
        font-size: 14px;
        padding: 10px 16px;
    }
}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
    <h4>{{$data['patientInfo']['name']}}  {{ __('customer.overview') }}</h4>
    <a href="{{ route('backend.customers.index') }}" class="btn btn-primary">{{ __('messages.back') }}
    </a>
</div>

<!-- Enhanced Tab Navigation -->
<ul class="nav nav-pills mb-4 patient-overview-tab" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
            <i class="ph ph-house"></i>
            <span>{{ __('customer.overview') }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="encounters-tab" data-bs-toggle="pill" data-bs-target="#encounters" type="button" role="tab" aria-controls="encounters" aria-selected="false">
            <i class="ph ph-stethoscope"></i>
            <span>Triage</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="prescriptions-tab" data-bs-toggle="pill" data-bs-target="#prescriptions" type="button" role="tab" aria-controls="prescriptions" aria-selected="false">
            <i class="ph ph-pill"></i>
            <span>Prescriptions</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="appointments-tab" data-bs-toggle="pill" data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="false">
            <i class="ph ph-calendar-dots"></i>
            <span>Appointments</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="medical-records-tab" data-bs-toggle="pill" data-bs-target="#medical-records" type="button" role="tab" aria-controls="medical-records" aria-selected="false">
            <i class="ph ph-folder-open"></i>
            <span>Medical Records</span>
        </button>
    </li>
    <!-- <li class="nav-item" role="presentation">
        <button class="nav-link" id="other-patients-tab" data-bs-toggle="pill" data-bs-target="#other-patients" type="button" role="tab" aria-controls="other-patients" aria-selected="false">
            <i class="ph ph-users-three"></i>
            <span>{{ __('customer.book_for_other') }}</span>
        </button>
    </li> -->
</ul>

<div class="tab-content" id="pills-tabContent">
    <!-- Overview Tab -->
    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab" tabindex="0">
        <div class="card">
            <div class="card-body">
                <!-- Patient Basic Info -->
                <div class="mb-4">
                    <h4 class="mb-3">{{ __('customer.patient_basic_info') }}</h4>
                    <div class="d-flex gap-3 align-items-center p-4 bg-body rounded flex-md-nowrap flex-wrap">
                        <div>
                            <img src="{{ $patient->profile_image ?? default_user_avatar() }}" alt="Profile Image"
                                    class="avatar avatar-80 rounded-pill">
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="m-0">{{ $data['patientInfo']['name'] }}</h4>
                            <div class="d-flex align-items-center column-gap-3 row-gap-2 mt-3 flex-md-nowrap flex-wrap">
                                <div class="d-flex align-items-center gap-2 text-break">
                                    <i class="ph ph-envelope-simple text-heading"></i>
                                    <a href="#" class="text-secondary text-decoration-underline font-size-16">
                                            {{ $data['patientInfo']['email'] }}
                                    </a>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ph ph-phone text-heading"></i>
                                    <a href="#" class="text-primary text-decoration-underline font-size-16">
                                            {{ $data['patientInfo']['contact'] }}
                                    </a>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ph ph-cake text-heading"></i>
                                    <span class="font-size-16"> {{ \Carbon\Carbon::parse($data['patientInfo']['dob'])->format('d-m-Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="row">
                    <!-- Recent Encounters -->
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Encounters</h5>
                                <button class="btn btn-sm btn-outline-primary" onclick="switchToTab('encounters')">View All</button>
                            </div>
                            <div class="card-body">
                                @if($data['recentEncounters']->count() > 0)
                                    @foreach($data['recentEncounters'] as $encounter)
                                        <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                                            <div class="avatar-40 bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="ph ph-stethoscope text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ \Carbon\Carbon::parse($encounter->encounter_date)->format('M d, Y') }}</h6>
                                                <p class="text-muted mb-1 small">Dr. {{ $encounter->doctor->first_name ?? '' }} {{ $encounter->doctor->last_name ?? '' }}</p>
                                                <p class="mb-0 small">{{ Str::limit($encounter->description ?? 'No description', 50) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted text-center">No recent encounters</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recent Prescriptions -->
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Prescriptions</h5>
                                <button class="btn btn-sm btn-outline-primary" onclick="switchToTab('prescriptions')">View All</button>
                            </div>
                            <div class="card-body">
                                @if($data['recentPrescriptions']->count() > 0)
                                    @foreach($data['recentPrescriptions'] as $prescription)
                                        <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                                            <div class="avatar-40 bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="ph ph-pill text-success"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $prescription->medicine->name ?? $prescription->name }}</h6>
                                                <p class="text-muted mb-1 small">{{ $prescription->frequency }} - {{ $prescription->duration }}</p>
                                                <p class="mb-0 small">Dr. {{ $prescription->encounter->doctor->first_name ?? '' }} {{ $prescription->encounter->doctor->last_name ?? '' }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted text-center">No recent prescriptions</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Appointments -->
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Upcoming Appointments</h5>
                                <button class="btn btn-sm btn-outline-primary" onclick="switchToTab('appointments')">View All</button>
                            </div>
                            <div class="card-body">
                                @if($data['upcomingAppointments']->count() > 0)
                                    @foreach($data['upcomingAppointments'] as $appointment)
                                        <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                                            <div class="avatar-40 bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="ph ph-calendar-dots text-warning"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</h6>
                                                <p class="text-muted mb-1 small">{{ $appointment->appointment_time }}</p>
                                                <p class="mb-0 small">Dr. {{ $appointment->doctor->first_name ?? '' }} {{ $appointment->doctor->last_name ?? '' }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted text-center">No upcoming appointments</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-4">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-primary">
                            <i class="ph ph-calendar-plus me-2"></i>New Appointment
                        </button>
                        <button class="btn btn-outline-primary" onclick="switchToTab('medical-records')">
                            <i class="ph ph-folder-open me-2"></i>View Records
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="ph ph-download me-2"></i>Export Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Encounters Tab -->
    <div class="tab-pane fade" id="encounters" role="tabpanel" aria-labelledby="encounters-tab" tabindex="0">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">Patient Encounters</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-md-end">
                            <input type="text" class="form-control form-control-sm" id="encounters-search" placeholder="Search encounters...">
                            <button class="btn btn-sm btn-outline-primary" id="encounters-filter-btn">
                                <i class="ph ph-funnel"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Filter Row -->
                <div class="row mt-3 d-none" id="encounters-filters">
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="encounters-doctor-filter">
                            <option value="">All Doctors</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" id="encounters-date-from" placeholder="From Date">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" id="encounters-date-to" placeholder="To Date">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-primary w-100" id="encounters-apply-filter">Apply</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="encounters-content">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prescriptions Tab -->
    <div class="tab-pane fade" id="prescriptions" role="tabpanel" aria-labelledby="prescriptions-tab" tabindex="0">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">Patient Prescriptions</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-md-end">
                            <input type="text" class="form-control form-control-sm" id="prescriptions-search" placeholder="Search medicines...">
                            <button class="btn btn-sm btn-outline-primary" id="prescriptions-filter-btn">
                                <i class="ph ph-funnel"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Filter Row -->
                <div class="row mt-3 d-none" id="prescriptions-filters">
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="prescriptions-doctor-filter">
                            <option value="">All Doctors</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" id="prescriptions-date-from" placeholder="From Date">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" id="prescriptions-date-to" placeholder="To Date">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-primary w-100" id="prescriptions-apply-filter">Apply</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="prescriptions-content">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments Tab -->
    <div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab" tabindex="0">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">Patient Appointments</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-md-end">
                            <select class="form-select form-select-sm" id="appointments-status-filter">
                                <option value="">All Status</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="checkout">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <button class="btn btn-sm btn-outline-primary" id="appointments-filter-btn">
                                <i class="ph ph-funnel"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Filter Row -->
                <div class="row mt-3 d-none" id="appointments-filters">
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" id="appointments-doctor-filter">
                            <option value="">All Doctors</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" id="appointments-date-from" placeholder="From Date">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" id="appointments-date-to" placeholder="To Date">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-primary w-100" id="appointments-apply-filter">Apply</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="appointments-content">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Records Tab -->
    <div class="tab-pane fade" id="medical-records" role="tabpanel" aria-labelledby="medical-records-tab" tabindex="0">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Medical Records</h5>
            </div>
            <div class="card-body">
                <div id="medical-records-content">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Other Patients Tab (existing functionality) -->
    <div class="tab-pane fade" id="other-patients" role="tabpanel" aria-labelledby="other-patients-tab" tabindex="0">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-end mb-3">
                    <button class="btn btn-secondary"  data-bs-toggle="modal" data-bs-target="#addOtherPatientModal">
                        {{ __('customer.add_other_patient') }}
                    </button>
                </div>
                <div class="row gy-3">
                    @if ($otherPatients->isNotEmpty())
                        @foreach ($otherPatients as $otherPatient)
                            <div class="col-lg-12">
                                <div class="card rounded-3 card-end bg-body">
                                    <div class="card-body">
                                        <div class="d-flex flex-sm-nowrap flex-wrap gap-3">
                                            <div class="avatar-wrapper">
                                                <img src="{{ $otherPatient->getFirstMediaUrl('profile_image') ? asset($otherPatient->getFirstMediaUrl('profile_image')) : default_user_avatar() }}"
                                                alt="profile image" class="rounded-circle me-3" width="60" height="60">
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                                                        <h5 class="font-size-18 mb-0">{{ $otherPatient->first_name }} {{ $otherPatient->last_name }}</h5>
                                                        <span class="badge bg-secondary-subtle rounded-pill">{{ $otherPatient->relation }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center column-gap-3 row-gap-2">
                                                        <button type="button editBtn" class="btn btn-link p-0 editBtn text-icon" data-id="{{$otherPatient->id}}" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal_{{ $otherPatient->id }}">
                                                                <i class="ph ph-pencil-simple font-size-18"></i>
                                                        </button>
                                                        <form method="POST" action="{{ route('backend.customers.otherPatient.delete', $otherPatient->id) }}" class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-link p-0 text-icon deleteBtn delete-patient"
                                                                data-id="{{ $otherPatient->id }}"
                                                                data-name="{{ $otherPatient->first_name }} {{ $otherPatient->last_name }}"
                                                                data-url="{{ route('backend.customers.otherPatient.delete', $otherPatient->id) }}"
                                                                title="Delete">
                                                                <i class="ph ph-trash font-size-18"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center flex-wrap gap-3">
                                                    <div class="d-flex align-items-center gap-3 font-size-14">
                                                        <i class="ph ph-user text-heading"></i>{{ $otherPatient->gender }}
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3 font-size-14">
                                                        <i class="ph ph-phone text-heading"></i>{{ $otherPatient->contactNumber }}
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3 font-size-14">
                                                        <i class="ph ph-cake text-heading"></i> {{ \Carbon\Carbon::parse($otherPatient->dob)->format('d-m-Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info col-12">
                            {{ __('customer.no_patient_available') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include existing modals for other patients -->
@include('customer::backend.customers.partials.other_patient_modals', ['otherPatients' => $otherPatients])

@endsection

@push('after-scripts')
<script src="{{ mix('modules/customer/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.16/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
const patientId = {{ $data['patientInfo']['id'] }};

// Tab switching functionality
function switchToTab(tabName) {
    const tabButton = document.getElementById(tabName + '-tab');
    if (tabButton) {
        tabButton.click();
    }
}

// Load tab content when tab is shown
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('[data-bs-toggle="pill"]');
    
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(e) {
            const targetTab = e.target.getAttribute('data-bs-target').replace('#', '');
            loadTabContent(targetTab);
        });
    });

    // Setup filter toggles
    setupFilterToggles();
});

function loadTabContent(tabName) {
    switch(tabName) {
        case 'encounters':
            loadEncounters();
            break;
        case 'prescriptions':
            loadPrescriptions();
            break;
        case 'appointments':
            loadAppointments();
            break;
        case 'medical-records':
            loadMedicalRecords();
            break;
    }
}

function setupFilterToggles() {
    // Encounters filter toggle
    document.getElementById('encounters-filter-btn').addEventListener('click', function() {
        const filters = document.getElementById('encounters-filters');
        filters.classList.toggle('d-none');
    });

    // Prescriptions filter toggle
    document.getElementById('prescriptions-filter-btn').addEventListener('click', function() {
        const filters = document.getElementById('prescriptions-filters');
        filters.classList.toggle('d-none');
    });

    // Appointments filter toggle
    document.getElementById('appointments-filter-btn').addEventListener('click', function() {
        const filters = document.getElementById('appointments-filters');
        filters.classList.toggle('d-none');
    });

    // Apply filter buttons
    document.getElementById('encounters-apply-filter').addEventListener('click', loadEncounters);
    document.getElementById('prescriptions-apply-filter').addEventListener('click', loadPrescriptions);
    document.getElementById('appointments-apply-filter').addEventListener('click', loadAppointments);

    // Search inputs
    let encountersSearchTimeout;
    document.getElementById('encounters-search').addEventListener('input', function() {
        clearTimeout(encountersSearchTimeout);
        encountersSearchTimeout = setTimeout(loadEncounters, 500);
    });

    let prescriptionsSearchTimeout;
    document.getElementById('prescriptions-search').addEventListener('input', function() {
        clearTimeout(prescriptionsSearchTimeout);
        prescriptionsSearchTimeout = setTimeout(loadPrescriptions, 500);
    });

    // Status filter for appointments
    document.getElementById('appointments-status-filter').addEventListener('change', loadAppointments);
}

function loadEncounters() {
    const content = document.getElementById('encounters-content');
    content.innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

    const params = new URLSearchParams({
        search: document.getElementById('encounters-search').value,
        doctor_id: document.getElementById('encounters-doctor-filter').value,
        date_from: document.getElementById('encounters-date-from').value,
        date_to: document.getElementById('encounters-date-to').value
    });

    fetch(`/app/customers/patient/${patientId}/encounters?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                renderEncounters(data.data);
            } else {
                content.innerHTML = '<div class="alert alert-danger">Error loading encounters</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="alert alert-danger">Error loading encounters</div>';
        });
}

function loadPrescriptions() {
    const content = document.getElementById('prescriptions-content');
    content.innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

    const params = new URLSearchParams({
        search: document.getElementById('prescriptions-search').value,
        doctor_id: document.getElementById('prescriptions-doctor-filter').value,
        date_from: document.getElementById('prescriptions-date-from').value,
        date_to: document.getElementById('prescriptions-date-to').value
    });

    fetch(`/app/customers/patient/${patientId}/prescriptions?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                renderPrescriptions(data.data);
            } else {
                content.innerHTML = '<div class="alert alert-danger">Error loading prescriptions</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="alert alert-danger">Error loading prescriptions</div>';
        });
}

function loadAppointments() {
    const content = document.getElementById('appointments-content');
    content.innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

    const params = new URLSearchParams({
        status: document.getElementById('appointments-status-filter').value,
        doctor_id: document.getElementById('appointments-doctor-filter').value,
        date_from: document.getElementById('appointments-date-from').value,
        date_to: document.getElementById('appointments-date-to').value
    });

    fetch(`/app/customers/patient/${patientId}/appointments?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                renderAppointments(data.data);
            } else {
                content.innerHTML = '<div class="alert alert-danger">Error loading appointments</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="alert alert-danger">Error loading appointments</div>';
        });
}

function loadMedicalRecords() {
    const content = document.getElementById('medical-records-content');
    content.innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

    fetch(`/app/customers/patient/${patientId}/medical-records`)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                renderMedicalRecords(data.data);
            } else {
                content.innerHTML = '<div class="alert alert-danger">Error loading medical records</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="alert alert-danger">Error loading medical records</div>';
        });
}

function renderEncounters(data) {
    const content = document.getElementById('encounters-content');
    
    if (data.data.length === 0) {
        content.innerHTML = '<div class="alert alert-info">No encounters found</div>';
        return;
    }

    let html = '<div class="timeline">';
    
    data.data.forEach(encounter => {
        const date = new Date(encounter.encounter_date).toLocaleDateString();
        const doctorName = encounter.doctor ? `Dr. ${encounter.doctor.first_name} ${encounter.doctor.last_name}` : 'Unknown Doctor';
        const clinicName = encounter.clinic ? encounter.clinic.name : 'Unknown Clinic';
        const prescriptionCount = encounter.prescriptions ? encounter.prescriptions.length : 0;
        
        html += `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1">${date}</h6>
                            <p class="text-muted mb-0">${doctorName} • ${clinicName}</p>
                        </div>
                        <span class="badge bg-primary">${prescriptionCount} Prescriptions</span>
                    </div>
                    
                    ${encounter.description ? `<p class="mb-2">${encounter.description}</p>` : ''}
                    
                    ${encounter.medical_histroy && encounter.medical_histroy.length > 0 ? `
                        <div class="mb-2">
                            <small class="text-muted">Medical History:</small>
                            ${encounter.medical_histroy.map(history => `<span class="badge bg-secondary-subtle me-1">${history.type}: ${history.title}</span>`).join('')}
                        </div>
                    ` : ''}
                    
                    ${encounter.encounter_other_details && encounter.encounter_other_details.other_details ? `
                        <div class="mb-2">
                            <small class="text-muted">Other Details:</small>
                            <p class="small mb-0">${encounter.encounter_other_details.other_details}</p>
                        </div>
                    ` : ''}
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary">View Details</button>
                        ${encounter.medical_report && encounter.medical_report.length > 0 ? '<button class="btn btn-sm btn-outline-secondary">Download Report</button>' : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    // Add pagination if needed
    if (data.next_page_url || data.prev_page_url) {
        html += '<div class="d-flex justify-content-center mt-3">';
        html += '<nav><ul class="pagination">';
        if (data.prev_page_url) {
            html += '<li class="page-item"><a class="page-link" href="#" onclick="loadEncountersPage(' + (data.current_page - 1) + ')">Previous</a></li>';
        }
        if (data.next_page_url) {
            html += '<li class="page-item"><a class="page-link" href="#" onclick="loadEncountersPage(' + (data.current_page + 1) + ')">Next</a></li>';
        }
        html += '</ul></nav></div>';
    }
    
    content.innerHTML = html;
}

function renderPrescriptions(data) {
    const content = document.getElementById('prescriptions-content');
    
    if (data.data.length === 0) {
        content.innerHTML = '<div class="alert alert-info">No prescriptions found</div>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-lg m-0"><thead><tr>';
    html += '<th>Medicine</th><th>Strength & Form</th><th>Frequency</th><th>Duration</th><th>Doctor</th><th>Date</th><th>Actions</th>';
    html += '</tr></thead><tbody>';
    
    data.data.forEach(prescription => {
        const medicine = prescription.medicine || {};
        const encounter = prescription.encounter || {};
        const doctor = encounter.doctor || {};
        const encounterDate = encounter.encounter_date ? new Date(encounter.encounter_date).toLocaleDateString() : 'N/A';
        
        html += `
            <tr>
                <td>
                    <div>
                        <strong>${medicine.name || prescription.name}</strong>
                        ${medicine.generic_name ? `<br><small class="text-muted">Generic: ${medicine.generic_name}</small>` : ''}
                        ${medicine.brand_name ? `<br><small class="text-muted">Brand: ${medicine.brand_name}</small>` : ''}
                    </div>
                </td>
                <td>
                    ${medicine.strength || 'N/A'}
                    ${medicine.dosage_form ? `<br><small class="text-muted">${medicine.dosage_form}</small>` : ''}
                </td>
                <td>${prescription.frequency || 'N/A'}</td>
                <td>${prescription.duration || 'N/A'}</td>
                <td>Dr. ${doctor.first_name || ''} ${doctor.last_name || ''}</td>
                <td>${encounterDate}</td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary" title="View Details">
                            <i class="ph ph-eye"></i>
                        </button>
                        ${medicine.url ? `<a href="${medicine.url}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View BNF"><i class="ph ph-link"></i></a>` : ''}
                    </div>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    
    content.innerHTML = html;
}

function renderAppointments(data) {
    const content = document.getElementById('appointments-content');
    
    if (data.data.length === 0) {
        content.innerHTML = '<div class="alert alert-info">No appointments found</div>';
        return;
    }

    let html = '';
    
    data.data.forEach(appointment => {
        const date = new Date(appointment.appointment_date).toLocaleDateString();
        const doctor = appointment.doctor || {};
        const clinic = appointment.cliniccenter || {};
        const service = appointment.clinicservice || {};
        const transaction = appointment.appointmenttransaction || {};
        
        let statusBadge = '';
        switch(appointment.status) {
            case 'confirmed':
                statusBadge = '<span class="badge bg-primary">Confirmed</span>';
                break;
            case 'checkout':
                statusBadge = '<span class="badge bg-success">Completed</span>';
                break;
            case 'cancelled':
                statusBadge = '<span class="badge bg-danger">Cancelled</span>';
                break;
            default:
                statusBadge = `<span class="badge bg-secondary">${appointment.status}</span>`;
        }
        
        html += `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1">${date} at ${appointment.appointment_time}</h6>
                            <p class="text-muted mb-0">Dr. ${doctor.first_name || ''} ${doctor.last_name || ''} • ${clinic.name || 'Unknown Clinic'}</p>
                        </div>
                        ${statusBadge}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Service:</small>
                            <p class="mb-1">${service.name || 'N/A'}</p>
                            
                            <small class="text-muted">Duration:</small>
                            <p class="mb-1">${appointment.duration || 'N/A'} minutes</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Total Amount:</small>
                            <p class="mb-1">$${appointment.total_amount || '0.00'}</p>
                            
                            ${appointment.advance_paid_amount ? `
                                <small class="text-muted">Advance Paid:</small>
                                <p class="mb-1">$${appointment.advance_paid_amount}</p>
                            ` : ''}
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-sm btn-outline-primary">View Details</button>
                        ${appointment.status === 'checkout' ? '<button class="btn btn-sm btn-outline-success">View Encounter</button>' : ''}
                        ${appointment.status === 'confirmed' ? '<button class="btn btn-sm btn-outline-warning">Reschedule</button>' : ''}
                        ${appointment.status === 'confirmed' ? '<button class="btn btn-sm btn-outline-danger">Cancel</button>' : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    content.innerHTML = html;
}

function renderMedicalRecords(data) {
    const content = document.getElementById('medical-records-content');
    
    let html = '<div class="accordion" id="medicalRecordsAccordion">';
    
    // Medical History Section
    if (data.medicalHistory && data.medicalHistory.length > 0) {
        html += `
            <div class="accordion-item">
                <h2 class="accordion-header" id="medicalHistoryHeading">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#medicalHistoryCollapse" aria-expanded="true" aria-controls="medicalHistoryCollapse">
                        <i class="ph ph-heart me-2"></i>Medical History (${data.medicalHistory.length})
                    </button>
                </h2>
                <div id="medicalHistoryCollapse" class="accordion-collapse collapse show" aria-labelledby="medicalHistoryHeading" data-bs-parent="#medicalRecordsAccordion">
                    <div class="accordion-body">
        `;
        
        data.medicalHistory.forEach(history => {
            const encounter = history.encounter || {};
            const doctor = encounter.doctor || {};
            const date = encounter.encounter_date ? new Date(encounter.encounter_date).toLocaleDateString() : 'N/A';
            
            html += `
                <div class="d-flex justify-content-between align-items-center p-3 border rounded mb-2">
                    <div>
                        <h6 class="mb-1">${history.type}: ${history.title}</h6>
                        <small class="text-muted">Dr. ${doctor.first_name || ''} ${doctor.last_name || ''} • ${date}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary">View Encounter</button>
                </div>
            `;
        });
        
        html += '</div></div></div>';
    }
    
    // Medical Reports Section
    if (data.medicalReports && data.medicalReports.length > 0) {
        html += `
            <div class="accordion-item">
                <h2 class="accordion-header" id="medicalReportsHeading">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#medicalReportsCollapse" aria-expanded="false" aria-controls="medicalReportsCollapse">
                        <i class="ph ph-file-text me-2"></i>Medical Reports & Documents (${data.medicalReports.length})
                    </button>
                </h2>
                <div id="medicalReportsCollapse" class="accordion-collapse collapse" aria-labelledby="medicalReportsHeading" data-bs-parent="#medicalRecordsAccordion">
                    <div class="accordion-body">
        `;
        
        data.medicalReports.forEach(report => {
            const encounter = report.encounter || {};
            const doctor = encounter.doctor || {};
            const date = report.date ? new Date(report.date).toLocaleDateString() : 'N/A';
            
            html += `
                <div class="d-flex justify-content-between align-items-center p-3 border rounded mb-2">
                    <div>
                        <h6 class="mb-1">${report.name}</h6>
                        <small class="text-muted">Dr. ${doctor.first_name || ''} ${doctor.last_name || ''} • ${date}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary">View</button>
                        <button class="btn btn-sm btn-outline-secondary">Download</button>
                    </div>
                </div>
            `;
        });
        
        html += '</div></div></div>';
    }
    
    // Other Details Section
    if (data.otherDetails && data.otherDetails.length > 0) {
        html += `
            <div class="accordion-item">
                <h2 class="accordion-header" id="otherDetailsHeading">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#otherDetailsCollapse" aria-expanded="false" aria-controls="otherDetailsCollapse">
                        <i class="ph ph-note me-2"></i>Other Details & Notes (${data.otherDetails.length})
                    </button>
                </h2>
                <div id="otherDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="otherDetailsHeading" data-bs-parent="#medicalRecordsAccordion">
                    <div class="accordion-body">
        `;
        
        data.otherDetails.forEach(detail => {
            const encounter = detail.encounter || {};
            const doctor = encounter.doctor || {};
            const date = encounter.encounter_date ? new Date(encounter.encounter_date).toLocaleDateString() : 'N/A';
            
            html += `
                <div class="p-3 border rounded mb-2">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <small class="text-muted">Dr. ${doctor.first_name || ''} ${doctor.last_name || ''} • ${date}</small>
                        <button class="btn btn-sm btn-outline-primary">View Encounter</button>
                    </div>
                    <p class="mb-0">${detail.other_details}</p>
                </div>
            `;
        });
        
        html += '</div></div></div>';
    }
    
    html += '</div>';
    
    if (!data.medicalHistory?.length && !data.medicalReports?.length && !data.otherDetails?.length) {
        html = '<div class="alert alert-info">No medical records found</div>';
    }
    
    content.innerHTML = html;
}

// Include existing other patient functionality
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("addPatientForm");
    const modalElement = document.getElementById("addOtherPatientModal");

    // Extract ID from URL
    const urlSegments = window.location.pathname.split('/');
    const userId = urlSegments[urlSegments.length - 1];

    // Image upload functionality
    const profileImageInput = document.getElementById('add-patient-profile');
    const profileImagePreview = document.getElementById('add-patient-preview');
    const removeImageBtn = document.getElementById('add-patient-remove-image');

    if (profileImageInput && profileImagePreview) {
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeImageBtn && profileImagePreview) {
        removeImageBtn.addEventListener('click', function() {
            profileImagePreview.src = "{{ default_file_url() }}";
            if (profileImageInput) {
                profileImageInput.value = '';
            }
        });
    }

    function formatIntlWithSpace(inputEl, itiInstance) {
        if (!inputEl || !itiInstance) return inputEl ? inputEl.value : '';
        try {
            var dial = (itiInstance.getSelectedCountryData && itiInstance.getSelectedCountryData().dialCode) || '';
            var full = (itiInstance.getNumber && typeof itiInstance.getNumber === 'function') ? itiInstance.getNumber() : '';
            if (full && typeof full === 'string') {
                var digits = full.replace(/\D/g, '');
                if (dial && digits.startsWith(dial)) {
                    var rest = digits.slice(dial.length);
                    return rest ? '+' + dial + ' ' + rest : '+' + dial;
                }
                if (dial) {
                    var remaining = digits;
                    return remaining ? '+' + dial + ' ' + remaining : '+' + dial;
                }
                return full;
            }
            var raw = (inputEl.value || '').replace(/\D/g, '');
            if (dial && raw) {
                if (raw.startsWith(dial)) raw = raw.slice(dial.length);
                return '+' + dial + (raw ? ' ' + raw : '');
            }
        } catch (e) {}
        return inputEl.value || '';
    }

    // Add event listener to handle form submission
    if (document.getElementById('add-patient-submit-btn')) {
        document.getElementById('add-patient-submit-btn').addEventListener('click', function(e) {
            e.preventDefault();

            // Clear previous error messages
            form.querySelectorAll(".error").forEach(el => el.textContent = '');

            const firstName = form.querySelector('[name="first_name"]');
            const lastName = form.querySelector('[name="last_name"]');
            const dob = form.querySelector('[name="dob"]');
            const contactNumber = form.querySelector('[name="contactNumber"]');
            const gender = form.querySelector('[name="gender"]:checked');
            const relation = form.querySelector('[name="relation"]:checked');

            // Validation
            if (!firstName.value.trim() || !lastName.value.trim() || !dob.value.trim() || !contactNumber.value.trim() || !gender || !relation) {
                if (!firstName.value.trim()) {
                    const container = firstName.closest('.mb-3');
                    container.querySelector('.error').textContent = 'First Name field is required.';
                }
                if (!lastName.value.trim()) {
                    const container = lastName.closest('.mb-3');
                    container.querySelector('.error').textContent = 'Last Name field is required.';
                }
                if (!dob.value.trim()) {
                    const container = dob.closest('.mb-3');
                    container.querySelector('.error').textContent = 'Date of Birth field is required.';
                }
                if (!contactNumber.value.trim()) {
                    const container = contactNumber.closest('.mb-3');
                    container.querySelector('.error').textContent = 'Phone Number field is required.';
                }
                if (!gender) {
                    const genderContainer = form.querySelector('.mb-3 .gender-error').closest('.mb-3');
                    const genderError = genderContainer.querySelector('.gender-error');
                    genderError.textContent = 'Gender field is required.';
                }
                if (!relation) {
                    const relationContainer = form.querySelector('.mb-3 .relation-error').closest('.mb-3');
                    const relationError = relationContainer.querySelector('.relation-error');
                    relationError.textContent = 'Relation field is required.';
                }
                return;
            }

            // Format phone number
            try {
                if (window.intlTelInputGlobals && contactNumber) {
                    const itiInstance = window.intlTelInputGlobals.getInstance(contactNumber);
                    if (itiInstance) {
                        contactNumber.value = formatIntlWithSpace(contactNumber, itiInstance);
                    }
                } 
            } catch (err) {}

            const formData = new FormData(form);
            formData.append("user_id", userId);

            fetch("{{ route('backend.customers.other_patient') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Accept": "application/json"
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    $("#addOtherPatientModal").modal("hide");
                    if (typeof successSnackbar === 'function') {
                        successSnackbar("Patient added successfully!");
                    }
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }

    // Reset form when modal is closed
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', function () {
            form.querySelectorAll(".error").forEach(el => el.textContent = '');
            form.querySelectorAll(".required-star").forEach(el => el.style.display = 'none');
            form.reset();
            profileImagePreview.src = "{{ default_file_url() }}";
        });
    }
});

// Delete patient functionality
document.querySelectorAll('.delete-patient').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();

        const deleteUrl = this.dataset.url;
        const patientName = this.dataset.name;

        Swal.fire({
            title: '{{ __("messages.are_you_sure") }}',
            html: `{{ __("messages.delete_confirm") }} <br><strong>${patientName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("messages.yes_delete") }}',
            cancelButtonText: '{{ __("messages.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        Swal.fire({
                            title: '{{ __("messages.deleted") }}',
                            text: '{{ __("messages.delete_success") }}',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: '{{ __("messages.error") }}',
                            text: data.message ?? '{{ __("messages.delete_error") }}',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire({
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.delete_error") }}',
                        icon: 'error'
                    });
                });
            }
        });
    });
});

// Initialize Flatpickr and phone inputs
function initializePhoneInputs() {
    const phoneInputs = document.querySelectorAll('.phone-input, .intl-tel-input');
    phoneInputs.forEach(function(input) {
        if (input.getAttribute('data-initialized') === 'true') {
            return;
        }

        const iti = intlTelInput(input, {
            initialCountry: "gb",
            preferredCountries: ["gb", "us", "in", "au", "ca"],
            separateDialCode: true,
            autoPlaceholder: "aggressive",
            nationalMode: false,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.16/build/js/utils.js"
        });

        input.setAttribute('data-initialized', 'true');

        try {
            const existing = (input.value || '').trim();
            if (existing) {
                iti.setNumber(existing);
            }
        } catch (e) {}
    });
}

// Initialize Flatpickr for DOB fields
const dobInput = document.getElementById('dob');
if (dobInput) {
    flatpickr(dobInput, {
        dateFormat: "Y-m-d",
        maxDate: "today",
        allowInput: false,
        clickOpens: true,
        placeholder: "Select date of birth"
    });
}

document.querySelectorAll('.flatpickr-dob').forEach(function(input) {
    if (input.id !== 'dob') {
        flatpickr(input, {
            dateFormat: "Y-m-d",
            maxDate: "today",
            allowInput: false,
            clickOpens: true,
            placeholder: "Select date of birth"
        });
    }
});

initializePhoneInputs();

document.addEventListener('shown.bs.modal', function () {
    setTimeout(initializePhoneInputs, 100);
});
</script>
@endpush