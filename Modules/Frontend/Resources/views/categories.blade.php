@extends('frontend::layouts.master')
@section('title', __('frontend.categories'))

@section('content')
    @include('frontend::components.section.breadcrumb')
    <div class="list-page section-spacing px-0">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between column-gap-5 row-gap-3 flex-wrap">
                <div class="page-title" id="page_title">
                    <h6 class="m-0">{{ __('service.all') }} {{ __('frontend.categories') }}</h6>
                </div>
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
            <div class="card-style-slider">
                <div id="shimmer-loader" class="d-flex gap-3 flex-wrap p-4">
                    @for ($i = 0; $i < 8; $i++)
                        @include('frontend::components.card.shimmer_category_card')
                    @endfor
                </div>
                <table id="datatable" class="table table-responsive custom-card-table">
                </table>
                {{-- <div class="row gy-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 mt-3">
                @foreach ($categories as $category)
                    <div class="col">
                        <x-frontend::card.category_card :category="$category" />
                    </div>
                @endforeach --}}
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
        }]

        document.addEventListener('DOMContentLoaded', (event) => {
            const searchInput = document.getElementById('datatable-search');
            // Trigger search when input changes
            searchInput.addEventListener('input', function() {
                window.renderedDataTable.search(this.value).draw();
            });
            const shimmerLoader = document.getElementById('shimmer-loader');
            const dataTableElement = document.getElementById('datatable');
            frontInitDatatable({
                url: '{{ route('category.index_data') }}',
                finalColumns,
                cardColumnClass: 'row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5',
                onLoadStart: () => {
                    // Show shimmer loader before loading data
                    shimmerLoader.classList.remove('d-none');
                    dataTableElement.classList.add('d-none');
                },
                onLoadComplete: () => {
                    shimmerLoader.classList.add('d-none');
                    dataTableElement.classList.remove('d-none');
                },
            });
        })
    </script>
@endpush
