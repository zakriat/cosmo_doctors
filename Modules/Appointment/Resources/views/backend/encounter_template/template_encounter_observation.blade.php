<h5 class="card-title mb-3">{{ __('appointment.observation') }}</h5>
<input type="hidden" name="encounter_id" id="observation_encounter_id" value="{{ $data['id'] }}">
<input type="hidden" name="user_id" id="observation_user_id" value="{{ $data['user_id'] }}">
<div class="pb-0 mb-3">
    <p class="mb-2 mb-0 fs-12 clinical_details_notes text-danger">
        <b>{{ __('appointment.note_encounter_observation') }}</b>
    </p>
    <select id="observations" name="observation_id" class="select2 form-select"
        data-placeholder="{{ __('appointment.select_observation') }}">
        <option value="">{{ __('appointment.select_observation') }}</option>
        @foreach ($observation_list as $observation)
            <option value="{{ $observation->name }}">{{ $observation->name }}</option>
        @endforeach
    </select>
</div>
<div class="medial-history-card medial-history-card-observation">
    @if (count($data['selectedObservationList']) > 0)
        <ul class="list-inline m-0 p-0">
            @foreach ($data['selectedObservationList'] as $index => $observation)
                <li class="mb-3">
                    <div class="d-flex align-items-start justify-content-between gap-1">
                        <span class="text-light-black">{{ $index + 1 }}. {{ $observation['title'] }}</span>
                        @if ($data['status'] == 1)
                            <button class="btn p-0 text-danger" onclick="removeobservation({{ $observation['id'] }})">
                                <i class="ph ph-x-circle"></i>
                            </button>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="text-center py-4">
            <p class="text-danger mb-0">{{ __('appointment.no_observation_found') }}</p>
        </div>
    @endif
</div>

@push('after-scripts')
<style>
    .text-light-black {
        color: #4a4a4a !important; /* softer black */
    }
    .heading-color {
        color: #212529 !important;
    }
    .text-danger {
        color: #dc3545 !important;
    }
</style>
<script>
    $(document).ready(function() {
        var baseUrl = '{{ url('/') }}';

        // Initialize Select2 immediately
        function initializeSelect2() {
            $('#observations').select2({
                placeholder: '{{ __("appointment.select_observation") }}',
                tags: true,
                width: '100%',
                allowClear: true,
                createTag: function (params) {
                    const term = $.trim(params.term);
                    if (term === '') { return null; }
                    return { id: term, text: term };
                }
            });
        }
        
        // Initialize Select2 on page load
        initializeSelect2();

        $('#observations').on('change', function() {
            var observationName = $(this).val();
            var encounterId = $('#observation_encounter_id').val();
            var userId = $('#observation_user_id').val();

            if (observationName && observationName.trim()) {
                $.ajax({
                    url: baseUrl + '/app/encounter-template/save-template-histroy',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: 'encounter_observations',
                        name: observationName.trim(),
                        encounter_id: encounterId,
                        template_id: encounterId,
                        user_id: userId
                    },
                    success: function(response) {
                        if (response && response.status) {
                            // Update the observations list
                            updateObservationsList(response.medical_histroy);
                            
                            // Update dropdown options if constant_data exists
                            if (response.constant_data && response.constant_data.length > 0) {
                                updateDropdownOptions(response.constant_data);
                            }

                            // Clear the select2 selection
                            $('#observations').val('').trigger('change');
                            
                            // Show success message
                            if (window.successSnackbar) {
                                window.successSnackbar('{{ __("appointment.observation_added_successfully") }}');
                            }
                        } else {
                            if (window.errorSnackbar) {
                                window.errorSnackbar('{{ __("appointment.failed_to_save_observation") }}');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        if (window.errorSnackbar) {
                            window.errorSnackbar('{{ __("appointment.error_try_again") }}');
                        }
                    }
                });
            }
        });
    });

    // Helper function to update observations list
    function updateObservationsList(medicalHistory) {
        var listHtml = '';
        if (medicalHistory && medicalHistory.length > 0) {
            medicalHistory.forEach(function(observation, index) {
                listHtml += `
                <li class="mb-3">
                    <div class="d-flex align-items-start justify-content-between gap-1">
                        <span class="text-light-black">${index + 1}. ${observation.title}</span>
                        <button class="btn p-0 text-danger"
                            onclick="removeobservation(${observation.id})">
                            <i class="ph ph-x-circle"></i>
                        </button>
                    </div>
                </li>`;
            });
            $('.medial-history-card-observation').html('<ul class="list-inline m-0 p-0">' + listHtml + '</ul>');
        } else {
            $('.medial-history-card-observation').html(`
                <div class="text-center py-4">
                    <p class="text-danger mb-0">{{ __('appointment.no_observation_found') }}</p>
                </div>
            `);
        }
    }

    // Helper function to update dropdown options
    function updateDropdownOptions(constantData) {
        var dropdownHtml = `<option value="">{{ __('appointment.select_observation') }}</option>`;
        constantData.forEach(function(observation) {
            dropdownHtml += `<option value="${observation.name}">${observation.name}</option>`;
        });
        $('#observations').html(dropdownHtml);
    }

    // Initialize removeobservation function globally
    window.removeobservation = function(Id) {
        var baseUrl = '{{ url('/') }}';

        if (Id) {
            $.ajax({
                url: baseUrl + '/app/encounter-template/remove-template-histroy?id=' + Id +
                    '&type=encounter_observation',
                method: 'GET',
                success: function(response) {
                    if (response && response.status) {
                        // Update the observations list
                        updateObservationsList(response.medical_histroy);
                        
                        // Update dropdown options if constant_data exists
                        if (response.constant_data && response.constant_data.length > 0) {
                            updateDropdownOptions(response.constant_data);
                        }

                        // Clear the select2 selection
                        $('#observations').val('').trigger('change');
                        
                        // Show success message
                        if (window.successSnackbar) {
                            window.successSnackbar('{{ __("appointment.observation_removed_successfully") }}');
                        }
                    } else {
                        if (window.errorSnackbar) {
                            window.errorSnackbar('{{ __("appointment.failed_to_remove_observation") }}');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    if (window.errorSnackbar) {
                        window.errorSnackbar('{{ __("appointment.error_try_again") }}');
                    }
                }
            });
        }
    }
</script>
@endpush
