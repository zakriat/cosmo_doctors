<div class="services-card p-3 rounded">
    <div class="position-relative">
        <a href="{{ route('service-details', ['id' => $service->id]) }}" class="services-img">
            <img src="{{ $service->file_url }}" alt="services-image" class="w-100 object-cover">
        </a>
        <div class="d-flex position-absolute gap-3 align-items-center service-meta">
            @if ($service->is_video_consultancy)
                <div class="service-meta-video bg-success badge">
                    <i class="ph ph-video-camera me-1 font-size-14 fw-bold align-middle"></i>
                    <span class="font-size-12"> Video</span>
                </div>
            @endif

            <span class="service-meta-dentist badge">{{ optional($service->category)->name ?? '' }}</span>
        </div>
    </div>
    <h6 class="service-title line-count-1 mt-3">
        <a href="{{ route('service-details', ['id' => $service->id]) }}">
            {{ $service->name }}
        </a>
    </h6>
    <div class=" mb-3 pb-1">

        <div class="service-price d-flex align-items-center gap-4">
        @if ($service->discount)
            <div class="d-flex align-items-center gap-2">
                @if(isset($service->payable_amount))
                    <span class="text-secondary fw-bold">{{ Currency::format($service->payable_amount) }}</span>
                    <del>{{ Currency::format($service->charges + $service->inclusive_tax_price) }}</del>
                @else
                    <span class="text-secondary fw-bold">{{ Currency::format($service->charges + $service->inclusive_tax_price) }}</span>
                    <del>{{ Currency::format($service->charges + $service->inclusive_tax_price) }}</del>
                @endif
            </div>
            <div class="service-offer text-success fw-bold">
                {{ $service->discount_type == 'percentage' ? $service->discount_value . '%' : Currency::format($service->discount_value) }}
                off
            </div>
        @else
            @if($service->inclusive_tax_price > 0)
                <span class="text-secondary fw-bold">{{ Currency::format($service->charges + $service->inclusive_tax_price) }}</span>
            @else
                <span class="text-secondary fw-bold">{{ Currency::format($service->charges) }}</span>
            @endif
        @endif

        </div>

        @if($service->inclusive_tax_price > 0)
        <div>
            <small class="text-secondary"><i>{{ __('messages.lbl_with_inclusive_tax') }}</i></small>
        </div>
        @endif
    </div>

    <div class="service-duration d-flex align-items-center gap-2 mb-4">
        <div class="d-flex align-items-center gap-2">
            <i class="ph ph-clock"></i>
            <span class="m-0">{{ __('frontend.duration') }}
            </span>
        </div>
        <!-- <h6 class="m-0">{{ $service->duration_min }} {{ __('frontend.minutes') }}
            </h6> -->
            <h6 class="m-0">
            {{ getDurationFormat($service->duration_min) }}
          </h6>
    </div>
    <a href="{{ route('booking', ['id' => $service->id]) }}" class="btn btn-primary-subtle w-100">{{ __('frontend.book_now') }}
    </a>
</div>

