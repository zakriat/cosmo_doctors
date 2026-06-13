
    <x-backend.section-header>

        @if($data['status'] == 1)
            <x-backend.quick-action url='{{ route("backend.bodychart.bodychart_bulk_action") }}'>
                <div class="">
                    <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                        <option value="">{{ __('messages.no_action') }}</option>
                        <option value="delete">{{ __('messages.delete') }}</option>
                    </select>
                </div>

            </x-backend.quick-action>


        <x-slot name="toolbar">
            <!-- <a href="{{ route("backend.patient-record", ['id' => $data['appointment_id']]) }}"" class=" btn btn-primary" data-type="ajax" data-bs-toggle="tooltip">Back</a> -->

            <div class="d-flex justify-content-end">
                <a href="{{route("backend.bodychart.bodychart_form", $data['id'])}}" class="btn btn-primary" data-type="ajax" data-bs-toggle="tooltip"><i class="fas fa-plus-circle"></i> Add Body Chart</a>

                {{-- <a class="btn btn-primary" onclick="addBodychart()" data-type="ajax" data-bs-toggle="tooltip"><i class="fas fa-plus-circle"></i> Add BodyChart</a> --}}

            </div>
        </x-slot>
        @endif
    </x-backend.section-header>



    <table id="datatable" class="table table-responsive">
    </table>




@push ('after-styles')
<link rel="stylesheet" href="{{ mix('modules/appointment/style.css') }}">
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">

<script>


</script>
@endpush

@push ('after-scripts')
<script src="{{ mix('modules/appointment/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>


<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>
    const columns = [
        @if( $data['status'] == 1)
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
            data: 'id',
            name: 'id',
            title: "{{ __('appointment.lbl_id') }}",
            searchable: false,
            orderable: true,
        },
        {
            data: 'name',
            name: 'name',
            title: "{{ __('appointment.lbl_bodychart_name') }}",
            orderable: true
        },
        {
    data: 'description',
    name: 'description',
    title: "{{ __('appointment.lbl_bodychart_description') }}",
    render: function (data, type, row, meta) {
        return `<p class="table-description-short">${data}</p>`;
    }
},
        {
            data: 'patient_id',
            name: 'patient_id',
            title: "{{ __('appointment.lbl_patient_name') }}",
        },


        {
            data: 'doctor_name',
            name: 'doctor_name',
            title: "{{ __('appointment.lbl_doctor') }}",
            orderable: true,
            searchable: true,
            width: '5%'
        },

      

    ]


    const actionColumn = [
       
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('appointment.lbl_action') }}",
            width: '5%'
        }
      
    ]



    let finalColumns = [
        ...columns,
        ...actionColumn
    ]

    document.addEventListener('DOMContentLoaded', (event) => {
        initDatatable({
            url: '{{ route("backend.bodychart.bodychart_datatable",$data['id']) }}',
            finalColumns,
            advanceFilter: () => {
                return {}
            }
        });
    })


    function reloadDataTable() {
            const dataTable = $('#datatable').DataTable();
            if (dataTable) {
                dataTable.ajax.reload();
            }
        }


function addBodychart() {

    document.getElementById('body_chart_list').classList.add('d-none');

    document.getElementById('add_body_chart').classList.remove('d-none');
}



function Editbodychart(bodychart_id) {


var baseUrl = '{{ url('/') }}';

document.getElementById('body_chart_list').classList.add('d-none');

// Perform the AJAX request
$.ajax({
    url: `${baseUrl}/app/bodychart/get-bodychart-details/${bodychart_id}`,
    method: 'GET',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
        if (response) {

            console.log(response.html)

            $('#add_body_chart').html(response.html);
            if (typeof Vue !== 'undefined') {
                Vue.nextTick(() => {
                    new Vue({
                          el: '#body-chart-offcanvas',
                     });
                });
            }
        } else {
            $('#add_body_chart').html('<p>Error: No content available.</p>');
        }
    },
    error: function(xhr, status, error) {
        console.error('Error fetching body chart details:', error);
        $('#add_body_chart').html('<p>Error loading data. Please try again.</p>');
    }
});


}












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

    $(document).on('update_quick_action', function() {
        // resetActionButtons()
    })
</script>
@endpush
