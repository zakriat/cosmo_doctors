@extends('frontend::layouts.master')
@section('title', __('frontend.doctors'))

@section('content')
    @include('frontend::components.section.breadcrumb')
    <div class="list-page section-spacing px-0">
        <div class="movie-lists">
            <div class="container-fluid">
                <div class="row gy-2">
                    <div class="col-xl-7 col-lg-9 filter">
                        <div class="d-flex flex-lg-row flex-column gap-3 align-items-lg-center">
                            <h6 class="m-0 flex-shrink-0">{{ __('frontend.filter_by') }}</h6>
                            <div class="row gx-3 gy-2 flex-grow-1">
                                <div class="col-md-4">
                                    <div class="form-group datatable-filter">
                                        <select name="clinic" id="clinic" class="form-select select2" data-filter="select">
                                            {{-- <option value="" disabled selected>{{ __('clinic.lbl_select_clinic') }}</option> --}}
                                            <option value="">{{ __('service.all') }} {{ __('service.lbl_clinic') }}
                                            </option>
                                            @foreach ($clinics as $clinic)
                                                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select name="service" id="service" class="form-select select2" data-filter="select">
                                        <option value="" disabled selected>{{ __('frontend.select_service') }}</option>
                                        <option value="">{{ __('service.all') }} {{ __('service.designation') }}
                                        </option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="rating" id="rating" class="form-select select2" data-filter="select">
                                        <option value="" selected>{{ __('service.all') }} {{ __('rating') }}
                                        </option>
                                        <option value="1-2">1 to 2</option>
                                        <option value="2-3">2 to 3</option>
                                        <option value="3-4">3 to 4</option>
                                        <option value="4-5">4 to 5</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 d-xl-block d-none"></div>
                    <div class="col-lg-3">
                        <div class="d-flex align-items-center p-2 rounded section-bg">
                            <div class="icon ps-2">
                                <i class="ph ph-magnifying-glass align-middle"></i>
                            </div>
                            <input type="text" id="datatable-search"
                                class="form-control px-2 py-2 h-auto border-0 focus:ring-0" placeholder="{{ __('messages.search') }}...">
                        </div>
                    </div>
                </div>

                <div id="selected-filters" class="d-flex gap-2 flex-wrap mt-3"></div>

                <div class="card-style-slider">
                    <div id="shimmer-loader" class="d-flex gap-3 flex-wrap p-4">
                        @for ($i = 0; $i < 8; $i++)
                            @include('frontend::components.card.shimmer_doctor_card')
                        @endfor
                    </div>
                    <table id="datatable" class="table table-responsive custom-card-table">
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        let finalColumns = [{
            data: 'card',
            name: 'card',
            orderable: false,
            searchable: true
        }];

        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('datatable-search');
            const selectedFiltersDiv = document.getElementById('selected-filters');
            const clinicFilter = $('#clinic');
            const serviceFilter = $('#service'); 
            const ratingFilter = $('#rating');
            
            clinicFilter.select2({ 
                placeholder: '{{ __('service.lbl_clinic') }}',
                allowClear: true,
                width: '100%'
            });
            serviceFilter.select2({ 
                placeholder: '{{ __('frontend.select_service') }}',
                allowClear: true,
                width: '100%'
            });
            ratingFilter.select2({ 
                placeholder: '{{ __('frontend.rating') }}',
                allowClear: true,
                width: '100%'
            });

            const filters = {
                clinic: '',
                service: '',
                rating: ''
            };

            function updateSelectedFilters() {
                selectedFiltersDiv.innerHTML = '';

                const selectedClinic = clinicFilter.find('option:selected');
                const selectedService = serviceFilter.find('option:selected');
                const selectedRating = ratingFilter.find('option:selected');

                const selectedFilters = [
                    { key: 'clinic', text: selectedClinic.val() ? selectedClinic.text() : '' },
                    { key: 'service', text: selectedService.val() ? selectedService.text() : '' },
                    { key: 'rating', text: selectedRating.val() ? selectedRating.text() : '' }
                ];

                selectedFilters.forEach(filter => {
                    if (filter.text && !filter.text.includes('All')) {
                        const filterBadge = document.createElement('span');
                        filterBadge.classList.add('filters-bagde', 'px-3', 'py-2');
                        filterBadge.textContent = filter.text;
                        filterBadge.style.backgroundColor = 'var(--bs-primary-bg-subtle)';
                        filterBadge.style.border = '1px solid var(--bs-primary)';
                        filterBadge.style.color = 'var(--bs-heading-color)';
                        filterBadge.style.borderRadius = 'var(--bs-border-radius-pill)';

                        const closeIcon = document.createElement('span');
                        closeIcon.classList.add('ms-3', 'fw-bold', 'text-primary', 'font-size-14');
                        closeIcon.innerHTML = '&times;';
                        closeIcon.style.cursor = 'pointer';
                        closeIcon.onclick = () => clearFilter(filter.key);

                        filterBadge.appendChild(closeIcon);
                        selectedFiltersDiv.appendChild(filterBadge);
                    }
                });
            }

            function clearFilter(key) {
                switch(key) {
                    case 'clinic':
                        clinicFilter.val('').trigger('change');
                        break;
                    case 'service':
                        serviceFilter.val('').trigger('change');
                        break;
                    case 'rating':
                        ratingFilter.val('').trigger('change');
                        break;
                }
                
                if (window.renderedDataTable) {
                    window.renderedDataTable.draw();
                }
            }

            function handleFilterChange(select, key) {
                select.on('change', function() {
                    updateSelectedFilters();
                    if (window.renderedDataTable) {
                        window.renderedDataTable.draw();
                    }
                });
            }

            handleFilterChange(clinicFilter, 'clinic');
            handleFilterChange(serviceFilter, 'service');
            handleFilterChange(ratingFilter, 'rating');

            const shimmerLoader = document.getElementById('shimmer-loader');
            const dataTableElement = document.getElementById('datatable');

            frontInitDatatable({
                url: '{{ route('doctor.index_data') }}',
                finalColumns,
                advanceFilter: () => ({
                    filter: {
                        clinic_id: clinicFilter.val(),
                        service_id: serviceFilter.val(),
                        rating: ratingFilter.val(),
                    },
                    search: searchInput.value
                }),
                onLoadStart: () => {
                    shimmerLoader.classList.remove('d-none');
                    dataTableElement.classList.add('d-none');
                },
                onLoadComplete: () => {
                    shimmerLoader.classList.add('d-none');
                    dataTableElement.classList.remove('d-none');
                },
                language: {
                    processing: '',
                }
            });

            searchInput.addEventListener('keyup', function () {
                if (window.renderedDataTable) {
                    window.renderedDataTable.search(this.value).draw();
                }
            });


            updateSelectedFilters();
        });
    </script>
@endpush