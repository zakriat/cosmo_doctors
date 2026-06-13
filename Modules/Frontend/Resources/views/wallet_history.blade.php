@extends('frontend::layouts.master')

@section('title', __('frontend.wallet_history'))
@section('content')
@include('frontend::components.section.breadcrumb')

<div class="list-page section-spacing px-0">
    <div class="page-title" id="page_title">
        <div class="container">
            <div class="tab-content mt-5">
                <div class="tab-pane active p-0" id="wallet-history">
                    <ul class="list-inline m-0">
                        <div id="shimmer-loader" class="d-flex gap-3 flex-wrap p-4 shimmer-loader">
                            @for ($i = 0; $i < 6; $i++)
                               @include('frontend::components.card.shimmer_wallet_card') 
                            @endfor
                        </div>
                        <table id="datatable" class="table table-responsive custom-card-table">
                        </table>
                    </ul>
                </div>
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
            const shimmerLoader = document.getElementById('shimmer-loader');
            const dataTableElement = document.getElementById('datatable');
            frontInitDatatable({
                url: '{{ route('wallet-history.index_data') }}',
                finalColumns,
                cardColumnClass: 'row-cols-1',
                onLoadStart: () => {
                    shimmerLoader.classList.remove('d-none');
                    dataTableElement.classList.add('d-none');
                },
                onLoadComplete: () => {
                    shimmerLoader.classList.add('d-none');
                    dataTableElement.classList.remove('d-none');
                },
            });
        });
    </script>
@endpush
