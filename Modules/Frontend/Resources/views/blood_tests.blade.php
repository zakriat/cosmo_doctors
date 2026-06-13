@extends('frontend::layouts.patient_layout')

@section('title', 'Blood Tests')

@section('content')
<div class="container-fluid content-inner pb-0">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="ph ph-test-tube me-2"></i>
                        Blood Tests
                    </h4>
                </div>
                
                <div class="card-body">
                    @if($bloodTests->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="ph ph-info me-2"></i>
                            No blood tests found
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($bloodTests as $test)
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <h6 class="mb-1">{{ $test->test_type ?? 'Blood Test' }}</h6>
                                                    <small class="text-muted">
                                                        <i class="ph ph-calendar me-1"></i>
                                                        {{ \Carbon\Carbon::parse($test->appointment_date)->format('M d, Y') }}
                                                    </small>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="ph ph-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($test->appointment_time)->format('h:i A') }}
                                                    </small>
                                                </div>
                                                
                                                <div class="col-md-2">
                                                    <small class="text-muted d-block mb-1">Status</small>
                                                    @if($test->status == 'pending')
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                    @elseif($test->status == 'confirmed')
                                                        <span class="badge bg-info">Confirmed</span>
                                                    @elseif($test->status == 'check_in')
                                                        <span class="badge bg-primary">Checked In</span>
                                                    @elseif($test->status == 'check_out')
                                                        <span class="badge bg-success">Completed</span>
                                                    @elseif($test->status == 'cancelled')
                                                        <span class="badge bg-danger">Cancelled</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <small class="text-muted d-block mb-1">Report Status</small>
                                                    @if($test->report_file && $test->report_status == 'ready')
                                                        <span class="badge bg-success">
                                                            <i class="ph ph-check-circle me-1"></i>
                                                            Report Ready
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="ph ph-clock me-1"></i>
                                                            Report Pending
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-md-4 text-end">
                                                    @if($test->report_file && $test->report_status == 'ready')
                                                        <a href="{{ asset('storage/' . $test->report_file) }}" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-primary me-2">
                                                            <i class="ph ph-eye me-1"></i>
                                                            View Report
                                                        </a>
                                                        <a href="{{ route('patient.blood-tests.download', $test->id) }}" 
                                                           class="btn btn-sm btn-secondary">
                                                            <i class="ph ph-download me-1"></i>
                                                            Download
                                                        </a>
                                                    @else
                                                        <span class="text-muted">No report available</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
