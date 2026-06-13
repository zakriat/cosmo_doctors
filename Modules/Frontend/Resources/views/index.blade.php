@extends('frontend::layouts.master')
@section('title', __('frontend.home'))

@section('content')
    <!-- Main Banner -->
    @if (
        $data['sectionData'] &&
            isset($data['sectionData']['section_1']) &&
            $data['sectionData']['section_1']['section_1'] == 1)
        <div id="banner-section" class="banner-section">
            @include('frontend::components.section.banner', [
                'clinics' => $data['allclinics'],
                'services' => $data['allservice'],
                'paymentMethods' => $data['paymentMethods'],
                'sliders' => $data['sliders'],
                'sectionData' => $data['sectionData'],
            ])
        </div>
    @endif

    <!-- Categories Section -->
    @if ( $data['sectionData'] && isset( $data['sectionData']['section_2']) &&  $data['sectionData']['section_2']['section_2'] == 1)
    <div class="category-section">
        @include('frontend::components.section.category_section', ['sectionData' => $data['sectionData']])
    </div>
    @endif

    <!-- Service Section -->
    <div class="service-section">
        @include('frontend::components.section.service_section', ['sectionData' => $data['sectionData']])
    </div>

    <!-- Service Section -->
    <div class="service-section">
        @include('frontend::components.section.appurl_section', ['sectionData' => $data['sectionData']])
    </div>

    <!-- Clinic Section -->
    <div class="client-section">
        @include('frontend::components.section.clinic_section', ['sectionData' => $data['sectionData']])
    </div>

    <!-- Doctor Section -->
    <div class="doctor-section">
        @include('frontend::components.section.doctor_section', ['sectionData' => $data['sectionData']])
    </div>

    <!-- Faq Section -->
    <div class="faq-section">
        @include('frontend::components.section.faq_section', [
            'faqs' => $data['faqs'],
            'sectionData' => $data['sectionData'],
        ])
    </div>

    <!-- Testimonial Section -->
    <div class="testimonial-section">
        @include('frontend::components.section.testimonial_section', [
            'ratings' => $data['ratings'],
            'sectionData' => $data['sectionData'],
        ])
    </div>

    <!-- Blog Section -->
    <div class="blog-section">
        @include('frontend::components.section.blog_section', [
            'blogs' => $data['blogs'],
            'sectionData' => $data['sectionData'],
        ])
    </div>
@endsection

@push('after-scripts')
    <script>
        console.log(document.getElementsByClassName('main-banner'));
    </script>
@endpush
