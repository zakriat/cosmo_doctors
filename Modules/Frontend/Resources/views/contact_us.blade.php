@extends('frontend::layouts.master')

@section('content')

@section('title', __('frontend.contact_us'))

@include('frontend::components.section.breadcrumb')
<div class="list-page section-spacing px-0">
    <div class="page-title" id="page_title">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                @php
                    $lat = $settings['bussiness_address_latitude'] ?? '51.5033'; // Default: London Eye
                    $lng = $settings['bussiness_address_longitude'] ?? '-0.1195';
                @endphp
                <iframe loading="lazy" class="iframe-map" src="https://maps.google.com/maps?q={{ $lat }},{{ $lng }}&amp;t=m&amp;z=10&amp;output=embed&amp;iwloc=near" title="Business Location" aria-label="Business Location"></iframe>
                </div>
                <div class="col-lg-4 mt-lg-0 mt-5">
                    <div class="contact-card p-4 section-bg rounded">
                        <h5>We are here to support your health and well-being</h5>
                        <p class="m-0 font-size-14">Our passion is creating personalized and effective healthcare solutions. Please email or call us if you have any questions.</p>

                        @php
                           $country = isset($settings['country']) ? Modules\World\Models\Country::find($settings['country'])->name ?? null : null;
                           $state = isset($settings['state']) ? Modules\World\Models\State::find($settings['state'])->name ?? null : null;
                           $city = isset($settings['city']) ? Modules\World\Models\City::find($settings['city'])->name ?? null : null;
                           $shop_number = $settings['shop_number'] ?? null;  // Ensure 'shop_number' is present
                           $area = $settings['area'] ?? null;
                        @endphp

                        <div class="contact-detial">
                            @if(!empty($settings['bussiness_address_line_2']) || $state || $country || $city)
                                <h6 class="text-primary">
                                    <a href="javascript:void(0);">{{ $state ? "$state, " : '' }}{{ $country ?? '' }}</a>
                                </h6>
                                <div class="d-flex gap-2 mb-1">
                                    <h5 class="mb-0 font-size-14 fw-medium">Address:</h5>
                                    <p class="mb-0 font-size-14">
                                        {{ $settings['bussiness_address_line_1'] ?? '' }},
                                        {{ $settings['bussiness_address_line_2'] ?? '' }}
                                        {{ $city ? ", $city" : '' }}
                                        {{ $settings['bussiness_address_postal_code'] ?? '' }}
                                    </p>
                                </div>
                            @endif

                            @if(!empty($settings['inquriy_email']))
                                <div class="d-flex gap-2 mb-1">
                                    <h5 class="mb-0 font-size-14 fw-medium">Email:</h5>
                                    <p class="mb-0 font-size-14">{{ $settings['inquriy_email'] }}</p>
                                </div>
                            @endif

                            @if(!empty($settings['helpline_number']))
                                <div class="d-flex gap-2 mb-1">
                                    <h5 class="mb-0 font-size-14 fw-medium">Phone:</h5>
                                    <p class="mb-0 font-size-14">{{ $settings['helpline_number'] }}</p>
                                </div>
                            @endif

                            @if(!empty($settings['shop_number']))
                                <div class="d-flex gap-2 mb-1">
                                    <h5 class="mb-0 font-size-14 fw-medium">{{ __('service_providers.lbl_shop_number') }}:</h5>
                                    <p class="mb-0 font-size-14">{{ $settings['shop_number'] }}</p>
                                </div>
                            @endif

                            @if(!empty($area))
                                <div class="d-flex gap-2 mb-1">
                                    <h5 class="mb-0 font-size-14 fw-medium">Area:</h5>
                                    <p class="mb-0 font-size-14">{{ $area }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- <div>
                            <h6 class="font-size-18">Follow Us</h6>
                            <ul class="list-inline contact-social d-flex flex-wrap gap-3 mt-3">
                                <li><a href="https://www.facebook.com/"><i class="ph-fill ph-facebook-logo"></i></a></li>
                                <li><a href="https://x.com/"><i class="ph-fill ph-x-logo"></i></a></li>
                                <li><a href="#"><i class="ph-fill ph-linkedin-logo"></i></a></li>
                                <li><a href="https://www.youtube.com/embed/FONivpg_jvo?si=-wMzDPa1TvycbJT7"><i class="ph-fill ph-youtube-logo"></i></a></li>
                                <li><a href="https://in.instagram.com/"><i class="ph-fill ph-instagram-logo"></i></a></li>
                            </ul>
                        </div> -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
