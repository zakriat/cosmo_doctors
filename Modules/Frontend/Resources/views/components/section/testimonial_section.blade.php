@if ($sectionData && isset($sectionData['section_8']) && $sectionData['section_8']['section_8'] == 1 && $ratings->isNotEmpty())
<div class="section-spacing bg-primary-subtle text-body" style="background-image: url('{{ asset('img/frontend/bg-vector.png')}}'); background-position: center center; background-repeat: no-repeat; background-size: cover;">
    <div class="container">
        <div class="slick-general" data-items="1" data-speed="1000" data-autoplay="false" data-center="false" data-infinite="false" data-navigation="false" data-pagination="true" data-spacing="12">
            @foreach($ratings as $rating)
            <div class="slick-item">
                @if($rating)
                <x-frontend::card.testimonial_card :rating="$rating" />
                @endif
            </div>
            @endforeach
        </div>
 
    </div>
</div>
@endif