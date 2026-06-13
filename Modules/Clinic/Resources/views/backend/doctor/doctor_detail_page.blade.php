@extends('backend.layouts.app')

@section('title')
    {{ __('clinic.doctor_details') }}
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
    <h4>{{ __('clinic.doctor_details') }} - {{ __('messages.dr') }}. {{ $data['full_name'] ?? __('messages.not_available') }}</h4>
    <a href="{{ route('backend.appointments.index') }}" class="btn btn-primary">
        <i class="ph ph-arrow-left me-1"></i>{{ __('messages.back') }}
    </a>
</div>

<div class="row">
    <!-- Doctor Profile Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <img src="{{ !empty($data['profile_image']) ? $data['profile_image'] : asset('img/avatar/avatar.webp') }}"
                         alt="{{ __('messages.profile_image') }}" class="img-fluid avatar avatar-120 avatar-rounded mb-3">
                    <h4 class="mb-2">{{ __('messages.dr') }}. {{ $data['full_name'] ?? __('messages.not_available') }}</h4>
                    <p class="text-muted mb-0">{{ $data['specialization'] ?? __('clinic.general_practitioner') }}</p>
                </div>

                <!-- Contact Information -->
                <div class="mb-4">
                    <h6 class="mb-3">{{ __('clinic.contact_information') }}</h6>    
                    <div class="d-flex align-items-center mb-2">
                        <i class="ph ph-envelope me-2 text-primary"></i>
                        <a href="mailto:{{ $data['email'] ?? '' }}" class="text-decoration-none">
                            {{ $data['email'] ?? __('messages.not_available') }}
                        </a>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="ph ph-phone me-2 text-primary"></i>
                        <a href="tel:{{ $data['mobile'] ?? '' }}" class="text-decoration-none">
                            {{ $data['mobile'] ?? __('messages.not_available') }}
                        </a>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="ph ph-map-pin me-2 text-primary"></i>
                        <span>{{ $data['address'] ?? __('messages.not_available') }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="ph ph-user me-2 text-primary"></i>
                        <span>{{ $data['gender'] ? __('messages.' . strtolower($data['gender'])) : __('messages.not_available') }}</span>
                    </div>
                </div>

                <!-- About Section -->
                @if(!empty($data['about']))
                <div class="mb-4">
                    <h6 class="mb-3">{{ __('clinic.about') }}</h6>
                    <p class="text-muted">{{ $data['about'] }}</p>
                </div>
                @endif

                <!-- Statistics -->
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card bg-primary-subtle">
                            <div class="card-body text-center p-3">
                                <h5 class="mb-1 text-primary">{{ $data['appointment'] ?? 0 }}</h5>
                                <small class="text-muted">{{ __('clinic.total_appointments') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-success-subtle">
                            <div class="card-body text-center p-3">
                                <h5 class="mb-1 text-success">{{ $data['total_sessions'] ?? 0 }}</h5>
                                <small class="text-muted">{{ __('clinic.sessions') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-info-subtle">
                            <div class="card-body text-center p-3">
                                <h5 class="mb-1 text-info">{{ $data['experience'] ?? 0 }}</h5>
                                <small class="text-muted">{{ __('clinic.experience_years') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-warning-subtle">
                            <div class="card-body text-center p-3">
                                <h5 class="mb-1 text-warning">{{ $data['services']->count() ?? 0 }}</h5>
                                <small class="text-muted">{{ __('service.services') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Doctor Services and Details -->
    <div class="col-lg-8">
        <!-- Services Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('clinic.services_offered') }}</h5>
            </div>
            <div class="card-body">
                @if(isset($data['services']) && $data['services']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('clinic.service_name') }}</th>
                                    <th>{{ __('appointment.price') }}</th>
                                    <th>{{ __('clinic.clinic') }}</th>
                                    <th>{{ __('clinic.appointments_done') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['services'] as $service)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-40 bg-primary-subtle rounded-circle me-3">
                                                <i class="ph ph-stethoscope text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $service->servicename ?? __('messages.not_available') }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success">
                                            {{ \Currency::format($service->charges ?? 0) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $service->clinic_name ?? __('messages.not_available') }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $appointmentCount = collect($data['total_appointment'] ?? [])
                                                ->where('service_id', $service->service_id)
                                                ->first()['count'] ?? 0;
                                        @endphp
                                        <span class="badge bg-primary-subtle text-primary">{{ $appointmentCount }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ph ph-stethoscope text-muted fs-1"></i>
                        <p class="text-muted mt-2">{{ __('clinic.no_services_available') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Commission Information -->
        @if(isset($data['commissions']) && count($data['commissions']) > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('clinic.commission_structure') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($data['commissions'] as $commission)
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="mb-1">{{ $commission['title'] ?? __('messages.not_available') }}</h6>
                                <p class="mb-1 text-muted">{{ $commission['type'] ?? __('messages.not_available') }}</p>
                                <span class="badge bg-primary">{{ $commission['value'] ?? __('messages.not_available') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Ratings and Reviews -->
        @if(isset($data['ratings']) && $data['ratings']->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('clinic.ratings_reviews') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        @php
                            $averageRating = $data['ratings']->avg('rating') ?? 0;
                            $totalRatings = $data['ratings']->count();
                        @endphp
                        <div class="display-4 text-warning mb-2">{{ number_format($averageRating, 1) }}</div>
                        <div class="mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="ph ph-star{{ $i <= $averageRating ? '' : '-thin' }} text-warning"></i>
                            @endfor
                        </div>
                        <p class="text-muted">{{ $totalRatings }} {{ __('clinic.reviews') }}</p>
                    </div>
                    <div class="col-md-8">
                        @foreach($data['ratings']->take(3) as $rating)
                        <div class="d-flex align-items-start mb-3">
                            <div class="avatar avatar-40 bg-primary-subtle rounded-circle me-3">
                                <i class="ph ph-user text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <h6 class="mb-0 me-2">{{ $rating->user->full_name ?? __('clinic.anonymous') }}</h6>
                                    <div>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="ph ph-star{{ $i <= $rating->rating ? '' : '-thin' }} text-warning small"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-muted mb-0">{{ $rating->review_msg ?? __('clinic.no_review_provided') }}</p>
                                <small class="text-muted">{{ $rating->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('after-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
@endpush
