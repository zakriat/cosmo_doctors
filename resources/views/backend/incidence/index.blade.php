@extends('backend.layouts.app')

@section('title', __($module_title))

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush

@section('content')
    <div class="card mb-4">
        <div class="card-body p-0">

            <div class="row">
                <div class="col">

                    <table id="datatable" class="table table-responsive p-bk-table">
                        <thead>
                            <tr>
                                <th>
                                    @lang('Created Date')
                                </th>
                                <th>
                                    @lang('Description')
                                </th>
                                <th>
                                    @lang('Subject Type')
                                </th>
                                <th>
                                    @lang('Action')
                                </th>                                
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($$module_name as $module_name_singular)

                                <tr class="">
                                    <td>
                                        {{ date("Y-d-m H:i:s",strtotime($module_name_singular['created_at'])) }}
                                    </td>
                                    <td>
                                        {{ ucfirst($module_name_singular['description']) }}
                                    </td>
                                    <td>
                                        {{ $module_name_singular['subject_type'] }}
                                    </td>
                                    <td>                                    
                                        <a style="cursor:pointer"
                                            onclick="getHistory({{$module_name_singular->id}})">
                                            <i class="ph ph-eye align-middle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; vertical-align: middle;">
                                        {{ __('messages.No_data_found') }}
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-7">                   
                    <form id="paginationForm" method="GET" action="{{ url()->current() }}" class="d-inline">
                        <label for="perPageSelect" class="me-2">Show</label>
                        <select name="per_page" id="perPageSelect" class="form-select d-inline-block w-auto"
                            onchange="document.getElementById('paginationForm').submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="ms-2">entries</span>
                    </form>
                    Showing {{ $$module_name->firstItem() }} to {{ $$module_name->lastItem() }} of
                    {{ $$module_name->total() }} entries
                </div>
                <div class="col-5">
                    <div class="float-end">
                        {!! $$module_name->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="view-data">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>                
                </div>
            </div>
        </div>
    </div>

    @push('after-scripts')
        <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
        <script src="{{ mix('js/vue.min.js') }}"></script>
        <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
        <script>
            function getHistory(id)
            {                
                if(id != "")
                {
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
@endsection
