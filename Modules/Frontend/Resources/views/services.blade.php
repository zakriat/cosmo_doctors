@extends('frontend::layouts.patient_layout')
@section('title', __('frontend.services'))

@section('content')
    @include('frontend::components.section.breadcrumb')

    <div class="list-page section-spacing px-0">
        <div class="movie-lists">
            <div class="container-fluid">
                <div class="row align-items-center gy-2">
                    <div class="col-xl-7 col-lg-9 filter">
                        <div class="d-flex flex-lg-row flex-column gap-3 align-items-lg-center">
                            <h6 class="m-0 flex-shrink-0">{{ __('frontend.filter_by') }}</h6>
                            <div class="row gx-3 gy-2  flex-grow-1">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group datatable-filter">
                                        <select name="clinic" id="clinic" class="form-select select2"
                                            data-filter="select">
                                            <option value="">{{ __('service.all') }} {{ __('service.lbl_clinic') }}
                                            </option>
                                            @foreach ($clinics as $clinic)
                                                <option value="{{ $clinic->id }}" 
                                                        @if(request()->get('clinic_id') == $clinic->id) selected @endif>
                                                    {{ $clinic->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <select name="price_range" id="price_range" class="form-select select2"
                                        data-filter="select">
                                        <option value="" disabled selected>{{ __('service.all') }}
                                            {{ __('service.lbl_price') }}</option>
                                        @foreach ($priceRanges as $index => $range)
                                            @if ($index === count($priceRanges) - 1)
                                                <option value="{{ $range[0] }}-{{ $range[1] }}">
                                                    {{ Currency::format($range[0]) }}+
                                                </option>
                                            @else
                                            <option value="{{ $range[0] }}-{{ $range[1] }}">
                                                {{ __('From') }} {{ Currency::format($range[0]) }}
                                                {{ __('to') }} {{ Currency::format($range[1]) }}
                                            </option>         
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="category" id="category" class="form-select select2" data-filter="select">
                                        <option value="">{{ __('service.all') }} {{ __('category.singular_title') }}
                                        </option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" data-parent="{{ $category->parent_id }}">
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                    </div>
                    <!-- Search Bar placed on the same line -->
                    <div class="col-xl-2 d-xl-block d-none"></div>
                    <div class="col-lg-3">
                        <div class="d-flex align-items-center p-2 rounded section-bg">
                            <div class="icon ps-2">
                                <i class="ph ph-magnifying-glass align-middle"></i> <!-- Font Awesome or Phosphor Icon -->
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
                            @include('frontend::components.card.shimmer_service_card')
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
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <!-- DataTables Core and Extensions -->
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
            
            // Use jQuery selectors for Select2
            const clinicSelect = $('#clinic');
            const priceSelect = $('#price_range');
            const categorySelect = $('#category');

            // Initialize Select2 for all filters
            clinicSelect.select2({
                placeholder: '{{ __('service.lbl_clinic') }}',
                allowClear: true,
                width: '100%'
            });

            priceSelect.select2({
                placeholder: '{{ __('service.lbl_price') }}',
                allowClear: true,
                width: '100%'
            });

            categorySelect.select2({
                placeholder: '{{ __('category.singular_title') }}',
                allowClear: true,
                width: '100%'
            });

            const filters = {
                clinic: '',
                price: '',
                category: ''
            };

            // Function to update the selected filters display
            function updateSelectedFilters() {
                selectedFiltersDiv.innerHTML = '';

                Object.keys(filters).forEach(key => {
                    if (filters[key] && !filters[key].startsWith('All ')) {
                        const filterBadge = document.createElement('span');
                        filterBadge.classList.add('filters-bagde', 'me-2');
                        filterBadge.textContent = filters[key];
                        filterBadge.style.backgroundColor = 'var(--bs-primary-bg-subtle)';
                        filterBadge.style.border = '1px solid var(--bs-primary)';
                        filterBadge.style.color = 'var(--bs-heading-color)';
                        filterBadge.style.borderRadius = 'var(--bs-border-radius-pill)';

                        const closeIcon = document.createElement('span');
                        closeIcon.classList.add('ms-3', 'fw-bold', 'text-primary', 'font-size-14');
                        closeIcon.innerHTML = '&times;';
                        closeIcon.style.cursor = 'pointer';
                        closeIcon.onclick = () => clearFilter(key);

                        filterBadge.appendChild(closeIcon);
                        selectedFiltersDiv.appendChild(filterBadge);
                    }
                });
            }

            // Function to clear a filter
            function clearFilter(key) {
                filters[key] = '';
                updateSelectedFilters();

                // Reset select2 when a filter is cleared
                if (key === 'clinic') clinicSelect.val('').trigger('change');
                if (key === 'price') priceSelect.val('').trigger('change');
                if (key === 'category') categorySelect.val('').trigger('change');

                // Update the URL to remove the filter
                updateUrlParameters();

                // Refresh the data table to show all data
                window.renderedDataTable.draw(); // This should refresh the table with all data
            }

            // Function to update URL parameters based on current filters
            function updateUrlParameters() {
                const url = new URL(window.location.href);
                Object.keys(filters).forEach(key => {
                    if (!filters[key]) {
                        url.searchParams.delete(key + '_id'); // Remove the parameter if filter is empty
                    } else {
                        url.searchParams.set(key + '_id', filters[key]); // Update the parameter
                    }
                });
                window.history.replaceState({}, '', url); // Update the URL without reloading
                window.renderedDataTable.draw();
            }

            // Unified filter change handler using Select2 events
            function handleFilterChange(select, key) {
                select.on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    // Only set the filter if it's not the default "All" option
                    filters[key] = selectedOption.val() ? selectedOption.text() : '';
                    updateSelectedFilters();
                    window.renderedDataTable.draw();
                });
            }
            

            // Attach event listeners for select filters
            handleFilterChange(clinicSelect, 'clinic');
            handleFilterChange(priceSelect, 'price');
            handleFilterChange(categorySelect, 'category');
                       
            // Search input handler
            searchInput.addEventListener('input', function() {
                window.renderedDataTable.search(this.value).draw();
            });

            const shimmerLoader = document.getElementById('shimmer-loader');
            const dataTableElement = document.getElementById('datatable');

            frontInitDatatable({
                url: '{{ route('service.index_data', ['doctor_id' => $doctor_id, 'clinic_id' => $clinic_id, 'category_id' => $category_id]) }}',
                finalColumns,
                advanceFilter: () => ({
                    clinic_id: clinicSelect.val(),
                    price: priceSelect.val(),
                    category_id: categorySelect.val(),
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

            // Check for category_id in the URL and set the filter
            const urlParams = new URLSearchParams(window.location.search);
            const categoryId = urlParams.get('category_id');
            if (categoryId) {
                categorySelect.val(categoryId).trigger('change'); // Set the category filter
            }
        });
    </script>
@endpush
