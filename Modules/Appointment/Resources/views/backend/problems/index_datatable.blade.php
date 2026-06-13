@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
<div class="table-content mb-5">
    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">
            @if(auth()->user()->can('delete_encounter') )
            <x-backend.quick-action url="{{ route('backend.problems.bulk_action') }}">
                <div class="">
                    <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                        <option value="">{{ __('messages.no_action') }}</option>
                        @can('delete_encounter')
                        <option value="delete">{{ __('messages.delete') }}</option>
                        @endcan
                    </select>
                </div>
            </x-backend.quick-action>
            @endif
            <div>
                <button type="button" class="btn btn-primary" data-modal="export">
                <i class="ph ph-export me-1"></i> {{ __('messages.export') }}
                </button>
                {{-- <button type="button" class="btn btn-secondary" data-modal="import">--}}
                {{-- <i class="fa-solid fa-upload"></i> Import--}}
                {{-- </button>--}}
            </div>
        </div>
        <x-slot name="toolbar">

            <div class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search" aria-describedby="addon-wrapping">
            </div>
            <!-- <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" id="filter-btn">
                <i class="ph ph-funnel"></i>{{ __('messages.advance_filter') }}
            </button> -->

             
          {{--  @hasPermission('add_encounter')
            <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">
            {{ __('messages.new') }}</x-buttons.offcanvas>
            @endhasPermission --}}
            
        </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-responsive">
    </table>
</div>
<div data-render="app">

    <problems-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}" edit-title="{{ __('messages.edit') }} {{ __($module_title) }}">
    </problems-offcanvas>



</div>
<x-backend.advance-filter>
    <x-slot name="title">
        <h4>{{ __('service.lbl_advanced_filter') }}</h4>
    </x-slot>
    <div class="form-group datatable-filter">
        <label class="form-label" for="template_name">{{ __('appointment.lbl_name') }}</label>
        <select name="template_name" id="template_name" class="select2 form-select" data-filter="select">
            <option value="">{{ __('appointment.lbl_name') }}</option>
        </select>
    </div>
    <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('appointment.reset') }}</button>
</x-backend.advance-filter>
@endsection

@push ('after-styles')
<link rel="stylesheet" href="{{ mix('modules/appointment/style.css') }}">
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push ('after-scripts')
<script src="{{ mix('modules/appointment/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>

<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>
    const columns = [
        @if(!auth()->user()->hasRole('receptionist'))
        {
            name: 'check',
            data: 'check',
            title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
            width: '0%',
            exportable: false,
            orderable: false,
            searchable: false,
        },
        @endif

        {
            data: 'name',
            name: 'name',
            title: "{{ __('appointment.lbl_name') }}"
        },
         
        {
            data: 'updated_at',
            name: 'updated_at',
            title: "{{ __('appointment.lbl_update_at') }}",
            orderable: true,
            visible: false,
        },


    ]


    const actionColumn = [
        @if(!auth()->user()->hasRole('receptionist'))
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('appointment.lbl_action') }}",
            width: '5%'
        }
        @endif
    ]



    let finalColumns = [
        ...columns,
        ...actionColumn
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
        initDatatable({
            url: '{{ route("backend.$module_name.index_data") }}',
            finalColumns,
            orderColumn: @if(auth()->user()->hasRole('receptionist'))
                            [[1, "desc"]]
                        @else
                            [[2, "desc"]]
                        @endif,
            advanceFilter: () => {
                return {
                    template_name: $('#template_name').val()
                }
            }
        });
        $('#reset-filter').on('click', function(e) {
            $('#template_name').val('');
            window.renderedDataTable.ajax.reload(null, false);
        });
    })


    function resetQuickAction() {
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

        $('#quick-action-type').change(function() {
            resetQuickAction()
        });

        document.getElementById('filter-btn').addEventListener('click', function() {
            // Ensure that the filter type is correctly set
            const type = 'problemFilter'; // or 'observationFilter'
            const url = `{{ route('advance_filter') }}?type=${type}`; // Append the filter type as a query parameter

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const selectElement = document.getElementById('template_name');
                    selectElement.innerHTML = '<option value="">{{ __('appointment.lbl_name') }}</option>'; // Reset options

                    // Check if the data contains items
                    const items = data.data || [];
                    items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id; // Adjust if the key names are different
                        option.textContent = item.name; // Adjust if the key names are different
                        selectElement.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching advanced filter data:', error));
        });

</script>
@endpush
