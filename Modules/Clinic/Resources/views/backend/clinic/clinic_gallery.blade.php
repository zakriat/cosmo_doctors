@extends('layouts.offcanvas')

@section('offcanvas-content')
<form id="clinic-gallery-form" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="offcanvas offcanvas-end" tabindex="-1" id="clinic-gallery-form" aria-labelledby="form-offcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h6 class="m-0 h5">
                {{ __('clinic.singular_title') }}: <span>{{ $clinic->name ?? '' }}</span>
            </h6>
            <button type="button" class="btn-close-offcanvas" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="ph ph-x-circle"></i>
            </button>
        </div>
        <div class="d-flex flex-column border-bottom p-3">
            <div>
                {{-- Uppy Dashboard placeholder --}}
                <div id="drag-drop-area" class="custom-file-upload mb-3"></div>
            </div>
        </div>
        <div class="offcanvas-body">
            @if(empty($featureImages) || count($featureImages) === 0)
                <div class="text-center mb-0">{{ __('messages.data_not_available') }}</div>
            @else
                <div class="gallery-images row">
                    @foreach($featureImages as $index => $feature)
                        <div class="image-container col">
                            <button class="delete-button btn btn-danger btn-sm position-absolute top-0 end-0" type="button" onclick="removeImage({{ $index }})">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            <img src="{{ $feature['full_url'] ?? '' }}" alt="{{ __('clinic.selected_image') }}" class="img-fluid selected-image" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="offcanvas-footer">
            <div class="d-grid d-sm-flex justify-content-sm-end gap-3">
                <button class="btn btn-white d-block" type="button" data-bs-dismiss="offcanvas">{{ __('messages.close') }}</button>
                <button class="btn btn-secondary d-block" id="gallery-upload-btn">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="gallery-upload-spinner"></span>
                    <span id="gallery-upload-text">{{ __('messages.upload') }}</span>
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://releases.transloadit.com/uppy/v3.13.0/uppy.min.js"></script>
@endpush

