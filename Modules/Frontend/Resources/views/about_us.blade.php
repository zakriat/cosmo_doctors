@extends('frontend::layouts.master')

@section('title', __('frontend.about_us'))

@section('content')
<div class="list-page section-spacing px-0">
    <div class="page-title" id="page_title">
        <div class="container">
            <div class="row">
                <div class="col-lg-1 d-lg-block d-none"></div>
                <div class="col-lg-10">
                    <div class="section-title d-flex justify-content-center align-items-center gap-3">
                        <div class="title-info text-center">
                            <span class="sub-title">{{__('frontend.about_us')}}</span>
                         
                        </div>
                    </div>
                        <div class="mt-5 text-center">

                        {!! $about_us['description'] !!}
                           
                        </div>
                    </div>
                </div>
                <div class="col-lg-1 d-lg-block d-none"></div>
            </div>
        </div>
    </div>
</div>
@endsection