@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="table-content mb-5">
        <!-- <x-backend.section-header>
            <x-slot name="toolbar"> 
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping"><i
                            class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control dt-search" placeholder="Search..." aria-label="Search"
                        aria-describedby="addon-wrapping">
                </div>
            </x-slot>
        </x-backend.section-header> -->

        <table id="datatable" class="table table-responsive">
        </table>
    </div>
    <div data-render="app">
        <view-commissions-offcanvas></view-commissions-offcanvas>
        <earning-form-offcanvas create-title="{{ __('Create') }} {{ __($module_title) }}"
            edit-title="{{ __('Create') }} {{ __('Staff Payout') }} " commission-type="vendor_commission"></earning-form-offcanvas>
    </div>
    
@endsection

@push ('after-styles')
<link rel="stylesheet" href="{{ mix('modules/earning/style.css') }}">
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push ('after-scripts')
<script src="{{ mix('modules/earning/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>

<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>
        const columns = [ 
            {
                data: 'user_id',
                name: 'user_id',
                title: "{{ __('earning.lbl_name') }}",
                orderable: true, 
                searchable: true
            },
            { 
                data: 'total_appointment', 
                name: 'total_appointment', 
                title: "{{ __('earning.lbl_tot_appointment') }}", 
                orderable: false,
                searchable: false
            },
            { 
                data: 'total_service_amount', 
                name: 'total_service_amount', 
                title: "{{ __('earning.lbl_total_earning') }}", 
                orderable: false,
                searchable: false
            },
            { 
                data: 'total_admin_earning', 
                name: 'total_admin_earning', 
                title: "{{ __('earning.lbl_admin_earnings') }}", 
                orderable: false, 
                searchable: false 
            },
            { 
                data: 'total_pay', 
                name: 'total_pay', 
                title: "{{ __('earning.lbl_vendor_earnings') }}",
                orderable: false,
                searchable: false
            },
            
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('service.lbl_update_at') }}",
                orderable: true,
                visible: false,
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
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                advanceFilter: () => {
                    return {
                    }
                }
            });
        })
</script>
@endpush
