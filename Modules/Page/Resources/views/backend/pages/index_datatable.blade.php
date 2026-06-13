@extends('backend.layouts.app')

@section('title')
     {{ __($module_title) }}
@endsection


@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush
@section('content')
    <div class="table-content mb-5">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">

            <x-backend.quick-action url='{{ route("backend.$module_name.bulk_action") }}'>
            <div class="">
                <select name="action_type" class="select2 form-select col-12" id="quick-action-type"
                    style="width:100%">
                    <option value="">{{ __('messages.no_action') }}</option>
                    <option value="change-status">{{ __('messages.status') }}</option>
                    <option value="delete">{{ __('messages.delete') }}</option>
                </select>
            </div>
                <div class="select-status d-none quick-action-field" id="change-status-action">
                    <select name="status" class="select2 form-select" id="status" style="width:100%">
                        <option value="" selected>{{ __('messages.select_status') }}</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </x-backend.quick-action>
            <div>
            {{--    <button type="button" class="btn btn-primary" data-modal="export">
                <i class="ph ph-export me-1"></i> {{ __('messages.export') }}
                </button>
          <button type="button" class="btn btn-secondary" data-modal="import">--}}
    {{--            <i class="fa-solid fa-upload"></i> Import--}}
    {{--          </button>--}}
                </div>

            </div>
            <x-slot name="toolbar">

            <div>
                <div class="datatable-filter" style="width: 100%; display: inline-block;">
                    {{$filter['status']}}
                <select name="column_status" id="column_status" class="select2 form-select" data-filter="select" >
                    <option value="">{{__('messages.all')}}</option>
                    <option value="1" {{$filter['status'] == '1' ? "selected" : ''}}>Active</option>
                    <option value="0" {{$filter['status'] == '0' ? "selected" : ''}}>Inactive</option>
                </select>
                </div>
            </div>
                <div class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search" aria-describedby="addon-wrapping">
                </div>
                @hasPermission('add_page')
                    <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">{{ __('messages.new') }}</x-buttons.offcanvas>
                @endhasPermission
            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive">
        </table>
    </div>

    <div data-render="app">

        <page-offcanvas
            create-title="{{ __('messages.create') }} {{ __('messages.new') }} {{ __($module_title) }}"
            edit-title="{{ __('messages.edit') }} {{ __($module_title) }}">
        </page-offcanvas>

        <x-backend.advance-filter>
          <x-slot name="title"><h4>Advanced Filter</h4></x-slot>
          <select name="" id="" class="select2">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
          </select>
        </x-backend.advance-filter>
    </div>
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ mix('modules/page/script.js') }}"></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    <script src="{{ asset('js/form-modal/index.js') }}" defer></script>

    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>

const columns = [
            {
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
            },
            { data: 'name', name: 'name', title: "{{ __('page.lbl_title') }}" },
            { data: 'status', name: 'status', orderable: false, searchable: true,  title: "{{ __('page.lbl_status') }}" },
        ]

        const actionColumn = [
            { data: 'action', name: 'action', orderable: false, searchable: false, title: "{{ __('page.lbl_action') }}" }
        ]

        const customFieldColumns = JSON.parse(@json($columns))

        let finalColumns = [
            ...columns,
            ...customFieldColumns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
            })
        })
 
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


    function copyUrl(event, id) {

        event.preventDefault();
        var urlToCopy = document.getElementById("myLink_" + id).href;

        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = urlToCopy;

        document.body.appendChild(tempInput);


        tempInput.select();
        document.execCommand("copy");


        document.body.removeChild(tempInput);

        window.successSnackbar('Copy Page Url !')

    }
    </script>
@endpush
