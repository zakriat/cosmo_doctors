<div class="table-responsive rounded mb-0 mx-3">
    <table class="table table-lg m-0" id="prescription_table">
        <thead>

            <tr class="text-white">
                <th scope="col">{{ __('appointment.name') }}</th>
                <th scope="col">{{ __('appointment.frequency') }}</th>
                <th scope="col">{{ __('appointment.duration') }}</th>
                @if ($data['status'] == 1)
                    <th scope="col">{{ __('appointment.action') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>

            @foreach ($data['prescriptions'] as $index => $prescription)
                <tr>
                    <td>
                        <p class="m-0">
                            {{ $prescription['name'] }}
                        </p>
                        <p class="m-0">
                            {{ $prescription['instruction'] }}
                        </p>
                    </td>
                    <td>
                        {{ $prescription['frequency'] }}
                    </td>
                    <td>
                        {{ $prescription['duration'] }}
                    </td>
                    @if ($data['status'] == 1)
                        <td class="action">
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" class="btn text-primary p-0 fs-5 me-2" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal" onclick="editPrescription({{ $prescription['id'] }})"
                                    aria-controls="form-offcanvas">
                                    <i class="ph ph-pencil-simple-line"></i>
                                </button>
                                <button type="button" class="btn text-danger p-0 fs-5"
                                    onclick="destroyData({{ $prescription['id'] }}, 'Are you sure you want to delete it?')"
                                    data-bs-toggle="tooltip">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach


            @if (count($data['prescriptions']) <= 0)
                <tr>
                    <td colspan="5">
                        <div class="my-1 text-danger text-center">{{ __('appointment.no_prescription_found') }}
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- @if (count($data['prescriptions']) > 0)
        <button id="printButton" class="btn btn-sm btn-primary" onclick="DownloadPDF({{ $data['id'] }})">
            <i class="ph ph-file-text me-1"></i>
            {{ __('appointment.lbl_download') }}
        </button>

        <button class="btn btn-sm btn-primary" onclick="sendPrescription(this, {{ $data['id'] }})">
            <div class="d-inline-flex align-items-center gap-1">
                <i class="ph ph-paper-plane-tilt" id="send_mail"></i>
                {{ __('appointment.email') }}
            </div>
        </button>
    @endif --}}
</div>

@push('after-scripts')
    <script>
        var baseUrl = '{{ url('/') }}';

        function destroyData(id, message) {

            confirmDeleteSwal({
                message
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.ajax({
                    url: baseUrl + '/app/encounter-template/delete-prescription/' + id,
                    type: 'GET',
                    success: (response) => {
                        if (response.html) {

                            $('#prescription_table').html(response.html);

                            Swal.fire({
                                title: 'Deleted',
                                text: response.message,
                                icon: 'success',
                                showClass: {
                                    popup: 'animate__animated animate__zoomIn'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__zoomOut'
                                }
                            });
                        } else {

                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Failed to delete the prescription.',
                                icon: 'error',
                                showClass: {
                                    popup: 'animate__animated animate__shakeX'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOut'
                                }
                            });
                        }
                    }
                });
            });
        }


        function editPrescription(id) {
            $.ajax({
                url: baseUrl + '/app/encounter-template/edit-prescription/' + id,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {

                    if (response.status) {

                        $('#name').val(response.data.name);
                        $('#frequency').val(response.data.frequency);
                        $('#duration').val(response.data.duration);
                        $('#instruction').val(response.data.instruction);

                        $('#id').val(response.data.id);
                        $('#user_id').val(response.data.user_id);
                        $('#encounter_id').val(response.data.encounter_id);


                        $('#addprescriptiontemplae').modal('show');
                    } else {
                        alert(response.message || 'Failed to load prescription details.');
                    }
                },
                error: (xhr, status, error) => {
                    console.error(error);
                    alert('An unexpected error occurred.');
                }
            });
        }


        function sendPrescription(button, id) {

            $(button).prop('disabled', true).html(`
        <div class="d-inline-flex align-items-center gap-1">
            <i class="ph ph-spinner ph-spin"></i>
            Sending...
        </div>
    `);

            $.ajax({
                url: baseUrl + '/app/encounter/send-prescription?id=' + id,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    if (response.status) {
                        window.successSnackbar(response.message);
                    } else {
                        window.errorSnackbar('Something went wrong! Please check.');
                    }
                    // Re-enable the button and reset its text
                    $(button).prop('disabled', false).html(`
                <div class="d-inline-flex align-items-center gap-1">
                    <i class="ph ph-paper-plane-tilt"></i>
                    {{ __('appointment.email') }}
                </div>
            `);
                },
                error: (xhr, status, error) => {
                    console.error(error);
                    window.errorSnackbar('Something went wrong! Please check.');
                    // Re-enable the button and reset its text
                    $(button).prop('disabled', false).html(`
                <div class="d-inline-flex align-items-center gap-1">
                    <i class="ph ph-paper-plane-tilt"></i>
                    {{ __('appointment.email') }}
                </div>
            `);
                }
            });
        }
    </script>
@endpush
