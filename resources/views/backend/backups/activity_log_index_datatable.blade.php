@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
    <div class="table-content mb-3">

        <table id="datatable" class="table table-responsive"></table>
    </div>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ __('messages.lbl_history') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="view-data">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.close') }}</button>
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
    <script type="text/javascript">
        const columns = [{
                data: 'created_at',
                name: 'created_at',
                title: '{{ __('messages.created_at') }}',
                orderable: false,
            },
            {
                data: 'description',
                name: 'description',
                title: "{{ __('clinic.lbl_description') }}",
                orderable: false,
            },
            {
                data: 'subject_type',
                name: 'subject_type',
                title: '{{ __('messages.subject_type') }}',
                orderable: false,
            },
            {
                data: 'updated_at',
                name: 'updated_at',
                width: '15%',
                visible: false
            },
        ];
        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: '{{ __('service.lbl_action') }}',
            width: '5%'
        }];
        let finalColumns = [
            ...columns,
            ...actionColumn
        ];
        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route('backend.backups.activity_log_index_data') }}',
                finalColumns,
                orderColumn: [
                    [3, 'desc']
                ],
                advanceFilter: () => {
                    return {};
                }
            });
        });
    </script>
    <script>
        function getHistory(id) {
            if (id != "") {
                var url = "{{ route('backend.backups.logs.view', ':id') }}";
                url = url.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#view-data').html(response);
                        $("#staticBackdrop").modal('show');
                    },
                    error: function(response) {
                        alert('error');
                    }
                });
            }
        }
    </script>
@endpush
