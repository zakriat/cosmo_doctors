@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ __($module_title) }} @endsection

@section('content')
<div class="table-content mb-3">
    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">
            <x-backend.quick-action url="{{ route('backend.incidence.bulk_action') }}">
                <div class="">
                    <select name="action_type" class="select2 form-select col-12" id="quick-action-type"
                        style="width:100%">
                        <option value="">{{ __('messages.no_action') }}</option>
                        <option value="change-status">{{ __('messages.status') }}</option>
                    </select>
                </div>
                <div class="select-status d-none quick-action-field" id="change-status-action">
                    <select name="status" class="select2 form-select" id="status" style="width:100%">
                        <option value="" selected>{{ __('messages.select_status') }}</option>
                        <option value="1">{{ __('messages.lbl_open') }}</option>
                        <option value="2">{{ __('messages.lbl_closed') }}</option>
                        <option value="3">{{ __('messages.lbl_reject') }}</option>
                    </select>
                </div>
            </x-backend.quick-action>
        </div>
        <x-slot name="toolbar">

            <div>
                <div class="datatable-filter">
                    <select name="column_status" id="column_status" class="select2 form-select"
                        data-filter="select" style="width: 100%">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="1">{{ __('messages.lbl_open') }}</option>
                        <option value="2">{{ __('messages.lbl_closed') }}</option>
                        <option value="3">{{ __('messages.lbl_reject') }}</option>
                    </select>
                </div>
            </div>

            <div class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping"><i
                        class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search"
                    aria-describedby="addon-wrapping">
            </div>

                    <!-- <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">
                    {{ __('messages.create') }} {{ __($module_title) }} </x-buttons.offcanvas> -->

        </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-responsive">
    </table>
</div>
<div data-render="app">
    <clinic-appointment-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}"
        edit-title="{{ __('messages.edit') }} {{ __($module_title) }}">
    </clinic-appointment-offcanvas>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{ __('messages.modal_title') }}</h5>
        <button type="button" class="close" onclick="replyPopupClose()" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div class="row mt-2">
            <div class="col">

                {{ html()->form('POST', route('backend.incidence.reply'))->class('form-horizontal')->open() }}
                {{ csrf_field() }}

                <div class="row align-items-center gy-2 mb-4">
                    <input type="hidden" name="incidence_id" id="incidence_id" value="">
                    <?php
                    $field_name = 'Reply';
                    $field_lable = 'Reply';
                    $field_placeholder = __('messages.type_reply');
                    $required = "required";
                    ?>
                    <div class="col-12 col-sm-2">
                        <div class="form-group mb-0">
                            <label>{{__('messages.lbl_reply')}}<span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-10">
                        <div class="form-group mb-0">
                            {{ html()->textarea($field_name)->placeholder($field_placeholder)->class('form-control')->rows(4)->attributes(["$required"]) }}
                        </div>
                    </div>
                </div>
                @if($data->incident_type==1)
                <div class="d-flex align-items-center justify-content-end">
                    <x-buttons.create title="">
                        {{__('messages.lbl_reply')}}
                    </x-buttons.create>
                </div>
                @endif
                {{ html()->form()->close() }}
            </div>
        </div>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="replyPopupClose()" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> -->
    </div>
  </div>
</div>

<!-- Modal for Description -->
<div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-break" id="descriptionModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="descriptionModalBody" class="text-break" style="white-space: pre-line;"></div>
      </div>
    </div>
  </div>
</div>

@endsection


@if(session('success'))
<div class="snackbar" id="snackbar">
    <div class="d-flex justify-content-around align-items-center">
        <p class="mb-0">{{ session('success') }}</p>
        <a href="#" class="dismiss-link text-decoration-none text-success" onclick="dismissSnackbar(event)">Dismiss</a>
    </div>
</div>
@endif
@if (session('error'))
    <div class="snackbar" id="snackbar">
        <div class="d-flex justify-content-around align-items-center">
            <p class="mb-0">{{ session('error') }}</p>
            <a href="#" class="dismiss-link text-decoration-none text-white ms-3" onclick="dismissSnackbar(event)">Dismiss</a>
        </div>
    </div>
@endif
@push ('after-styles')
{{-- <link rel="stylesheet" href="{{ mix('modules/world/style.css') }}"> --}}
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push ('after-scripts')
<script src="{{ mix('modules/clinic/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>

