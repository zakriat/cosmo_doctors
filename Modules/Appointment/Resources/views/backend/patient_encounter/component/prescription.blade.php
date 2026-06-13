<div class="modal fade" id="addprescription" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('clinic.add_prescription') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form method="post" id="form-submit" class="requires-validation" novalidate>
                    @csrf
                    <div class="row" id="prescription-model">


                        <input type="hidden" name="id" id="id" value="">
                        <input type="hidden" name="encounter_id" id="problem_encounter_id" value="{{ $data['id'] }}">
                        <input type="hidden" name="user_id" id="problem_user_id" value="{{ $data['user_id'] }}">
                        <input type="hidden" name="type" value="encounter_prescription">
                        <input type="hidden" name="medicine_id" id="medicine_id" value="">

                        <!-- Medicine Selection -->
                        <div class="form-group">
                            <label class="form-label col-md-12">
                                Select Medicine <span class="text-danger">*</span>
                            </label>
                            <select name="medicine_id" id="medicine_select" class="form-control select2 col-md-12" style="width: 100%">
                                <option value="">Select Medicine</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a medicine.
                            </div>
                        </div>

                        <!-- Medicine Information Display -->
                     <!-- Medicine Information Display -->
<div id="medicine-info-card" class="medicine-info-card mb-3 border rounded" style="display: none; border: 2px solid #28a745 !important; background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);">
    <div class="card-header" style="background-color: #28a745; color: white; padding: 12px 15px; border-radius: 4px 4px 0 0; margin: -1px -1px 15px -1px;">
        <h6 class="mb-0" style="font-weight: 600; font-size: 16px; color: white;">
            <i class="fa-solid fa-pills me-2"></i>Medicine Information
        </h6>
    </div>
    <div class="p-3">
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <div class="info-item p-2" style="background-color: #f8fff9; border-left: 3px solid #28a745; border-radius: 4px;">
                    <small style="color: #155724; font-weight: 600; display: block; margin-bottom: 4px;">Generic Name:</small>
                    <small style="color: #000000; font-weight: 500;"><span id="medicine-generic"></span></small>
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <div class="info-item p-2" style="background-color: #f8fff9; border-left: 3px solid #28a745; border-radius: 4px;">
                    <small style="color: #155724; font-weight: 600; display: block; margin-bottom: 4px;">Strength:</small>
                    <small style="color: #000000; font-weight: 500;"><span id="medicine-strength"></span></small>
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <div class="info-item p-2" style="background-color: #f8fff9; border-left: 3px solid #28a745; border-radius: 4px;">
                    <small style="color: #155724; font-weight: 600; display: block; margin-bottom: 4px;">Dosage Form:</small>
                    <small style="color: #000000; font-weight: 500;"><span id="medicine-form"></span></small>
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <div class="info-item p-2" style="background-color: #f8fff9; border-left: 3px solid #28a745; border-radius: 4px;">
                    <small style="color: #155724; font-weight: 600; display: block; margin-bottom: 4px;">Manufacturer:</small>
                    <small style="color: #000000; font-weight: 500;"><span id="medicine-manufacturer"></span></small>
                </div>
            </div>
        </div>
        
        <div class="mb-2">
            <div class="info-item p-2" style="background-color: #f8fff9; border-left: 3px solid #28a745; border-radius: 4px;">
                <small style="color: #155724; font-weight: 600; display: block; margin-bottom: 4px;">Indication:</small>
                <small style="color: #000000; font-weight: 500;"><span id="medicine-indication"></span></small>
            </div>
        </div>
        
        <div class="mb-2">
            <div class="info-item p-2" style="background-color: #e1e1f8; border-left: 3px solid #28559dff; border-radius: 4px;">
                <small style="color: #28559dff; font-weight: 600; display: block; margin-bottom: 4px;">⚠️ Side Effects:</small>
                <small style="color: #000000; font-weight: 500;"><span id="medicine-side-effects"></span></small>
            </div>
        </div>
        
        <div class="mt-3" id="medicine-url-container" style="display: none;">
            <a id="medicine-url" href="#" target="_blank" class="btn btn-success" style="background-color: #28a745; border-color: #28a745; font-weight: 500;">
                <i class="fa-solid fa-external-link me-1"></i> View Reference
            </a>
        </div>
    </div>
