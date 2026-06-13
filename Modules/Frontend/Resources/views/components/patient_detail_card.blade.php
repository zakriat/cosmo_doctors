{{-- Patient Detail Card Component - Adapted from patient_detail_enhanced.blade.php --}}
@push('after-styles')
<style>
/* Patient Detail Card Styles */
.patient-detail-card .nav-link {
    border-radius: 50px;
    padding: 12px 20px;
    margin-right: 8px;
    border: 1px solid transparent;
    transition: all 0.3s ease;
}

.patient-detail-card .nav-link:hover {
    background-color: var(--bs-primary-bg-subtle);
    color: var(--bs-primary);
}

.patient-detail-card .nav-link.active {
    background-color: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

.patient-detail-card .nav-link i {
    margin-right: 8px;
    font-size: 1.1em;
}

.patient-detail-card .timeline {
    position: relative;
}

.patient-detail-card .timeline .card {
    position: relative;
    margin-bottom: 1rem;
}

.patient-detail-card .card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

@media (max-width: 768px) {
    .patient-detail-card .nav-pills {
        flex-wrap: wrap;
    }
    
    .patient-detail-card .nav-link {
        margin-bottom: 8px;
        font-size: 14px;
        padding: 10px 16px;
    }
}
</style>
@endpush

<div class="patient-detail-card">
    <!-- Enhanced Tab Navigation -->
    <ul class="nav nav-pills mb-4" id="patient-detail-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="patient-overview-tab" data-bs-toggle="pill" data-bs-target="#patient-overview" type="button" role="tab" aria-controls="patient-overview" aria-selected="true">
                <i class="ph ph-house"></i>
                <span>{{ __('frontend.overview') }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="patient-triage-tab" data-bs-toggle="pill" data-bs-target="#patient-triage" type="button" role="tab" aria-controls="patient-triage" aria-selected="false">
                <i class="ph ph-stethoscope"></i>
                <span>{{ __('frontend.triage') }}</span>
            </button>
        </li>
        <li