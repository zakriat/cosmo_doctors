@if ($sectionData && isset($sectionData['section_7']) && $sectionData['section_7']['section_7'] == 1)
<div class="section-spacing">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="d-flex justify-content-between flex-column h-100">
                    <div class="section-title">
                        <div class="title-info  mb-5 pb-2">
                            <span class="sub-title">{{ $sectionData['section_7']['title'] }}</span>
                            <h5 class="mb-3 title">{{ $sectionData['section_7']['subtitle'] }}</h5>
                            <p class="mb-0">{{ $sectionData['section_7']['description'] }}</p>
                        </div>
                        <a href="{{ route('faq') }}" class="text-secondary font-size-14 fw-bold">{{ __('clinic.view_all') }}</a>
                    </div>
                    <div>
                           <img src="{{ asset('img/frontend/vector-2.png')}}" alt="img" class="vector-arrow">
                           <p class="vector-title m-0">Ask anything</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-6 mt-lg-0 mt-4">
                <div class="accordion" id="faq">
                    @foreach($faqs as $key => $value)
                        <div class="accordion-item custom-accordion rounded">
                            <h2 class="accordion-header" id="headingOne_{{ $value->id }}">
                                <button class="accordion-button custom-accordion-button gap-3 p-0 {{ !$loop->first ? 'collapsed' : '' }}" type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapseOne_{{ $value->id }}" 
                                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                    aria-controls="collapseOne_{{ $value->id }}">
                                    {{ $value->question }}
                                </button>
                            </h2>
                            <div id="collapseOne_{{ $value->id }}" 
                                class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                aria-labelledby="headingOne_{{ $value->id }}" 
                                data-bs-parent="#faq">
                                <div class="accordion-body custom-accordion-body p-0">
                                    <span>{{ $value->answer }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif