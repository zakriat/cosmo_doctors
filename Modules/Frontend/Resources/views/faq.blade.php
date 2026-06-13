@extends('frontend::layouts.master')

@section('content')

@section('title', __('frontend.faq'))

@include('frontend::components.section.breadcrumb')
<div class="list-page section-spacing px-0">
    <div class="page-title" id="page_title">
        <!-- <h4 class="m-0 text-center">{{__('frontend.faq')}}</h4> -->

        <div class="container">
            <div class="accordion" id="faq">
                @foreach($faqs as $key => $value)
                    <div class="accordion-item custom-accordion rounded">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button  custom-accordion-button gap-3 p-0 {{ $key == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne_{{ $value->id }}" aria-expanded="true" aria-controls="collapseOne_{{ $value->id }}">
                                {{ $value->question }}
                            </button>
                        </h2>
                        <div id="collapseOne_{{ $value->id }}" class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}" aria-labelledby="headingOne" data-bs-parent="#faq">
                            <div class="accordion-body custom-accordion-body p-0">
                                <span> {{ $value->answer }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection