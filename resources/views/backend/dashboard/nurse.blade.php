@extends('backend.layouts.app', ['isBanner' => false])

@section('title') {{ __('messages.dashboard') }} @endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('messages.dashboard') }} - Nurse Panel</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <img src="{{ $current_user->profile_image }}" alt="Profile" class="avatar-80 rounded-circle">
                        </div>
                        <div>
                            <h5 class="mb-1">Welcome back, {{ $data['display_name'] }}! 👩‍⚕️</h5>
                            <p class="mb-0 text-muted">
                                Specialization: {{ $data['nurse_specialization'] }} | 
                                Clinic: {{ $data['clinic_name'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon iq-icon-box-2 bg-primary-subtle rounded me-3">
                            <i class="fas fa-calendar-check text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Total Appointments</p>
                            <h4 class="mb-0">{{ $data['total_appointments'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon iq-icon-box-2 bg-success-subtle rounded me-3">
                            <i class="fas fa-users text-success" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Total Patients</p>
                            <h4 class="mb-0">{{ $data['total_patient'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon iq-icon-box-2 bg-warning-subtle rounded me-3">
                            <i class="fas fa-user-md text-warning" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Assigned Doctors</p>
                            <h4 class="mb-0">{{ $data['total_assign_doctor'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon iq-icon-box-2 bg-info-subtle rounded me-3">
                            <i class="fas fa-pound-sign text-info" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Total Earnings</p>
                            <h4 class="mb-0">{{ setting('currency_symbol', '£') }}{{ number_format($data['total_earning'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nurse-Specific Features -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">🩺 Nursing Tasks</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <div>
                                <i class="fas fa-heartbeat text-danger me-2"></i>
                                Patient Vitals
                            </div>
                            <span class="badge bg-primary rounded-pill">View</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <div>
                                <i class="fas fa-pills text-success me-2"></i>
                                Medication Administration
                            </div>
                            <span class="badge bg-primary rounded-pill">Manage</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <div>
                                <i class="fas fa-clipboard-list text-info me-2"></i>
                                Nursing Notes
                            </div>
                            <span class="badge bg-primary rounded-pill">Add</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <div>
                                <i class="fas fa-user-check text-warning me-2"></i>
                                Patient Assessments
                            </div>
                            <span class="badge bg-primary rounded-pill">Create</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">📋 Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="#" class="btn btn-outline-primary w-100">
                                <i class="fas fa-calendar-plus mb-2 d-block"></i>
                                View Appointments
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-outline-success w-100">
                                <i class="fas fa-user-plus mb-2 d-block"></i>
                                Add Patient
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-outline-info w-100">
                                <i class="fas fa-notes-medical mb-2 d-block"></i>
                                Patient Records
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-outline-warning w-100">
                                <i class="fas fa-chart-line mb-2 d-block"></i>
                                Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">📊 Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No recent activity</h6>
                        <p class="text-muted">Your nursing activities will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-styles')
<style>
.icon.iq-icon-box-2 {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
    border-radius: 8px;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.btn i {
    font-size: 1.5rem;
}
</style>
@endpush