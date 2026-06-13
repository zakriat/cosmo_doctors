@extends('frontend::layouts.master')
@section('title',  $service->name )

@section('content')
@include('frontend::components.section.breadcrumb')
    <div class="list-page section-spacing px-0">
        <div class="page-title" id="page_title">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-md-8">
                        <div class="service-detail-inner">
                            <img src="{{ $service->file_url }}" alt="service-image"
                                class="w-100 rounded object-cover service-images">
                            <div class="my-5">
                                <h5 class="mb-3">{{ __('frontend.about_service') }}
                                </h5>
                                <p>{{ $service->description }}</p>
                            </div>

                            <div class="d-flex align-items-center justify-content-between gap-5 flex-wrap mt-5 mb-3">
                                <h6 class="mb-0 font-size-18">{{ __('frontend.choose_clinic') }}
                                </h6>
                                @if ($service->ClinicServiceMapping->count() > 3)
                                    <a href="{{ route('clinics', ['service_id' => $service->id ?? null]) }}"
                                        class="text-secondary fw-bold font-size-14">{{ __('clinic.view_all') }}</a>
                                @endif
                            </div>

                            @if ($service->ClinicServiceMapping)
                                <div class="row gy-4 row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3"
                                    id="clinic-cards">
                                    @foreach ($service->ClinicServiceMapping->take(3) as $clinic_service)
                                        <div class="col">
                                            <x-frontend::card.clinic_card :clinic="$clinic_service->center" />
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 mt-md-0 mt-5">


                        <div class="service-detail-content section-bg rounded sticky">
                            <span class="badge bg-primary-subtle mb-3">{{ optional($service->category)->name }}</span>
                            <h5>{{ $service->name }}</h5>
                            @if ($service->discount)
                                <div class="service-price d-flex flex-wrap align-items-center gap-4  pb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        @if(isset($service->payable_amount))
                                            <span class="text-secondary fw-bold">{{ Currency::format($service->payable_amount) }}</span>
                                            <del>{{ Currency::format($service->charges + $service->inclusive_tax_price) }}</del>
                                        @else
                                            <span class="text-secondary fw-bold">{{ Currency::format($service->charges + $service->inclusive_tax_price) }}</span>
                                            <del>{{ Currency::format($service->charges + $service->inclusive_tax_price) }}</del>
                                        @endif
                                    </div>
                                    <span class="text-success fw-bold">
                                        {{ $service->discount_type == 'percentage' ? $service->discount_value . '%' : Currency::format($service->discount_value) }}
                                        off</span>
                                </div>
                            @else
                                <div class="service-price d-flex flex-wrap align-items-center gap-4  pb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-secondary fw-bold">{{ Currency::format($service->charges + $service->inclusive_tax_price) }}</span>
                                    </div>
                                </div>
                            @endif

                            @if($service->inclusive_tax_price >0 )

                            <div class="mb-3">
                                <small class="text-secondary"><i>{{ __('messages.lbl_with_inclusive_tax') }}</i></small>
                            </div>
                            @endif

                            <div class="service-duration d-flex align-items-center gap-2 mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ph ph-clock"></i>
                                    <span class="m-0"> {{ __('frontend.duration') }}</span>
                                </div>
                                <h6 class="m-0">{{getDurationFormat($service->duration_min) }}
                                </h6>
                            </div>
                            <a href="{{ route('booking', ['id' => $service->id]) }}" class="btn btn-secondary w-100"
                                id="book-now-button">{{ __('frontend.book_now') }}
                                </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        let clinicId = null;
        const bookNowButton = document.getElementById('book-now-button');
        $(document).ready(function() {
            const rows = document.querySelectorAll('.clinics-card');
            console.log('rows:', rows);
            rows.forEach((row) => {
                const card = row;
                console.log('card:', card);
                if (card && !card.querySelector('.clinic-checkbox')) {
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.className = 'clinic-checkbox form-check-input m-2 position-absolute';
                    card.style.position = 'relative'; // Ensure the card has relative positioning
                    card.appendChild(checkbox);
                }
            });


            attachClinicEventListeners();


        });

        function attachClinicEventListeners() {
            const clinicCheckboxes = document.querySelectorAll('.clinic-checkbox');

            clinicCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', (event) => {
                    if (event.target.classList.contains('clinic-checkbox')) {
                        // Deselect other checkboxes
                        document.querySelectorAll('.clinic-checkbox').forEach((cb) => {
                            if (cb !== event.target) {
                                cb.checked = false;
                                cb.closest('.clinics-card').classList.remove('text-muted');
                            }
                        });

                        // Apply muted style to the selected card
                        const card = event.target.closest('.clinics-card');
                        if (event.target.checked) {
                            card.classList.add('text-muted');
                            clinicId = card.dataset.id;
                            updateClinicSelection(card); // Enable the button
                        } else {
                            card.classList.remove('text-muted');
                            clinicId = null;
                        }
                    }
                    console.log('clinicId:', clinicId);
                    updateBookNowButtonLink();
                });
            });


        }

        function updateClinicSelection(card) {
            const link = card.querySelector('a[href*="/clinic-details/"]');
            const clinicNameElement = card.querySelector('.clinics-title a');
            if (link) {
                const url = link.getAttribute('href');
                const clinicIdMatch = url.match(/clinic-details\/(\d+)/);
                const clinicName = clinicNameElement.textContent.trim();
                if (clinicIdMatch) {
                    clinicId = clinicIdMatch[1];
                }
            }
        }

        function updateBookNowButtonLink() {
            // Update the href of the "Book Now" button
            console.log('clinicId:', clinicId);
            const currentUrl = "{{ route('booking', ['id' => $service->id]) }}";
            const updatedUrl = clinicId ? currentUrl + "?clinic_id=" + clinicId : currentUrl;
            bookNowButton.setAttribute('href', updatedUrl);
            bookNowButton.setAttribute('data-clinic-id', clinicId); // Update data-clinic-id attribute
        }
    </script>
@endpush
