@if ($sectionData && isset($sectionData['section_6']) && $sectionData['section_6']['section_6'] == 1)
    <div class="section-spacing section-background">
        <div class="container">
            <div
                class="section-title d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                <div class="title-info">
                    <span class="sub-title">{{ $sectionData['section_6']['title'] }}</span>
                    <h5 class="m-0 title">{{ $sectionData['section_6']['subtitle'] }}</h5>
                </div>
                <div><a href="{{ route('doctors') }}" class="btn btn-secondary">{{ __('clinic.view_all') }}</a></div>
            </div>
            <div class="slick-general doctor-section-cards" data-items="4" data-items-desktop="4" data-items-laptop="3"
                data-items-tab="2" data-items-mobile-sm="1" data-items-mobile="1" data-speed="1000"
                data-autoplay="false" data-center="false" data-infinite="false" data-navigation="false"
                data-pagination="true" data-spacing="12">
                @foreach ($sectionData['section_6']['doctor_id'] as $doctor_id)
    @php
        // Fetch doctor with the associated user
        $doctor = \Modules\Clinic\Models\Doctor::where('id', $doctor_id)
            ->with('user') // Removed empty string in the relationship
            ->first();

        if ($doctor) {
            $doctor_user = optional($doctor->user);
            $doctor_id = $doctor_user->id; // Ensure we are using the correct user ID
        
            $total_appointment = \Modules\Appointment\Models\Appointment::where('doctor_id', $doctor_id)
                ->where('status', 'checkout')
                ->count();

            $reviews = \Modules\Clinic\Models\DoctorRating::where('doctor_id', $doctor_id)->get();
            $average_rating = $reviews->isNotEmpty() ? round($reviews->avg('rating'), 1) : 0; 

            $doctor->average_rating = $average_rating;
            $doctor->total_appointment = $total_appointment;
        }
    @endphp

    @if ($doctor)
        <div class="d-none doctorcards">
            <x-frontend::card.doctor_card :doctor="$doctor" />
        </div>
    @endif
@endforeach
            </div>
        </div>
    </div>
@endif
@push('after-scripts')
    <script>
        // Variables
        document.addEventListener('DOMContentLoaded', () => {
            const serviceSection = document.querySelector('.doctor-section');
            if (serviceSection) {
                const observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            console.log('hii')
                            showNextdoctorCards();
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

        function showNextdoctorCards() {
            const shimmerCards = document.querySelectorAll('.doctor-section-card');
            const doctorcards = document.querySelectorAll('.doctorcards');
            const slickSlider = $('.doctor-section-cards');
            shimmerCards.forEach((card) => {
                const cardIndex = $(card).index();
                if ($(card).hasClass('slick-active')) {
                    slickSlider.slick('slickNext');
                }
                slickSlider.slick('slickRemove', cardIndex);
            });
            if (doctorcards) {
                doctorcards.forEach(card => card.classList.remove(
                    'd-none')); // Remove 'd-none' from the element
            }
        }
    </script>
@endpush