</div>

                        <!-- Name -->
                        <div class="form-group">
                            <label class="form-label col-md-12">
                                {{ __('clinic.name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="prescription_name" class="form-control col-md-12"
                                placeholder="{{ __('clinic.lbl_name') }}" value="" required>
                            <div class="invalid-feedback">
                                Please provide a valid Name.
                            </div>
                            <small class="text-muted">{{ __('appointment.add_prescription_note') }}</small>
                        </div>

                        <!-- Frequency -->
                        <div class="form-group">
                            <label class="form-label col-md-12">
                                {{ __('clinic.lbl_frequency') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="frequency" id="frequency" class="form-control col-md-12"
                                placeholder="{{ __('clinic.lbl_frequency') }}" value="" required>
                            <div class="invalid-feedback">
                                Please provide a valid frequency.
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="form-group">
                            <label class="form-label col-md-12">
                                {{ __('clinic.lbl_duration') }} <span class="text-danger">*</span>
                            </label>
                            {{-- <input type="number" name="duration" id="duration" class="form-control col-md-12"
                                placeholder="{{ __('clinic.lbl_duration') }}" value="" required>
                            <div class="invalid-feedback">
                                Please provide a valid duration.
                            </div> --}}

                            <input type="number" name="duration" id="duration" class="form-control col-md-12"
                                placeholder="{{ __('clinic.lbl_duration') }}" required>
                            <div class="invalid-feedback">
                                Please provide a valid duration.
                            </div>
                        </div>

                        <!-- Instruction -->
                        <div class="form-group">
                            <label class="form-label" for="instruction">{{ __('clinic.lbl_instruction') }}</label>
                            <textarea class="form-control" name="instruction" id="instruction" placeholder="{{ __('clinic.lbl_instruction') }}">{{ old('instruction') }}</textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="save-btn">
                                <span class="btn-text">Save</span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Saving...
                                </span>
                            </button>
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
            var medicinesData = [];

            // Load medicines data
            function loadMedicines() {
                $.ajax({
                    url: baseUrl + '/app/medicines/index_list',
                    method: 'GET',
                    success: function(response) {
                        medicinesData = response;
                        
                        // Populate select2 dropdown
                        $('#medicine_select').empty().append('<option value="">Select Medicine</option>');
                        
                        response.forEach(function(medicine) {
                            $('#medicine_select').append(
                                `<option value="${medicine.id}" 
                                    data-generic="${medicine.generic_name || ''}"
                                    data-strength="${medicine.strength || ''}"
                                    data-form="${medicine.dosage_form || ''}"
                                    data-manufacturer="${medicine.manufacturer || ''}"
                                    data-indication="${medicine.indication || ''}"
                                    data-side-effects="${medicine.side_effects || ''}"
                                    data-url="${medicine.url || ''}"
                                >${medicine.name}</option>`
                            );
                        });
                        
                        // Initialize select2
                        $('#medicine_select').select2({
                            placeholder: 'Select Medicine',
                            allowClear: true,
                            dropdownParent: $('#addprescription')
                        });
                    },
                    error: function(xhr) {
                        console.error('Failed to load medicines:', xhr);
                    }
                });
            }

            // Handle medicine selection
            $('#medicine_select').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const medicineId = $(this).val();
                
                if (medicineId) {
                    // Update hidden field
                    $('#medicine_id').val(medicineId);
                    
                    // Auto-fill medicine name
                    $('#prescription_name').val(selectedOption.text());
                    
                    // Show medicine information
                    $('#medicine-generic').text(selectedOption.data('generic') || '-');
                    $('#medicine-strength').text(selectedOption.data('strength') || '-');
                    $('#medicine-form').text(selectedOption.data('form') || '-');
                    $('#medicine-manufacturer').text(selectedOption.data('manufacturer') || '-');
                    $('#medicine-indication').text(selectedOption.data('indication') || '-');
                    $('#medicine-side-effects').text(selectedOption.data('side-effects') || '-');
                    
                    // Handle URL
                    const url = selectedOption.data('url');
                    if (url) {
                        $('#medicine-url').attr('href', url);
                        $('#medicine-url-container').show();
                    } else {
                        $('#medicine-url-container').hide();
                    }
                    
                    $('#medicine-info-card').show();
                } else {
                    $('#medicine_id').val('');
                    $('#medicine-info-card').hide();
                }
            });

            // Load medicines on page load
            loadMedicines();

            $('#form-submit').on('submit', function(event) {
                event.preventDefault();

                const form = this;
                let isValid = true;

                // Clear previous validation styles
                form.classList.remove('was-validated');
                // Remove all validation classes completely
                $('.form-control').removeClass('is-invalid is-valid was-validated');

                // ✅ Duration validation starts here
                let durationVal = $('#duration').val();
                
                // Remove any symbols/characters except numbers
                durationVal = durationVal.replace(/[^0-9]/g, '');
                
                // Update the field value with cleaned data
                $('#duration').val(durationVal);
                
                const duration = Number(durationVal);

                // ✅ Clean instruction field - remove special symbols but keep letters, numbers, spaces, and basic punctuation
                let instructionVal = $('#instruction').val();
                if (instructionVal) {
                    // Remove special symbols but keep letters, numbers, spaces, periods, commas, and basic punctuation
                    instructionVal = instructionVal.replace(/[^\w\s.,!?-]/g, '');
                    // Update the field value with cleaned data
                    $('#instruction').val(instructionVal);
                }

                // Check if it's a positive integer (no decimals, not negative, not zero)
                if (!Number.isInteger(duration) || duration <= 0) {
                    $('#duration').addClass('is-invalid');
                    $('#duration').next('.invalid-feedback').text('Duration must be a positive whole number (e.g., 1, 2, 3).');
                    isValid = false;
                } else {
                    $('#duration').removeClass('is-invalid');
                    $('#duration').next('.invalid-feedback').text('');
                }


                // ✅ Stop here if invalid
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }

                if (!isValid) return;

                // Show loading state
                $('#save-btn').prop('disabled', true);
                $('.btn-text').addClass('d-none');
                $('.btn-loading').removeClass('d-none');

                // Submit via AJAX
                const formData = $(this).serializeArray();
                const hasId = formData.some(field => field.name === 'id' && field.value !== '');
                const id = formData.find(field => field.name === 'id')?.value || null;

                const route = hasId
                    ? `${baseUrl}/app/encounter/update-prescription/${id}`
                    : `${baseUrl}/app/encounter/save-prescription`;

                $.ajax({
                    url: route,
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Reset button state
                        $('#save-btn').prop('disabled', false);
                        $('.btn-text').removeClass('d-none');
                        $('.btn-loading').addClass('d-none');
                        
                        if (response.html) {
                            $('#prescription_table').html(response.html);
                            $('#addprescription').modal('hide');
                            $('#form-submit').trigger('reset').removeClass('was-validated');
                            $('#id').val('');
                            $('#medicine_id').val('');
                            $('#medicine_select').val('').trigger('change');
                            $('.form-control').removeClass('is-valid is-invalid was-validated');
                            $('#medicine-info-card').hide();
                            window.successSnackbar(`Prescription ${hasId ? 'updated' : 'added'} successfully`);
                        } else {
                            window.errorSnackbar('Something went wrong! Please check.');
                        }
                    },
                    error: function(xhr) {
                        // Reset button state on error
                        $('#save-btn').prop('disabled', false);
                        $('.btn-text').removeClass('d-none');
                        $('.btn-loading').addClass('d-none');
                        alert('An error occurred: ' + xhr.responseText);
                    }
                });
            });


            $('#addprescription').on('hidden.bs.modal', function() {
                // Reset button state when modal is closed
                $('#save-btn').prop('disabled', false);
                $('.btn-text').removeClass('d-none');
                $('.btn-loading').addClass('d-none');
                
                $('#id').val('')
                $('#medicine_id').val('');
                $('#medicine_select').val('').trigger('change');
                $('#form-submit').trigger('reset').removeClass('was-validated');
                $('.form-control').removeClass('is-valid is-invalid was-validated');
                $('#medicine-info-card').hide();
            });

        });
    </script>
@endpush