<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>
   const columns = [{
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
            },

              {
        data: 'image',
        name: 'image',
        title: "{{ __('messages.image') }}",
        searchable: false,
        orderable: false,
    },


     {
        data: 'name',
        name: 'name',
        title: "{{ __('messages.name') }}"
    },
    {
        data: 'title',
        name: 'title',
        title: "{{ __('messages.field_title') }}",
    },
    {
        data: 'description',
        name: 'description',
        title: "{{ __('messages.field_description') }}",
        createdCell: function (td, cellData, rowData, row, col) {
                $(td).addClass('description-column');
            },
            render: function (data, type, row, meta) {
                return '<span class="custom-span-class">' + data + '</span>';
            }
    },
    {
        data: 'phone',
        name: 'phone',
        title: "{{ __('messages.field_phone_number') }}",
        searchable: false,
    },
    {
        data: 'email',
        name: 'email',
        title: "{{ __('messages.field_emailid') }}",
    },
    {
        data: 'incident_date',
        name: 'incident_date',
        title: "{{ __('messages.date') }}",
        searchable: false,
    },
    {
        data: 'status',
        name: 'status',
        searchable: true,
        title: "{{ __('messages.status') }}",
        width: '5%'
    },
    {
        data: 'updated_at',
        name: 'updated_at',
        title: "{{ __('messages.updated_at') }}",
        searchable: false,
        visible: false
    }

    ]

    const actionColumn = [{
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        title: "{{__('messages.action')}}",
        width: '5%'
    }]

    let finalColumns = [
        ...columns,
        ...actionColumn
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
        initDatatable({
            url: '{{ route("backend.incidence.index_data") }}',
            finalColumns,
            orderColumn: [[ 9, "desc" ]],
            advanceFilter: () => {
                return {
                }
            }
        });

        // ✅ Initialize Select2 on page load
        $('.select2').select2({
            width: '100%'
        });

        // ✅ Re-initialize Select2 after DataTable redraw
        $(document).on('draw.dt', '#datatable', function () {
            $('.select2').select2({
                width: '100%'
            });
        });

        // ✅ Handle Select2 change events for incidence status updates
        $(document).on('select2:select', '.change-select', function (e) {
            let url = $(this).attr('data-url');
            let body = {
                value: $(this).val(),
                _token: $(this).attr('data-token')
            };
            
            $.ajax({
                type: 'POST',
                url: url,
                data: body,
                success: function(res) {
                    if (res.status) {
                        window.successSnackbar(res.message);
                        $('#datatable').DataTable().ajax.reload();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: res.message,
                            icon: "error",
                            showClass: {
                                popup: 'animate__animated animate__zoomIn'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__zoomOut'
                            }
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Something went wrong.',
                        icon: "error"
                    });
                }
            });
        });
    })

    window.setPreview = function(fileUrl) {
        window.open(fileUrl, '_blank');
    }
    function resetQuickAction () {
    const actionValue = $('#quick-action-type').val();
        if (actionValue != '') {
            $('#quick-action-apply').removeAttr('disabled');

            if (actionValue == 'change-status') {
                $('.quick-action-field').addClass('d-none');
                $('#change-status-action').removeClass('d-none');
            } else {
                $('.quick-action-field').addClass('d-none');
            }
        } else {
            $('#quick-action-apply').attr('disabled', true);
            $('.quick-action-field').addClass('d-none');
        }
        }

        $('#quick-action-type').change(function () {
            resetQuickAction()
        });

        $(document).on('update_quick_action', function() {
            // resetActionButtons()
        })
</script>

<script>
    function replyPopup(id)
    {
        $('#incidence_id').val(id);
        $('textarea[name="Reply"]').val('');

        var url = "{{ route('backend.incidence.getReply', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.reply) {
                    $('textarea[name="Reply"]').val(response.reply);
                }
                $("#exampleModal").modal('show');
            },
            error: function() {
                alert('Something went wrong while fetching reply.');
                $("#exampleModal").modal('show');
            }
        });
    }

    function replyPopupClose()
    {
        $("#exampleModal").modal('hide');
    }

    $(document).on('click', '.view-description', function () {
        const title = $(this).data('title');
        const description = $(this).data('description');

        $('#descriptionModalLabel').text(title);
        $('#descriptionModalBody').text(description);
        $('#descriptionModal').modal('show');
    });
</script>
@endpush

