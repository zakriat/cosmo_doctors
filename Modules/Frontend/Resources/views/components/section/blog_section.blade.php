@if ($sectionData && isset($sectionData['section_9']) && $sectionData['section_9']['section_9'] == 1 && !empty($sectionData['section_9']['blog_id'])   && $blogs->isNotEmpty())
<div class="section-spacing">
    <div class="container">
        <div class="section-title d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div class="title-info">
                <span class="sub-title">{{ $sectionData['section_9']['title'] }}</span>
                <h5 class="m-0 title">{{ $sectionData['section_9']['subtitle'] }}</h5>
            </div>
            <div><a href="{{ route('blogs') }}" class="btn btn-secondary">{{ __('clinic.view_all') }}</a></div>
        </div>
        <div class="slick-general" data-items="3" data-items-desktop="3" data-items-laptop="3" data-items-tab="2" data-items-mobile-sm="1" data-items-mobile="1" data-speed="1000" data-autoplay="false" data-center="false" data-infinite="false" data-navigation="false" data-pagination="true" data-spacing="12">

            @foreach ($sectionData['section_9']['blog_id'] as $blog_id)
                @php
                    $blog = \Modules\Blog\Models\Blog::find($blog_id);
                @endphp

                @if($blog) 
                    <div class="col">
                        <x-frontend::card.blog_card :blog="$blog" />
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif
