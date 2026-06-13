@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="ph ph-test-tube me-2"></i>
                        🩸 {{ __($module_title) }}
                    </h4>
                    <a href="{{ route('backend.blood-tests.index') }}" class="btn btn-secondary">
                        <i class="ph ph-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                
                <form action="{{ route('backend.blood-tests.update', $appointment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Patient Information (Read-only) -->
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="ph ph-info me-2"></i>
                                    <strong>Patient Information:</strong> This blood test was booked via WordPress and synced automatically.
                                </div>
                            </div>
                            
                            <!-- Patient Name -->
                            <div class="col-md-6">
                                <label class="form-label">Patient Name</label>
                                <input type="text" class="form-control" value="{{ $appointment->user ? $appointment->user->full_name : 'N/A' }}" readonly>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" value="{{ $appointment->email ?? 'N/A' }}" readonly>
                            </div>
                            
                            <!-- Phone -->
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" value="{{ $appointment->phone ?? 'N/A' }}" readonly>
                            </div>
                            
                            <!-- Test Type -->
                            <div class="col-md-6">
                                <label class="form-label">Test Type <span class="text-danger">*</span></label>
                                <input type="text" name="test_type" class="form-control" value="{{ old('test_type', $appointment->test_type) }}" required>
                            </div>
                            
                            <!-- Appointment Date -->
                            <div class="col-md-6">
                                <label class="form-label">Appointment Date <span class="text-danger">*</span></label>
                                <input type="date" name="appointment_date" class="form-control" 
                                       value="{{ old('appointment_date', $appointment->appointment_date ? date('Y-m-d', strtotime($appointment->appointment_date)) : '') }}" required>
                            </div>
                            
                            <!-- Appointment Time -->
                            <div class="col-md-6">
                                <label class="form-label">Appointment Time <span class="text-danger">*</span></label>
                                <input type="time" name="appointment_time" class="form-control" 
                                       value="{{ old('appointment_time', $appointment->appointment_time) }}" required>
                            </div>
                            
                            <!-- Total Amount -->
                            <div class="col-md-6">
                                <label class="form-label">Total Amount (£) <span class="text-danger">*</span></label>
                                <input type="number" name="total_amount" class="form-control" step="0.01" 
                                       value="{{ old('total_amount', $appointment->total_amount) }}" required>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="pending" {{ old('status', $appointment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="check_in" {{ old('status', $appointment->status) == 'check_in' ? 'selected' : '' }}>Check In</option>
                                    <option value="check_out" {{ old('status', $appointment->status) == 'check_out' ? 'selected' : '' }}>Check Out</option>
                                    <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            
                            <!-- Notes -->
                            <div class="col-12">
                                <label class="form-label">Additional Notes</label>
                                <textarea name="appointment_extra_info" class="form-control" rows="4">{{ old('appointment_extra_info', $appointment->appointment_extra_info) }}</textarea>
                            </div>
                            
                            <!-- Optional: Assign Doctor -->
                            <div class="col-md-6">
                                <label class="form-label">Assign Doctor (Optional)</label>
                                <select name="doctor_id" class="form-select">
                                    <option value="">-- No Doctor --</option>
                                    @foreach(\App\Models\User::where('user_type', 'doctor')->get() as $doctor)
                                        <option value="{{ $doctor->id }}" {{ old('doctor_id', $appointment->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Optional: Assign Clinic -->
                            <div class="col-md-6">
                                <label class="form-label">Assign Clinic (Optional)</label>
                                <select name="clinic_id" class="form-select">
                                    <option value="">-- No Clinic --</option>
                                    @foreach(\Modules\Clinic\Models\Clinics::all() as $clinic)
                                        <option value="{{ $clinic->id }}" {{ old('clinic_id', $appointment->clinic_id) == $clinic->id ? 'selected' : '' }}>
                                            {{ $clinic->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('backend.blood-tests.index') }}" class="btn btn-secondary">
                            <i class="ph ph-x me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-check me-1"></i> Update Blood Test
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Report Upload Section -->
            <div class="card mt-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="ph ph-file-pdf me-2"></i>
                        Blood Test Report
                    </h5>
                </div>
                
                <div class="card-body">
                    <!-- Existing Report -->
                    @if($appointment->report_file)
                        <div class="alert alert-success mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="ph ph-check-circle me-2"></i>
                                    <strong>Report Uploaded:</strong> {{ basename($appointment->report_file) }}
                                    <br>
                                    <small class="text-muted">Uploaded: {{ $appointment->report_uploaded_at ? $appointment->report_uploaded_at->format('M d, Y h:i A') : 'N/A' }}</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ asset('storage/' . $appointment->report_file) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="ph ph-eye"></i> View
                                    </a>
                                    <a href="{{ asset('storage/' . $appointment->report_file) }}" download class="btn btn-sm btn-secondary">
                                        <i class="ph ph-download"></i> Download
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteReport()">
                                        <i class="ph ph-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                            
                            @if($appointment->report_notes)
                                <div class="mt-3 pt-3 border-top">
                                    <strong>Notes:</strong>
                                    <p class="mb-0">{{ $appointment->report_notes }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="ph ph-warning me-2"></i>
                            No report uploaded yet
                        </div>
                    @endif
                    
                    <!-- Upload Form -->
                    <form action="{{ route('backend.blood-tests.upload_report', $appointment->id) }}" method="POST" enctype="multipart/form-data" id="reportUploadForm">
                        @csrf
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">
                                    Upload Report 
                                    @if(!$appointment->report_file)
                                        <span class="text-danger">*</span>
                                    @else
                                        <span class="text-muted">(Optional - leave empty to keep current file)</span>
                                    @endif
                                </label>
                                <input type="file" name="report" id="reportFile" class="form-control" accept=".pdf,.jpg,.jpeg,.png" {{ !$appointment->report_file ? 'required' : '' }}>
                                <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                                <div id="fileInfo" class="mt-2 text-muted small"></div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Report Status <span class="text-danger">*</span></label>
                                <select name="report_status" class="form-select" required>
                                    <option value="pending" {{ $appointment->report_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="ready" {{ $appointment->report_status == 'ready' ? 'selected' : '' }}>Ready</option>
                                </select>
                                <small class="text-muted">Set to "Ready" to notify patient</small>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Report Notes (Optional)</label>
                                <textarea name="report_notes" class="form-control" rows="3" placeholder="Add any notes about the report...">{{ $appointment->report_notes }}</textarea>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="ph ph-upload me-1"></i> 
                                    @if($appointment->report_file)
                                        Update Report
                                    @else
                                        Upload Report
                                    @endif
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
<script>
    // File input change handler
    document.getElementById('reportFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const fileInfo = document.getElementById('fileInfo');
        
        if (file) {
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            fileInfo.innerHTML = `Selected: <strong>${file.name}</strong> (${sizeMB} MB)`;
            fileInfo.classList.remove('text-danger');
            fileInfo.classList.add('text-success');
            
            if (file.size > 10 * 1024 * 1024) {
                fileInfo.innerHTML += ' - <span class="text-danger">File too large! Max 10MB</span>';
            }
        } else {
            fileInfo.innerHTML = '';
        }
    });
    
    // Form submit handler
    document.getElementById('reportUploadForm').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('reportFile');
        const file = fileInput.files[0];
        
        console.log('Form submitting...', {
            hasFile: !!file,
            fileName: file ? file.name : 'none',
            fileSize: file ? file.size : 0
        });
    });
    
    function deleteReport() {
        if (!confirm('Are you sure you want to delete this report?')) {
            return;
        }
        
        fetch('{{ route("backend.blood-tests.delete_report", $appointment->id) }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to delete report');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete report');
        });
    }
</script>
@endpush

@endsection