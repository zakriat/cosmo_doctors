<div class="table-responsive rounded mb-0">
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
                        @if($prescription->medicine)
                            <small class="text-muted">
                                @if($prescription->medicine->generic_name)
                                    {{ $prescription->medicine->generic_name }}
                                @endif
                                @if($prescription->medicine->strength)
                                    ({{ $prescription->medicine->strength }})
                                @endif
                                @if($prescription->medicine->dosage_form)
                                    - {{ ucfirst($prescription->medicine->dosage_form) }}
                                @endif
                            </small>
                        @endif
                        @if($prescription['instruction'])
                            <br><small class="text-info">{{ $prescription['instruction'] }}</small>
                        @endif
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
        <div class="my-3"></div> <!-- Added space between table and buttons -->
        @if (count($data['prescriptions']) > 0)
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
        @endif

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
                    url: baseUrl + '/app/encounter/delete-prescription/' + id,
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
                url: baseUrl + '/app/encounter/edit-prescription/' + id,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {

                    if (response.status) {

                        $('#prescription_name').val(response.data.name);
                        $('#frequency').val(response.data.frequency);
                        $('#duration').val(response.data.duration);
                        $('#instruction').val(response.data.instruction);
                        
                        // Handle medicine selection for edit
                        if (response.data.medicine_id) {
                            $('#medicine_select').val(response.data.medicine_id).trigger('change');
                            $('#medicine_id').val(response.data.medicine_id);
                        } else {
                            $('#medicine_select').val('').trigger('change');
                            $('#medicine_id').val('');
                        }

                        $('#id').val(response.data.id);
                        $('#user_id').val(response.data.user_id);
                        $('#encounter_id').val(response.data.encounter_id);


                        $('#addprescription').modal('show');
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



        function DownloadPDF(id) {
            const baseUrl = '{{ url('/') }}'; // Base URL of your application
            const downloadUrl = `${baseUrl}/app/encounter/download-prescription?id=${id}`;

            // Sending the request to fetch the download link
            $.ajax({
                url: downloadUrl,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    if (response.status && response.link) {
                        // Create a temporary link element
                        const link = document.createElement('a');
                        link.href = response.link; // The URL from the response
                        link.target = '_blank'; // Open in a new tab
                        link.download = ''; // Optional: Set a filename (the server usually provides it)
                        document.body.appendChild(link); // Append link to the body
                        link.click(); // Trigger the download
                        document.body.removeChild(link); // Remove link after downloading
                    } else {
                        alert('Failed to get the download link.');
                    }
                },
                error: (xhr, status, error) => {
                    console.error(error);
                    alert('An error occurred while trying to download the PDF.');
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
