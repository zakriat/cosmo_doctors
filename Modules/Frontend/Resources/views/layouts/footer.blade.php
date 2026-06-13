<footer class="footer">
    @php
        $settings = App\Models\Setting::whereIn('name', [
            'short_description',
            'helpline_number',
            'inquriy_email',
        ])->pluck('val', 'name');

        $shortDescription = $settings['short_description'] ?? null;
        $helplineNumber = $settings['helpline_number'] ?? null;
        $inquriyEmail = $settings['inquriy_email'] ?? null;

        $footerSection = Modules\FrontendSetting\Models\FrontendSetting::where('key', 'footer-setting')->first();
        $sectionData = $footerSection ? json_decode($footerSection->value, true) : null;
        $quickLinks = Modules\Page\Models\Page::where('status', 1)
            ->orderBy('sequence', 'asc')
            ->get();    
    @endphp

    @if (!empty($sectionData['footer_setting']) && $sectionData['footer_setting'] == 1)
        <div class="footer-top">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xxl-4 col-xl-4 col-md-6">
                        <div class="footer-inner">
                            <div class="footer-logo mb-4">
                                <!--Logo -->
                                <!-- @include('frontend::components.partials.logo') -->

                                <a class="navbar-brand text-primary" href="{{ route('frontend.index') }}">
                                  <img src="{{ asset(setting('dark_logo')) }}" height="40" alt="{{ app_name() }}">
                                </a>
                           
                            </div>
                            <span class="footer-description font-size-14 body-text">{{ $shortDescription }}</span>

                            <div class="mt-4 pt-md-4">
                                @if ($helplineNumber)
                                    <div class="d-flex gap-4 footer-action-card">
                                        <div class="footer-icon">
                                            <i class="ph ph-phone-call"></i>
                                        </div>
                                        <div>
                                            <p class="fw-medium font-size-14 footer-action-info mb-2" class="">
                                                {{ __('frontend.helpline_number') }}</p>
                                            <a href="tel:{{ $helplineNumber }}"
                                                class="footer-action-link">{{ $helplineNumber }}</a>
                                        </div>
                                    </div>
                                @endif

                                @if ($inquriyEmail)
                                    <div class="d-flex gap-4 footer-action-card mt-4 pt-2">
                                        <div class="footer-icon">
                                            <i class="ph ph-envelope-simple"></i>
                                        </div>
                                        <div>
                                            <p class="fw-medium font-size-14 footer-action-info mb-2" class="">{{ __('frontend.mail') }}</p>
                                            <a href="mailto:{{ $inquriyEmail }}"
                                                class="footer-action-link">{{ $inquriyEmail }}</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <!-- <span class="font-size-14">
                            {{ __('frontend.footer_content') }}
                        </span>
                        <div class="mt-5">
                            <p class="mb-2 font-size-14">{{ __('frontend.email_us') }}: <a href="mailto:customer@streamit.com" class="link-body-emphasis">customer@streamit.com</a></p>
                            <p class="m-0 font-size-14">{{ __('frontend.helpline_number') }}: <a href="tel:+480-555-0103" class="link-body-emphasis fw-medium">+ (480) 555-0103</a></p>
                        </div> -->
                        </div>
                    </div>

                    @if ($sectionData['enable_top_service'] == 1)
                        <div class="col-xxl-3 col-xl-3 col-md-6 mt-md-0 mt-5">
                            <h4 class="footer-title">{{ __('frontend.top_service') }}</h4>
                            <ul class="list-inline m-0 footer-menu">
                                @foreach ($sectionData['service_id'] as $service_id)
                                    @php
                                        $service = \Modules\Clinic\Models\ClinicsService::where('id', $service_id)
                                            ->with('appointmentService')
                                            ->first();
                                            $total_appointments = $service && $service->appointmentService
                                            ? $service->appointmentService->count()
                                            : 0;
                                    @endphp
                                    <li>
                                        <div class="d-flex gap-4 services-option">
                                            <div class="services-option-image">
                                                @if ($service)
                                                    <a href="{{ route('service-details', ['id' => $service->id]) }}">
                                                        <img src="{{ $service->file_url }}" alt="services"
                                                        class="img-fluid object-fit-cover border-0">
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="services-option-content">
                                                <h6 class="service-title line-count-1 mt-0 mb-2">
                                                    @if ($service)
                                                    <a href="{{ route('service-details', ['id' => $service->id]) }}">{{ $service->name }}</a>
                                                    @endif
                                                </h6>
                                                <p class="font-size-12">{{ $total_appointments }} {{ __('frontend.appointments') }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach

                            </ul>
                        </div>
                    @endif

                    @if ($sectionData['enable_quick_link'] == 1 && isset($quickLinks))
                    <div class="col-xxl-3 col-xl-3 col-md-6 mt-xl-0 mt-5">
                        <h4 class="footer-title">{{ __('frontend.quick_link') }}</h4>
                        <ul class="list-inline m-0 footer-menu column-count-2">
                            <li><a href="{{ route('contact-us') }}">{{ __('frontend.contact_us') }}</a></li>
                            <li><a href="{{ route('faq') }}">{{ __('frontend.faq') }}</a></li>
                            @foreach ($quickLinks as $page)
                            <li>
                                <a href="{{ route('pages', ['slug' => $page->slug]) }}">
                                    {{ __($page->name) }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                    @if ($sectionData['enable_top_category'] == 1)
                        <div class="col-xxl-2 col-xl-2 col-md-6 mt-xl-0 mt-5">
                            <h4 class="footer-title">{{ __('frontend.top_category') }}</h4>
                            <ul class="list-inline m-0 footer-menu">
                                @foreach ($sectionData['category_id'] as $category_id)
                                    @php
                                        $category = \Modules\Clinic\Models\ClinicsCategory::where(
                                            'id',
                                            $category_id,
                                        )->first();
                                    @endphp

                                    <li>
                                        @if ($category)
                                        <a href="{{ route('services', ['category_id' => $category->id]) }}">{{ $category->name }}</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    @endif
    <div class="footer-bottom">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <div class="text-white font-size-14 d-flex align-items-center justify-content-center justify-content-sm-start">
                        <span class="d-flex align-items-center gap-1">
                            © {{ now()->year }}<a href="{{ route('frontend.index') }}" class="text-primary">{{ __('frontend.kivicare') }}</a><span>{{ __('frontend.all_rights_reserved') }}</span>
                        </span>
                    </div>
                </div>
                <!-- <div class="col-sm-6 mt-sm-0 mt-3">
                    <div class="d-flex gap-3 align-items-center justify-content-center justify-content-sm-end footer-social-icon">
                        <a href="https://www.facebook.com/iqonicdesign/" target="_blank">
                            <i class="ph-fill ph-facebook-logo"></i>
                        </a>
                        <a href="https://twitter.com/iqonicdesign" target="_blank">
                            <i class="ph-fill ph-x-logo"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/iqonicthemes/" target="_blank">
                            <i class="ph-fill ph-linkedin-logo"></i>
                        </a>
                        <a href="https://www.youtube.com/iqonicdesign" target="_blank">
                            <i class="ph-fill ph-youtube-logo"></i>
                        </a>
                        <a href="https://www.instagram.com/iqonicdesign/?igshid=YmMyMTA2M2Y%3D" target="_blank">
                            <i class="ph-fill ph-instagram-logo"></i>
                        </a>
                    </div>

                </div> -->
            </div>
        </div>
    </div>
</footer>
<!-- sticky footer -->
{{-- @include('frontend::components.partials.footer-sticky-menu') --}}
