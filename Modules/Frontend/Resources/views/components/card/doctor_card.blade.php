<div class="doctor-card rounded text-center d-block">
    <div class="doctor-inner-card d-block">
        <div class="doctor-img d-inline-block">
            <a href="{{ route('doctor-details', ['id' => $doctor->id]) }}">
                <img src="{{ optional($doctor->user)->profile_image }}" alt="image" class="img-fluid rounded-circle object-fit-cover">
            </a>
        </div>
        <div class="doctor-content mb-3 pb-1">
            <div class="d-flex align-items-center justify-content-center gap-2 mt-4 mb-1 doctor-name">
                <h5 class="text-capitalize m-0">
                    <a href="{{ route('doctor-details', ['id' => $doctor->id]) }}">   {{getDisplayName($doctor->user)}}</a>
                </h5>
                <span class="doctor-available"></span>
            </div>
            <p class="font-size-14 mb-0 fw-medium">{{ optional(optional($doctor->user)->profile)->expert ?? '' }}</p>
        </div>
        @php
            $rating = $doctor->average_rating ?? 0;
        @endphp

        <div class="d-flex align-items-center justify-content-center gap-2 mb-4 pb-1">
            <div class="d-flex align-items-center">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $rating)
                        <i class="ph-fill ph-star text-warning"></i>
                    @else
                        <i class="ph ph-star text-warning"></i>
                    @endif
                @endfor
            </div>
            {{-- <small class="doctor-rate m-0 fw-bold">{{ $rating }}</small> --}}
        </div>

        <span class="text-primary font-size-14 fw-semibold">{{ $doctor->total_appointment }} {{ __('frontend.patient_served') }}
        </span>
    </div>
</div>

