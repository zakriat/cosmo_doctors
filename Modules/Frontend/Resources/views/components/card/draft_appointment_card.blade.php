<li class="appointments-card section-bg rounded p-5 border-warning" style="border: 2px dashed #ffc107;">
    <!-- Draft Badge -->
    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-warning text-dark px-3 py-2">
                <i class="ph ph-clock me-1"></i> {{ __('frontend.draft') }}
            </span>
            <span class="badge bg-info text-white px-3 py-2">
                {{ $draft->progress_percentage }}% {{ __('frontend.complete') }}
            </span>
        </div>
        <small class="text-muted">
            <i class="ph ph-calendar-blank me-1"></i>
            {{ __('frontend.expires_in') }}: {{ $draft->days_until_expiration }} {{ __('frontend.days') }}
        </small>
    </div>

    <!-- Progress Bar -->
    <div class="progress mb-3" style="height: 6px;">
        <div class="progress-bar bg-warning" role="progressbar" 
             style="width: {{ $draft->progress_percentage }}%;" 
             aria-valuenow="{{ $draft->progress_percentage }}" 
             aria-valuemin="0" 
             aria-valuemax="100">
        </div>
    </div>

    <!-- Service Name -->
    <div class="mb-3">
        <h5 class="mb-1">
            @if($draft->service)
                {{ $draft->service->name }}
            @else
                {{ __('frontend.incomplete_booking') }}
            @endif
        </h5>
        <p class="text-muted mb-0 small">
            <i class="ph ph-info me-1"></i>
            {{ __('frontend.stopped_at') }}: <strong>{{ $draft->step_name }}</strong>
        </p>
    </div>

    <!-- Booking Details -->
    <div class="appointments-card-content border-top border-bottom py-3">
        <div class="row gy-3">
            <!-- Category -->
            @if($draft->category)
            <div class="col-lg-4 col-md-6 col-12">
                <div class="d-flex flex-column gap-1">
                    <p class="mb-0 text-muted small">{{ __('frontend.category') }}</p>
                    <h6 class="mb-0">{{ $draft->category->name }}</h6>
                </div>
            </div>
            @endif

            <!-- Clinic -->
            @if($draft->clinic)
            <div class="col-lg-4 col-md-6 col-12">
                <div class="d-flex flex-column gap-1">
                    <p class="mb-0 text-muted small">{{ __('frontend.clinic_name') }}</p>
                    <h6 class="mb-0">{{ $draft->clinic->name }}</h6>
                </div>
            </div>
            @endif

            <!-- Doctor -->
            @if($draft->doctor)
            <div class="col-lg-4 col-md-6 col-12">
                <div class="d-flex flex-column gap-1">
                    <p class="mb-0 text-muted small">{{ __('frontend.doctor_name') }}</p>
                    <h6 class="mb-0">Dr. {{ $draft->doctor->first_name }} {{ $draft->doctor->last_name }}</h6>
                </div>
            </div>
            @endif

            <!-- Date & Time -->
            @if($draft->appointment_date)
            <div class="col-lg-4 col-md-6 col-12">
                <div class="d-flex flex-column gap-1">
                    <p class="mb-0 text-muted small">{{ __('frontend.date_time') }}</p>
                    <h6 class="mb-0">
                        {{ DateFormate($draft->appointment_date) }}
                        @if($draft->appointment_time)
                            {{ \Carbon\Carbon::parse($draft->appointment_time)->format(setting('time_formate') ?? 'h:i A') }}
                        @endif
                    </h6>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mt-4">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('booking', ['id' => $draft->service_id]) }}?resume={{ $draft->id }}" 
               class="btn btn-primary">
                <i class="ph ph-play me-2"></i>
                {{ __('frontend.continue_booking') }}
            </a>
            <button type="button" 
                    class="btn btn-outline-danger delete-draft-btn" 
                    data-draft-id="{{ $draft->id }}">
                <i class="ph ph-trash me-2"></i>
                {{ __('frontend.delete_draft') }}
            </button>
        </div>
        <small class="text-muted">
            {{ __('frontend.last_updated') }}: {{ $draft->updated_at->diffForHumans() }}
        </small>
    </div>
</li>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete draft button
    document.querySelectorAll('.delete-draft-btn').forEach(button => {
        button.addEventListener('click', function() {
            const draftId = this.getAttribute('data-draft-id');
            
            Swal.fire({
                title: '{{ __("frontend.are_you_sure") }}',
                text: '{{ __("frontend.delete_draft_confirmation") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("frontend.yes_delete") }}',
                cancelButtonText: '{{ __("frontend.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteDraft(draftId);
                }
            });
        });
    });
});

function deleteDraft(draftId) {
    fetch(`/api/draft-appointments/${draftId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '{{ __("frontend.deleted") }}',
                text: '{{ __("frontend.draft_deleted_successfully") }}',
                icon: 'success',
                confirmButtonColor: '#28a745'
            }).then(() => {
                // Reload the page to refresh the list
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: '{{ __("frontend.error") }}',
                text: data.message || '{{ __("frontend.failed_to_delete_draft") }}',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        console.error('Error deleting draft:', error);
        Swal.fire({
            title: '{{ __("frontend.error") }}',
            text: '{{ __("frontend.failed_to_delete_draft") }}',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    });
}
</script>
@endpush
