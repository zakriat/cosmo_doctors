@extends('frontend::layouts.master')
@section('title', __('frontend.clinics'))

@section('content')
    @include('frontend::components.section.breadcrumb')
    <div class="list-page section-spacing px-0">
        <div class="container">
            <div class="page-title" id="page_title">
                <!-- <h4 class="m-0 text-center">{{ __('frontend.clinics') }}</h4> -->
            </div>

            <div class="row gy-2">
                <div class="col-xl-4 col-md-5 filter">
                    <div class="d-flex flex-md-row flex-column gap-3 align-items-md-center">
                        <h6 class="m-0 flex-shrink-0">{{ __('frontend.filter_by') }}</h6>
                        <div class="row gx-3 gy-2  flex-grow-1">
                            <div class="col-sm-12">
                                <div class="form-group datatable-filter">
                                    <select name="service" id="service" class="form-select select2" data-filter="select">
                                        <option value="">{{ __('service.all') }} {{ __('service.title') }}</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-4 col-md-3 d-md-block d-none"></div>
                <div class="col-lg-3 col-md-4">
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
                        @include('frontend::components.card.shimmer_clinic_card')
                    @endfor
                </div>
                <table id="datatable" class="table table-responsive custom-card-table">
                </table>
            </div>
        </div>
    </div>
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
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

        document.addEventListener('DOMContentLoaded', (event) => {
            const searchInput = document.getElementById('datatable-search');
            const selectedFiltersDiv = document.getElementById('selected-filters');
            const serviceFilter = $('#service');

            serviceFilter.select2({
                placeholder: '{{ __('service.title') }}',
                allowClear: true,
                width: '100%'
            });

            function updateSelectedFilters() {
                selectedFiltersDiv.innerHTML = '';

                const selectedService = serviceFilter.find('option:selected');
                if (selectedService.val() && !selectedService.text().includes('All')) {
                    const filterBadge = document.createElement('span');
                    filterBadge.classList.add('filters-bagde', 'px-3', 'py-2');
                    filterBadge.textContent = selectedService.text();
                    filterBadge.style.backgroundColor = 'var(--bs-primary-bg-subtle)';
                    filterBadge.style.border = '1px solid var(--bs-primary)';
                    filterBadge.style.color = 'var(--bs-heading-color)';
                    filterBadge.style.borderRadius = 'var(--bs-border-radius-pill)';

                    const closeIcon = document.createElement('span');
                    closeIcon.classList.add('ms-3', 'fw-bold', 'text-primary', 'font-size-14');
                    closeIcon.innerHTML = '&times;';
                    closeIcon.style.cursor = 'pointer';
                    closeIcon.onclick = () => clearFilter('service');

                    filterBadge.appendChild(closeIcon);
                    selectedFiltersDiv.appendChild(filterBadge);
                }
            }

            function clearFilter(key) {
                if (key === 'service') {
                    serviceFilter.val('').trigger('change');
                }
                
                if (window.renderedDataTable) {
                    window.renderedDataTable.draw();
                }
            }

            serviceFilter.on('change', function() {
                updateSelectedFilters();
                if (window.renderedDataTable) {
                    window.renderedDataTable.draw();
                }
            });

            searchInput.addEventListener('input', function() {
                if (window.renderedDataTable) {
                    window.renderedDataTable.search(this.value).draw();
                }
            });

            const shimmerLoader = document.getElementById('shimmer-loader');
            const dataTableElement = document.getElementById('datatable');

            frontInitDatatable({
                url: '{{ route('clinic.index_data', ['service_id' => $service_id]) }}',
                finalColumns,
                advanceFilter: () => {
                    return {
                        service_id: serviceFilter.val(),
                    };
                },
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

            updateSelectedFilters();
        });
    </script>
@endpush