@if ($sectionData && isset($sectionData['section_2']) && $sectionData['section_2']['section_2'] == 1)
    <div class="section-spacing">
        <div class="container">
            <div
                class="section-title d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                <div class="title-info">
                    <span class="sub-title">{{ $sectionData['section_2']['title'] }}</span>
                    <h5 class="m-0 title">{{ $sectionData['section_2']['subtitle'] }}</h5>
                </div>
                <div><a href="{{ route('categories') }}" class="btn btn-secondary">{{ __('clinic.view_all') }}</a></div>
            </div>
            <div class="slick-general category-section-cards" data-items="5" data-items-desktop="5" data-items-laptop="4"
                data-items-tab="3" data-items-mobile-sm="2" data-items-mobile="1" data-speed="1000"
                data-autoplay="false" data-center="false" data-infinite="false" data-navigation="false"
                data-pagination="true" data-spacing="12">
                @foreach ($sectionData['section_2']['category_id'] as $category_id)
                    @include('frontend::components.card.shimmer_category_card')
                @endforeach
                @foreach ($sectionData['section_2']['category_id'] as $category_id)
                    @php
                        $category = \Modules\Clinic\Models\ClinicsCategory::where('id', $category_id)->first();
                    @endphp
                    <div class="slick-item d-none categorycards">
                        @if ($category)
                            <x-frontend::card.category_card :category="$category" />
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
@push('after-scripts')
    <script>
        // Variables
        document.addEventListener('DOMContentLoaded', () => {
            const shimmerCards = document.querySelectorAll('.category-shimmer-card');

            function showNextCategoryCards() {
                const categorycards = document.querySelectorAll('.categorycards');
                const slickSlider = $('.category-section-cards');
                shimmerCards.forEach((card) => {
                    const cardIndex = $(card).index();
                    if ($(card).hasClass('slick-active')) {
                        slickSlider.slick('slickNext');
                    }
                    slickSlider.slick('slickRemove', cardIndex);
                });
                if (categorycards) {
                    categorycards.forEach(card => card.classList.remove(
                        'd-none')); // Remove 'd-none' from the element
                }
            }
            const serviceSection = document.querySelector('.category-section');
            if (serviceSection) {
                const observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            showNextCategoryCards();
                            observer.disconnect();
                        }
                    });
                }, {
                    rootMargin: '0px',
                    threshold: 0.1
                });
                observer.observe(serviceSection);
            } else {
                console.error('Service section not found!');
            }
        });
    </script>
@endpush
