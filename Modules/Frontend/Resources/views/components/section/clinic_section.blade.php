@if ($sectionData && isset($sectionData['section_5']) && $sectionData['section_5']['section_5'] == 1)
    <div class="section-spacing">
        <div class="container">
            <div
                class="section-title d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                <div class="title-info">
                    <span class="sub-title">{{ $sectionData['section_5']['title'] }}</span>
                    <h5 class="m-0 title">{{ $sectionData['section_5']['subtitle'] }}</h5>
                </div>
                <div><a href="{{ route('clinics') }}" class="btn btn-secondary">{{ __('clinic.view_all') }}</a></div>
            </div>
            <div class="slick-general clinic-section-cards" data-items="4" data-items-desktop="4" data-items-laptop="3"
                data-items-tab="2" data-items-mobile-sm="1" data-items-mobile="1" data-speed="1000"
                data-autoplay="false" data-center="false" data-infinite="false" data-navigation="false"
                data-pagination="true" data-spacing="12">
                @for ($i = 0; $i < 4; $i++)
                    @include('frontend::components.card.shimmer_clinic_card')
                @endfor
                @foreach ($sectionData['section_5']['clinic_id'] as $clinic_id)
                    @php
                        $clinic = \Modules\Clinic\Models\Clinics::where('id', $clinic_id)->first();
                    @endphp
                    <div class="slick-item d-none cliniccards">
                        @if($clinic)
                        <x-frontend::card.clinic_card :clinic="$clinic" />
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
            const shimmerCards = document.querySelectorAll('.clinic-section-card');

            function showNextclinicCards() {
                const cliniccards = document.querySelectorAll('.cliniccards');
                const slickSlider = $('.clinic-section-cards');
                shimmerCards.forEach((card) => {
                    const cardIndex = $(card).index();
                    if ($(card).hasClass('slick-active')) {
                        slickSlider.slick('slickNext');
                    }
                    slickSlider.slick('slickRemove', cardIndex);
                });
                if (cliniccards) {
                    cliniccards.forEach(card => card.classList.remove(
                        'd-none')); // Remove 'd-none' from the element
                }
            }
            const serviceSection = document.querySelector('.client-section');
            if (serviceSection) {
                const observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            showNextclinicCards();
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
