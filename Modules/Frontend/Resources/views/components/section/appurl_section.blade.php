@if ($sectionData && isset($sectionData['section_4']) && $sectionData['section_4']['section_4'] == 1)

@php
    $mediaGooglePay = Spatie\MediaLibrary\MediaCollections\Models\Media::where('collection_name', 'google_play')->first();
    $mediaAppStore = Spatie\MediaLibrary\MediaCollections\Models\Media::where('collection_name','app_store')->first();
    $mediaMainImage = Spatie\MediaLibrary\MediaCollections\Models\Media::where('collection_name','main_image')->first();
    $playstore_url =   App\Models\Setting::where('name','patient_app_play_store')->first();
    $appstore_url =   App\Models\Setting::where('name','patient_app_app_store')->first();
    $playStoreUrl = $playstore_url ? $playstore_url->val : 'https://play.google.com/';
    $appStoreUrl = $appstore_url && $appstore_url->val != '-' ? $appstore_url->val : 'https://apps.apple.com/';
@endphp

<div class="app-section bg-primary-subtle" style="background-image: url('{{ asset('img/frontend/bg-vector.png') }}'); background-position: center center; background-repeat: no-repeat; background-size: cover;">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-6  align-self-center">
                <div class="section-spacing">
                    <h4 class="mb-3">{{ $sectionData['section_4']['title'] }}</h4>
                    <p class="text-body">{{ $sectionData['section_4']['description'] }}</p>
                    <div class="d-flex flex-wrap gap-3 mt-5 pt-3 app-section-btn">
                        @if($mediaGooglePay)
                            <a href="{{ $playStoreUrl }}"><img src="{{ url('storage/' . $mediaGooglePay->id . '/' . $mediaGooglePay->file_name) }}" alt="app-btn-images" class="img-fluid rounded"></a>
                        @else
                            <a href="{{ $playStoreUrl }}"><img src="{{ asset('img/frontend/android-btn.png')}}" alt="app-btn-images" class="img-fluid rounded"></a>
                        @endif

                        @if($mediaAppStore)
                            <a href="{{ $appStoreUrl }}"><img src="{{ url('storage/' . $mediaAppStore->id . '/' . $mediaAppStore->file_name) }}" alt="app-btn-images" class="img-fluid rounded"></a>
                        @else 
                            <a href="{{ $appStoreUrl }}"><img src="{{ asset('img/frontend/ios-btn.png')}}" alt="app-btn-images" class="img-fluid rounded"></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-1 d-lg-block d-none"></div>
            <div class="col-lg-4 col-md-6 position-relative app-box-image">
                @if($mediaMainImage)
                    <img src="{{ url('storage/' . $mediaMainImage->id . '/' . $mediaMainImage->file_name) }}" alt="image" class="w-100 object-fit-cover app-images rounded">
                @else
                    <img src="{{ asset('img/frontend/app-images.png')}}" alt="image" class="w-100 object-fit-cover app-images rounded">
                @endif
                <div class="try-now d-lg-block d-none">
                    <p class="vector-title m-0">Try Now</p>
                    <img src="{{ asset('img/frontend/vector-1.png') }}" alt="img" class="w-75 h-75">
                </div>
            </div>
            <div class="col-lg-1 d-lg-block mt-5 pt-5 d-none">
            </div>
        </div>
    </div>
</div>
@endif