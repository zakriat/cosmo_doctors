@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
<div class="table-content mb-5">
        <x-backend.section-header>
            <div class="d-flex flex-wrap gap-3">
            @unless(auth()->user()->hasRole('doctor'))
                <div>
                    <button type="button" class="btn btn-primary" data-modal="export">
                    <i class="ph ph-export me-1"></i> {{ __('messages.export') }}
                    </button>
      {{--          <button type="button" class="btn btn-primary" data-modal="import">--}}
      {{--            <i class="ph ph-export me-1"></i> Import--}}
      {{--          </button>--}}
                  </div>
                  @endunless
            </div>
            <x-slot name="toolbar">

                
            @unless(auth()->user()->hasRole('doctor'))
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping"><i
                            class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search"
                        aria-describedby="addon-wrapping">
                </div>
                @endunless
                <!-- @hasPermission('add_doctors_session')
                <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('Create') }} {{ __($create_title) }}" class=" d-flex align-items-center gap-1">{{ __('messages.new') }}</x-buttons.offcanvas>
                @endhasPermission -->
            </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive">
        </table>
</div>
<div data-render="app">
    <doctor-session-clinic-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}"
    edit-title="{{ __('messages.edit') }} {{ __($module_title) }}" :customefield="{{ json_encode($customefield) }}"></doctor-session-clinic-offcanvas>
    <clinic-session-form-offcanvas></clinic-session-form-offcanvas>
</div>
@endsection


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
   const columns = [
        @unless(auth()->user()->hasRole('doctor'))
            // {
            //     name: 'check',
            //     data: 'check',
            //     title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
            //     width: '0%',
            //     exportable: false,
            //     orderable: false,
            //     searchable: false,
            // },
            {
                data: 'updated_at',
                name: 'updated_at',
                width: '15%',
                visible: false
            },
            {
                data: 'doctor_id',
                name: 'doctor_id',
                title: `{{ __('clinic.doctor_title') }}`,
                orderable: true,
                searchable: true,
            },
        @endunless
        @unless(auth()->user()->hasRole('receptionist'))
            {
                data: 'clinic_id',
                name: 'clinic_id',
                title: `{{ __('clinic.singular_title') }}`,
                orderable: false,
                searchable: true,
            },
            @endunless
            {
                data: 'day',
                name: 'day',
                title: "{{ __('clinic.working_day') }}",
                orderable: false,
                searchable: false,
            },

    ]

    const actionColumn = [{
       data: 'action',
       name: 'action',
        orderable: false,
        searchable: false,
        title: "{{ __('service.lbl_action') }}",
        width: '5%'
    }]
    const customFieldColumns = JSON.parse(@json($columns))

    let finalColumns = [
        ...columns,
        ...customFieldColumns,
        ...actionColumn
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
        initDatatable({
            url: '{{ route("backend.doctor-session.index_data") }}',
            finalColumns,
            orderColumn: [[ 0, "desc" ]],
            advanceFilter: () => {
                return {
                }
            }
        });
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
</script>
@endpush
