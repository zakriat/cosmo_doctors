<div class="d-flex gap-3 align-items-center">
    <div class="text-start">
        <h6 class="m-0">{{ $data->name }}</h6>
        @if($data->generic_name)
            <small class="text-muted">{{ __('medicines.lbl_generic') }}: {{ $data->generic_name }}</small>
        @endif
        @if($data->brand_name)
            <br><small class="text-muted">{{ __('medicines.lbl_brand') }}: {{ $data->brand_name }}</small>
        @endif
        @if($data->url)
            <br><a href="{{ $data->url }}" target="_blank" class="text-primary small">
                <i class="fa-solid fa-external-link"></i> {{ __('medicines.lbl_reference') }}
            </a>
        @endif
    </div>
</div>