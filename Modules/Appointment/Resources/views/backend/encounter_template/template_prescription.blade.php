<div class="modal fade" id="addprescriptiontemplae" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('clinic.add_prescription') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <input type="hidden" name="encounter_id" id="problem_encounter_id" value="{{ $data['id'] }}">
                <input type="hidden" name="user_id" id="problem_user_id" value="{{ $data['user_id'] }}">


                <form method="post" id="form-submit" class="requires-validation" novalidate>
                    @csrf
                    <div class="row" id="prescription-model">


                        <input type="hidden" name="id" id="id" value="">

                        <input type="hidden" name="user_id" id="user_id" value="{{ $data['user_id'] }}">
                        <input type="hidden" name="encounter_id" id="encounter_id" value="{{ $data['id'] }}">
                        <input type="hidden" name="type" value="encounter_prescription">

                        <input type="hidden" name="template_id" id="template_id" value="{{ $data['id'] }}">

                        <label class="form-label col-md-12">
                            {{ __('clinic.name') }} <span class="text-danger">*</span>
                        </label>

                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="{{ __('clinic.lbl_name') }}" required data-toggle="validator"
                                list="prescription-list" />
                        </div>
                        <div class="invalid-feedback">
                            {{ __('Please provide a valid frequency.') }}
                        </div>

                        <!-- Frequency -->
                        <div class="form-group">
                            <label class="form-label col-md-12">
                                {{ __('clinic.lbl_frequency') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="frequency" id="frequency" class="form-control col-md-12"
                                placeholder="{{ __('clinic.lbl_frequency') }}" value="" required>
                            <div class="invalid-feedback">
                                {{ __('Please provide a valid frequency.') }}
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="form-group">
                            <label class="form-label col-md-12">
                                {{ __('clinic.lbl_duration') }} <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="duration" id="duration" class="form-control col-md-12"
                                placeholder="{{ __('clinic.lbl_duration') }}" value="" required>
                            <div class="invalid-feedback">
                                {{ __('Please provide a valid duration.') }}
                            </div>
                        </div>

                        <!-- Instruction -->
                        <div class="form-group">
                            <label class="form-label" for="instruction">{{ __('clinic.lbl_instruction') }}</label>
                            <textarea class="form-control" name="instruction" id="instruction" placeholder="{{ __('clinic.lbl_instruction') }}">{{ old('instruction') }}</textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
    <script>
        $(document).ready(function() {


            var baseUrl = '{{ url('/') }}';

            $('#form-submit').on('submit', function(event) {
                event.preventDefault();

                let form = $(this)[0];
                if (form.checkValidity() === false) {
                    event.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }

                let formData = $(this).serializeArray();
                let hasId = formData.some(field => field.name === 'id' && field.value !== '');
                let id = formData.find(field => field.name === 'id')?.value || null;

                let route = hasId ?
                    `${baseUrl}/app/encounter-template/update-prescription/${id}` // Update route
                    :
                    `${baseUrl}/app/encounter-template/save-prescription`; // Save route

                $.ajax({
                    url: route,
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Include CSRF token for security
                    },
                    success: function(response) {
                        console.log(response)
                        if (response.html) {

                            document.getElementById('prescription_table').innerHTML = response
                                .html;
                            $('#addprescriptiontemplae').modal('hide');

                            $('#form-submit').trigger('reset');

                            $('#id').val('');
                            $('#user_id').val('');
                            $('#encounter_id').val('');

                            $('#form-submit')[0].classList.remove('was-validated');

                            window.successSnackbar(
                                `Prescription ${hasId ? 'updated' : 'added'} successfully`);
                        } else {
                            window.errorSnackbar('Something went wrong! Please check.');
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred: ' + xhr.responseText);
                    }
                });
            });


            $('#addprescriptiontemplae').on('hidden.bs.modal', function() {

                $('#id').val('');
                $('#user_id').val('');
                $('#encounter_id').val('');
                $('#form-submit').trigger('reset').removeClass('was-validated');
            });






        });
    </script>
@endpush
