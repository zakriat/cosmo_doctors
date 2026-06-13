@extends('frontend::layouts.patient_layout')

@section('title')
    {{ __('frontend.patient_dashboard') }}
@endsection

@push('after-styles')
<style>
/* Patient Dashboard Specific Styles */
.patient-dashboard-container {
    background-color: #f8f9fa;
    min-height: 100vh;
}

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

.welcome-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.stats-card {
    background: white;
    border-radius: 10px;
    padding: 1.25rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-icon {
    width: 48px;
    height: 48px;
    min-width: 48px; /* prevents shrinking */
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

@media (max-width: 576px) {
    .stats-card {
        padding: 1rem 0.875rem;
    }

    .stats-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        font-size: 1rem;
    }

    .stats-card h3 {
        font-size: 1.2rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid patient-main-content">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2" style="color: white;">{{ __('frontend.welcome_back') }}, {{ auth()->user()->first_name }}!</h2>
                <p class="mb-0 opacity-100">{{ __('frontend.dashboard_welcome_message') }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-flex gap-2 justify-content-md-end justify-content-center mt-3 mt-md-0 flex-wrap">
                    <a href="{{ route('services') }}" class="btn btn-light">
                        <i class="ph ph-calendar-plus me-2"></i>{{ __('frontend.book_appointment') }}
                    </a>
                    <a href="https://www.cosmodoctors.com/booking/?patient_id={{ auth()->id() }}&first={{ urlencode(auth()->user()->first_name) }}&last={{ urlencode(auth()->user()->last_name) }}&email={{ urlencode(auth()->user()->email) }}&phone={{ urlencode(auth()->user()->phone ?? '') }}" 
                       target="_blank" 
                       class="btn btn-danger">
                        <i class="ph ph-test-tube me-2"></i>🩸 Book Blood Test
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Stats -->
  <div class="row mb-4 g-3">
    <div class="col-6 col-xl-3">
        <div class="stats-card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="stats-icon bg-primary-subtle text-primary flex-shrink-0">
                    <i class="ph ph-calendar-check"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="mb-0 text-truncate" id="total-appointments">{{ $dashboardStats['total_appointments'] ?? 0 }}</h3>
                    <p class="text-muted mb-0 small text-truncate">{{ __('frontend.total_appointments') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stats-card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="stats-icon bg-warning-subtle text-warning flex-shrink-0">
                    <i class="ph ph-clock"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="mb-0 text-truncate" id="upcoming-appointments">{{ $dashboardStats['upcoming_appointments'] ?? 0 }}</h3>
                    <p class="text-muted mb-0 small text-truncate">{{ __('frontend.upcoming_appointments') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stats-card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="stats-icon bg-success-subtle text-success flex-shrink-0">
                    <i class="ph ph-pill"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="mb-0 text-truncate" id="total-prescriptions">{{ $dashboardStats['total_prescriptions'] ?? 0 }}</h3>
                    <p class="text-muted mb-0 small text-truncate">{{ __('frontend.prescriptions') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stats-card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="stats-icon bg-info-subtle text-info flex-shrink-0">
                    <i class="ph ph-heart"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="mb-0 fs-6 text-truncate">
                        {{ $dashboardStats['last_visit']
                            ? \Carbon\Carbon::parse($dashboardStats['last_visit'])->diffForHumans()
                            : __('frontend.never') }}
                    </h3>
                    <p class="text-muted mb-0 small text-truncate">{{ __('frontend.last_visit') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Patient Information Tabs -->
    <ul class="nav nav-pills mb-4 patient-overview-tab" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                <i class="ph ph-house"></i>
                <span>{{ __('frontend.overview') }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="triage-tab" data-bs-toggle="pill" data-bs-target="#triage" type="button" role="tab" aria-controls="triage" aria-selected="false">
                <i class="ph ph-stethoscope"></i>
                <span>{{ __('frontend.triage') }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="prescriptions-tab" data-bs-toggle="pill" data-bs-target="#prescriptions" type="button" role="tab" aria-controls="prescriptions" aria-selected="false">
                <i class="ph ph-pill"></i>
                <span>{{ __('frontend.prescriptions') }}</span>
            </button>
        </li>

         <li class="nav-item" role="presentation">
            <button class="nav-link" id="medical-records-tab" data-bs-toggle="pill" data-bs-target="#medical-records" type="button" role="tab" aria-controls="medical-records" aria-selected="false">
                <i class="ph ph-folder-open"></i>
                <span>{{ __('frontend.medical_records') }}</span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="appointments-tab" data-bs-toggle="pill" data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="false">
                <i class="ph ph-calendar-dots"></i>
                <span>{{ __('frontend.appointments') }}</span>
            </button>
        </li>
       
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab" tabindex="0">
            <div class="card">
                <div class="card-body">
                    <!-- Patient Basic Info -->
                    <div class="mb-4">
                        <h4 class="mb-3">{{ __('frontend.my_information') }}</h4>
                        <div class="d-flex gap-3 align-items-center p-4 bg-body rounded flex-md-nowrap flex-wrap">
                            <div>
                                <img src="{{ auth()->user()->profile_image ?? default_user_avatar() }}" alt="Profile Image"
                                        class="avatar avatar-80 rounded-pill">
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="m-0">{{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}</h4>
                                <div class="d-flex align-items-center column-gap-3 row-gap-2 mt-3 flex-md-nowrap flex-wrap">
                                    <div class="d-flex align-items-center gap-2 text-break">
                                        <i class="ph ph-envelope-simple text-heading"></i>
                                        <span class="text-secondary font-size-16">{{ auth()->user()->email }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ph ph-phone text-heading"></i>
                                        <span class="text-primary font-size-16">{{ auth()->user()->mobile ?? __('frontend.not_provided') }}</span>
                                    </div>
                                    @if(auth()->user()->date_of_birth)
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ph ph-cake text-heading"></i>
                                        <span class="font-size-16">{{ \Carbon\Carbon::parse(auth()->user()->date_of_birth)->format('d-m-Y') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity Section -->
                    <div class="row">
                        <!-- Recent Triage Records -->
                        <div class="col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ __('frontend.recent_triage') }}</h5>
                                    <button class="btn btn-sm btn-outline-primary" onclick="switchToTab('triage')">{{ __('frontend.view_all') }}</button>
                                </div>
                                <div class="card-body" id="recent-triage-content">
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">{{ __('frontend.loading') }}...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Prescriptions -->
                        <div class="col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ __('frontend.recent_prescriptions') }}</h5>
                                    <button class="btn btn-sm btn-outline-primary" onclick="switchToTab('prescriptions')">{{ __('frontend.view_all') }}</button>
                                </div>
                                <div class="card-body" id="recent-prescriptions-content">
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">{{ __('frontend.loading') }}...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Appointments -->
                        <div class="col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ __('frontend.upcoming_appointments') }}</h5>
                                    <button class="btn btn-sm btn-outline-primary" onclick="switchToTab('appointments')">{{ __('frontend.view_all') }}</button>
                                </div>
                                <div class="card-body" id="upcoming-appointments-content">
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">{{ __('frontend.loading') }}...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-4">
                        <h5 class="mb-3">{{ __('frontend.quick_actions') }}</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('services') }}" class="btn btn-primary">
                                <i class="ph ph-calendar-plus me-2"></i>{{ __('frontend.book_appointment') }}
                            </a>
                            <button class="btn btn-outline-primary" onclick="switchToTab('medical-records')">
                                <i class="ph ph-folder-open me-2"></i>{{ __('frontend.view_records') }}
                            </button>
                            <a href="{{ route('edit-profile') }}" class="btn btn-outline-secondary">
                                <i class="ph ph-user-gear me-2"></i>{{ __('frontend.edit_profile') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Triage Tab -->
        <div class="tab-pane fade" id="triage" role="tabpanel" aria-labelledby="triage-tab" tabindex="0">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">{{ __('frontend.my_triage_records') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 justify-content-md-end">
                                <input type="text" class="form-control form-control-sm" id="triage-search" placeholder="{{ __('frontend.search_triage') }}...">
                                <button class="btn btn-sm btn-outline-primary" id="triage-filter-btn">
                                    <i class="ph ph-funnel"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="triage-content">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">{{ __('frontend.loading') }}...</span>
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
                            <h5 class="mb-0">{{ __('frontend.my_prescriptions') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 justify-content-md-end">
                                <input type="text" class="form-control form-control-sm" id="prescriptions-search" placeholder="{{ __('frontend.search_prescriptions') }}...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="prescriptions-content">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">{{ __('frontend.loading') }}...</span>
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
                            <h5 class="mb-0">{{ __('frontend.my_appointments') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 justify-content-md-end">
                                <input type="text" class="form-control form-control-sm" id="appointments-search" placeholder="{{ __('frontend.search_appointments') }}...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="appointments-content">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">{{ __('frontend.loading') }}...</span>
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
                    <h5 class="mb-0">{{ __('frontend.medical_records') }}</h5>
                </div>
                <div class="card-body">
                    <div id="medical-records-content">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">{{ __('frontend.loading') }}...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
// Tab switching function
function switchToTab(tabName) {
    const tabButton = document.getElementById(tabName + '-tab');
    if (tabButton) {
        tabButton.click();
    }
}

// Load dashboard data when tabs are activated
document.addEventListener('DOMContentLoaded', function() {
    // Load initial overview data
    loadRecentData();
    
    // Add event listeners for tab switches
    document.querySelectorAll('[data-bs-toggle="pill"]').forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(event) {
            const targetTab = event.target.getAttribute('data-bs-target');
            
            switch(targetTab) {
                case '#triage':
                    loadTriageRecords();
                    break;
                case '#prescriptions':
                    loadPrescriptions();
                    break;
                case '#appointments':
                    loadAppointments();
                    break;
                case '#medical-records':
                    loadMedicalRecords();
                    break;
            }
        });
    });
});

// Load recent data for overview tab
function loadRecentData() {
    // Load recent triage records
    fetch('{{ route("patient.dashboard.triage") }}?recent=true')
        .then(response => response.json())
        .then(data => {
            document.getElementById('recent-triage-content').innerHTML = data.html || '<p class="text-muted text-center">{{ __("frontend.no_recent_triage") }}</p>';
        })
        .catch(error => {
            document.getElementById('recent-triage-content').innerHTML = '<p class="text-danger text-center">{{ __("frontend.error_loading_data") }}</p>';
        });
    
    // Load recent prescriptions
    fetch('{{ route("patient.dashboard.prescriptions") }}?recent=true')
        .then(response => response.json())
        .then(data => {
            document.getElementById('recent-prescriptions-content').innerHTML = data.html || '<p class="text-muted text-center">{{ __("frontend.no_recent_prescriptions") }}</p>';
        })
        .catch(error => {
            document.getElementById('recent-prescriptions-content').innerHTML = '<p class="text-danger text-center">{{ __("frontend.error_loading_data") }}</p>';
        });
    
    // Load upcoming appointments
    fetch('{{ route("patient.dashboard.appointments") }}?upcoming=true')
        .then(response => response.json())
        .then(data => {
            document.getElementById('upcoming-appointments-content').innerHTML = data.html || '<p class="text-muted text-center">{{ __("frontend.no_upcoming_appointments") }}</p>';
        })
        .catch(error => {
            document.getElementById('upcoming-appointments-content').innerHTML = '<p class="text-danger text-center">{{ __("frontend.error_loading_data") }}</p>';
        });
}

// Load full triage records
function loadTriageRecords() {
    const content = document.getElementById('triage-content');
    if (content.dataset.loaded) return;
    
    fetch('{{ route("patient.dashboard.triage") }}')
        .then(response => response.json())
        .then(data => {
            content.innerHTML = data.html || '<p class="text-muted text-center">{{ __("frontend.no_triage_records") }}</p>';
            content.dataset.loaded = 'true';
        })
        .catch(error => {
            content.innerHTML = '<p class="text-danger text-center">{{ __("frontend.error_loading_data") }}</p>';
        });
}

// Load prescriptions
function loadPrescriptions() {
    const content = document.getElementById('prescriptions-content');
    if (content.dataset.loaded) return;
    
    fetch('{{ route("patient.dashboard.prescriptions") }}')
        .then(response => response.json())
        .then(data => {
            content.innerHTML = data.html || '<p class="text-muted text-center">{{ __("frontend.no_prescriptions") }}</p>';
            content.dataset.loaded = 'true';
        })
        .catch(error => {
            content.innerHTML = '<p class="text-danger text-center">{{ __("frontend.error_loading_data") }}</p>';
        });
}

// Load appointments
function loadAppointments() {
    const content = document.getElementById('appointments-content');
    if (content.dataset.loaded) return;
    
    fetch('{{ route("patient.dashboard.appointments") }}')
        .then(response => response.json())
        .then(data => {
            content.innerHTML = data.html || '<p class="text-muted text-center">{{ __("frontend.no_appointments") }}</p>';
            content.dataset.loaded = 'true';
        })
        .catch(error => {
            content.innerHTML = '<p class="text-danger text-center">{{ __("frontend.error_loading_data") }}</p>';
        });
}

// Load medical records
function loadMedicalRecords() {
    const content = document.getElementById('medical-records-content');
    if (content.dataset.loaded) return;
    
    fetch('{{ route("patient.dashboard.medical-records") }}')
        .then(response => response.json())
        .then(data => {
            content.innerHTML = data.html || '<p class="text-muted text-center">{{ __("frontend.no_medical_records") }}</p>';
            content.dataset.loaded = 'true';
        })
        .catch(error => {
            content.innerHTML = '<p class="text-danger text-center">{{ __("frontend.error_loading_data") }}</p>';
        });
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Triage search
    const triageSearch = document.getElementById('triage-search');
    if (triageSearch) {
        triageSearch.addEventListener('input', function() {
            // Implement search functionality
            console.log('Searching triage:', this.value);
        });
    }
    
    // Prescriptions search
    const prescriptionsSearch = document.getElementById('prescriptions-search');
    if (prescriptionsSearch) {
        prescriptionsSearch.addEventListener('input', function() {
            // Implement search functionality
            console.log('Searching prescriptions:', this.value);
        });
    }
    
    // Appointments search
    const appointmentsSearch = document.getElementById('appointments-search');
    if (appointmentsSearch) {
        appointmentsSearch.addEventListener('input', function() {
            // Implement search functionality
            console.log('Searching appointments:', this.value);
        });
    }
});
</script>
@endpush