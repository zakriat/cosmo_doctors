<h5 class="card-title mb-3">{{ __('appointment.observation') }}</h5>
<div class="card bg-body">
    <input type="hidden" name="encounter_id" id="observation_encounter_id" value="{{ $data['id'] }}">
    <input type="hidden" name="user_id" id="observation_user_id" value="{{ $data['user_id'] }}">

    @if ($data['status'] == 0)
        <div class="card-body">
            @if (count($data['selectedObservationList']) > 0)
                <ul class="list-inline m-0 p-0">
                    @foreach ($data['selectedObservationList'] as $index => $observation)
                        <li class="mb-3">
                            <div class="d-flex align-items-start justify-content-between gap-1">
                                <span>{{ $index + 1 }}. {{ $observation['title'] }}</span>
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
    @else
        <div class="card-footer pb-0">
           
                <p class="mb-2 fs-12 clinical_details_notes text-danger">
                    <b>{{ __('appointment.note_encounter_observation') }}</b>
                </p>
          
            @if ($data['status'] == 1)
                <select id="observations" name="observation_id" class="select2 form-select observation "
                    placeholder="{{ __('appointment.select_observation') }}" data-filter="select">
                    <option value="">{{ __('appointment.select_observation') }}</option>
                    @foreach ($observation_list as $observation)
                        <option value="{{ $observation->name }}">{{ $observation->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="card-body medial-history-card medial-history-card-observation">
            @if (count($data['selectedObservationList']) > 0)
                <ul class="list-inline m-0 p-0">
                    @foreach ($data['selectedObservationList'] as $index => $observation)
                        <li class="mb-3">
                            <div class="d-flex align-items-start justify-content-between gap-1">
                                <span>{{ $index + 1 }}. {{ $observation['title'] }}</span>
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
    @endif


</div>

@push('after-scripts')
    <script>
        $(document).ready(function() {

            $('#observations').select2({
                placeholder: '{{ __('appointment.select_observation') }}',
                allowClear: true // Optional: Allows clearing the selection
            });
            $('#observations').on('select2:open', function () {
                var observationsInputField = $('.select2-container--open .select2-search__field');

                observationsInputField.off('keydown'); // Remove previous listeners
                observationsInputField.on('keydown', function (event) {
                    if (event.key === "Enter") {
                        var newOption = $(this).val();
                        if (newOption) {
                            var newOptionElement = new Option(newOption, newOption, true, true);
                            $('#observations').append(newOptionElement).trigger('change');
                            $('#observations').select2('close');
                        }
                    }
                });
            });

            var baseUrl = '{{ url('/') }}';

            $('#observations').on('change', function() {
                var observationName = $(this).val();
                var encounterId = $('#observation_encounter_id').val();
                var userId = $('#observation_user_id').val();

                if (observationName) {
                    $.ajax({
                        url: '{{ url('/app/encounter/save-select-option') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            type: 'encounter_observations',
                            name: observationName,
                            encounter_id: encounterId,
                            user_id: userId
                        },
                        success: function(response) {
                            if (response && response.status) {
                                updateObservationList(response.medical_histroy);
                                updateDropdown(response.data);
                                // Clear the selection after adding
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

        function updateObservationList(medicalHistory) {
            var observationlistHtml = '';
            if (medicalHistory && medicalHistory.length > 0) {
                medicalHistory.forEach(function(observation, index) {
                    observationlistHtml += `
                        <li class="mb-3">
                            <div class="d-flex align-items-start justify-content-between gap-1">
                                <span>${index + 1}. ${observation.title}</span>
                                <button class="btn p-0 text-danger"
                                    onclick="removeobservation(${observation.id})">
                                    <i class="ph ph-x-circle"></i>
                                </button>
                            </div>
                        </li>`;
                });
                $('.medial-history-card-observation').html('<ul class="list-inline m-0 p-0">' + observationlistHtml + '</ul>');
            } else {
                $('.medial-history-card-observation').html(`
                    <div class="text-center py-4">
                        <p class="text-danger mb-0">{{ __('appointment.no_observation_found') }}</p>
                    </div>
                `);
            }
        }

            function updateDropdown(data) {
                console.log('Updating dropdown with data:', data);
                var observationdropdownHtml = `<option value="">{{ __('appointment.select_observation') }}</option>`;
                
                if (data && data.length > 0) {
                    data.forEach(function(observation) {
                        observationdropdownHtml += `<option value="${observation.name}">${observation.name}</option>`;
                    });
                }
                
                $('#observations').html(observationdropdownHtml);

                // Reinitialize Select2
                $('#observations').select2({
                    tags: true,
                    placeholder: "{{ __('appointment.select_observation') }}",
                    allowClear: true
                });
            }



        function removeobservation(observationId) {
            if (observationId) {
                $.ajax({
                    url: '{{ url('/app/encounter/remove-histroy-data') }}',
                    method: 'GET',
                    data: {
                        id: observationId,
                        type: 'encounter_observations'
                    },
                    success: function(response) {
                        if (response && response.status) {
                            updateObservationList(response.medical_histroy);
                            updateDropdown(response.data);
                            
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
