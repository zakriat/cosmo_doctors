<div class="testimonial-card position-relative">
    <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
        <div class="d-flex align-items-center flex-shrink-0">
            @for ($i = 1; $i <= 5; $i++)
            @if ($i <= $rating->rating)
            <i class="ph-fill ph-star text-warning"></i>
            @else
            <i class="ph ph-star text-warning"></i>
            @endif
            @endfor
        </div>
        <h6 class="m-0 fw-bold font-size-10">{{ $rating->rating }}</h6>
    </div>
    <p class="fw-medium fst-italic font-size-18 mb-0 testimonial-desc text-center">
        @if(!empty($rating->review_msg))
        "{{ $rating->review_msg }}" 
        @endif
        <a href="{{ route('doctor-details', ['id' => optional($rating->doctor->doctor)->id]) }}">
            {{ '@' . optional($rating->doctor)->first_name . ' ' . optional($rating->doctor)->last_name }}
        </a>
    </p>
    <div class="d-flex justify-content-center align-items-center gap-3 mt-5 pt-md-4 pt-0">
        <img src="{{ optional($rating->user)->profile_image }}" alt="img" class="img-fluid author-img rounded-circle">
        <div>
            <h5 class="line-count-1">{{ optional($rating->user)->first_name.' '.optional($rating->user)->last_name }}</h5>

            @php 
                $timezone = App\Models\Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';
                $updatedAt = $rating->updated_at->setTimezone($timezone); 
            @endphp
            <span class="m-0 font-size-14">{{ $updatedAt->format('d M, Y') }}</span>
        </div>
    </div>
    <img src="{{ asset('img/frontend/vector-3.png')}}" alt="img" class="vector-arrow">
</div>
