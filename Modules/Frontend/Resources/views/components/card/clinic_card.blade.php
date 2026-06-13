@if($clinic)
<div class="clinics-card p-3 rounded">
    <div class="position-relative">
        <a href="{{ route('clinic-details', ['id' => optional($clinic)->id]) }}" class="d-block">
            <div class="clinics-img d-inline-block">
                <img src="{{ optional($clinic)->file_url }}" alt="image" class="w-100 rounded object-cover">
            </div>
        </a>
        <div class="d-flex position-absolute gap-3 align-content-center clinics-meta">
            <span class="clinics-meta-box fw-blod text-uppercase bg-success badge rounded-pill">{{ __('frontend.open') }}
            </span>
        </div>
    </div>
    <h6 class="clinics-title line-count-1 mt-3 mb-2 pb-1">
        <a href="{{ route('clinic-details', ['id' => optional($clinic)->id]) }}">
            {{ optional($clinic)->name }}
        </a>
    </h6>
    <div class="clinics-content mb-3">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="ph ph-map-pin-line align-middle font-size-18"></i>
            <a 
                href="https://www.google.com/maps/search/?api=1&query={{ urlencode(optional($clinic)->address) }}" 
                target="_blank" 
                class="m-0 font-size-14 text-decoration-underline"
                title="Open in Google Maps"
            >
                {{ optional($clinic)->address }}
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="ph ph-phone-call font-size-18"></i>
            <a href="tel:{{ optional($clinic)->contact_number }}" class="font-size-14 fw-semibold">
                {{ optional($clinic)->contact_number }}
            </a>

            
        </div>
    </div>
    <a href="{{ route('clinic-details', ['id' => optional($clinic)->id]) }}" class="text-secondary fw-bold">
        {{ __('frontend.view_detail') }}
    </a>
</div>
@endif
