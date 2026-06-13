<form id="soap-form">
    <p class="mt-2 mb-4 ">
        <span class="fw-bold">{{ __('messages.note') }}: </span>{{ __('messages.soap_description') }}
    </p>
    <div class="row">

        @csrf
        {{-- <input type="hidden" name="id" id="id" value="{{ $data['id'] }}"> --}}
        <input type="hidden" name="appointment_id" value="{{ $data['appointment_id'] }}">
        <input type="hidden" name="encounter_id" value="{{ $data['id'] }}">
        <input type="hidden" name="patient_id" value="{{ $data['user_id'] }}">

        <!-- Subjective -->
        <div class="mb-3">
            <label for="subjective" class="form-label">{{ __('messages.subjective') }} <span
                    class="text-danger">*</span></label>
            <textarea id="subjective" name="subjective" rows="3" cols="12" class="form-control"></textarea>
            <span class="text-danger" id="subjective-error"></span>
        </div>

        <!-- Objective -->
        <div class="mb-3">
            <label for="objective" class="form-label">{{ __('messages.objecvtive') }} <span
                    class="text-danger">*</span></label>
            <textarea id="objective" name="objective" rows="3" cols="12" class="form-control"></textarea>
            <span class="text-danger" id="objective-error"></span>
        </div>

        <!-- Assessment -->
        <div class="mb-3">
            <label for="assessment" class="form-label">{{ __('messages.assesment') }} <span
                    class="text-danger">*</span></label>
            <textarea id="assessment" name="assessment" rows="3" cols="12" class="form-control"></textarea>
            <span class="text-danger" id="assessment-error"></span>
        </div>

        <!-- Plan -->
        <div class="mb-3">
            <label for="plan" class="form-label">{{ __('messages.plan') }} <span
                    class="text-danger">*</span></label>
            <textarea id="plan" name="plan" rows="3" cols="12" class="form-control"></textarea>
            <span class="text-danger" id="plan-error"></span>
        </div>
    </div>

    <!-- Buttons -->
    @if ($data['status'] != 0)
    <div class="d-grid d-md-flex gap-3 p-3 justify-content-end">
        <button id="submit-button" class="btn btn-secondary" type="submit">
            {{ __('messages.save') }}
        </button>
    </div>
    @endif
</form>

@push('after-scripts')
    <script>
        $(document).ready(function() {
            var baseUrl = '{{ url('/') }}';
            let encounterId = $("input[name='encounter_id']").val();

            // Fetch existing data on page load
            if (encounterId) {
                let fetchUrl = `${baseUrl}/app/appointments/${encounterId}/appointment_patient_data`;

                $.ajax({
                    url: fetchUrl,
                    method: 'GET',
                    success: function(response) {
                        // console.log('Data fetched successfully:', response);

                        // Populate form fields with the fetched data
                        if (response && response.data) {
                            $('#subjective').val(response.data.subjective || '');
                            $('#objective').val(response.data.objective || '');
                            $('#assessment').val(response.data.assessment || '');
                            $('#plan').val(response.data.plan || '');
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            } else {
                console.error("Encounter ID is missing or invalid.");
            }

            // Hide error message on input change
            $('#subjective, #objective, #assessment, #plan').on('input', function() {
                $(this).next('.text-danger').text('');
            });

            // Submit form
            $('#soap-form').submit(function(e) {
                e.preventDefault(); // Prevent default form submission

                // Clear previous validation messages
                $('.text-danger').text('');

                // Client-side validation
                let hasError = false;

                // Check required fields
                if (!$('#subjective').val().trim()) {
                    $('#subjective-error').text('Subjective field is required.');
                    hasError = true;
                }
                if (!$('#objective').val().trim()) {
                    $('#objective-error').text('Objective field is required.');
                    hasError = true;
                }
                if (!$('#assessment').val().trim()) {
                    $('#assessment-error').text('Assessment field is required.');
                    hasError = true;
                }
                if (!$('#plan').val().trim()) {
                    $('#plan-error').text('Plan field is required.');
                    hasError = true;
                }

                // If validation fails, stop submission
                if (hasError) {
                    return;
                }

                let formData = $(this).serialize();

                let csrfToken = $('meta[name="csrf-token"]').attr('content');


                // Submit data
                if (encounterId) {
                    let submitUrl = `${baseUrl}/app/appointments/appointment_patient/${encounterId}`;
                    // console.log("Submit URL:", submitUrl);

                    $.ajax({
                        url: submitUrl,
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            // console.log('Data uploaded successfully:', response);
                            window.successSnackbar(response.message)

                        },
                        error: function(error) {
                            console.error('Error submitting data:', error);
                            window.errorSnackbar(response.message)

                        }
                    });
                } else {
                    console.error("Encounter ID is missing or invalid.");
                }
            });
        });
    </script>
@endpush
