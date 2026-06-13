<h5 class="card-title mb-3">{{ __('appointment.problems') }}</h5>

{{-- Hidden IDs --}}
<input type="hidden" name="encounter_id" id="problem_encounter_id" value="{{ $data['id'] }}">
<input type="hidden" name="user_id" id="problem_user_id" value="{{ $data['user_id'] }}">

{{-- Select input --}}
<div class="mb-3">
    <p class="mb-2 fs-12 text-danger">
        <strong>{{ __('appointment.note_encounter_problem') }}</strong>
    </p>
    <select id="problem" name="problem_id" class="select2 form-select"
        data-placeholder="{{ __('appointment.select_problems') }}">
        <option value="">{{ __('appointment.select_problems') }}</option>
        @foreach ($problem_list as $problem)
            <option value="{{ $problem->name }}">{{ $problem->name }}</option>
        @endforeach
    </select>
</div>

{{-- List of selected problems --}}
<div class="medial-history-card medial-history-card-problem">
    @if (count($data['selectedProblemList']) > 0)
        <ul class="list-unstyled m-0">
            @foreach ($data['selectedProblemList'] as $index => $problem)
                <li class="mb-2 pb-2 border-bottom">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <span class="heading-color">{{ $index + 1 }}. {{ $problem['title'] }}</span>
                        @if ($data['status'] == 1)
                            <button class="btn btn-sm p-0 text-danger"
                                onclick="removeProblem({{ $problem['id'] }})"
                                title="{{ __('messages.remove') }}">
                                <i class="ph ph-x-circle fs-5"></i>
                            </button>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="text-center py-4">
            <p class="text-danger mb-0">{{ __('appointment.no_problem_found') }}</p>
        </div>
    @endif
</div>

@push('after-scripts')
<style>
/* Reset select2-selection__rendered line-height to normal */
.select2-selection__rendered {
    line-height: normal !important;
}
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
$(function () {
    let baseUrl = '{{ url('/') }}';

    // Keep track of manually added problems for dropdown
    let manuallyAddedProblems = [];

    // Initialize Select2 immediately
    function initializeSelect2() {
        $('#problem').select2({
            placeholder: '{{ __("appointment.select_problems") }}',
            tags: true,
            width: '100%',
            allowClear: true,
            createTag: function (params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                return { id: term, text: term };
            }
        });
    }
    
    // Initialize Select2 on page load
    initializeSelect2();
    
    // Initialize dropdown with any existing problems from the list
    function initializeDropdownWithExistingProblems() {
        var existingProblems = [];
        $('.medial-history-card-problem ul li').each(function() {
            var problemText = $(this).find('span').text();
            var problemTitle = problemText.substring(problemText.indexOf('. ') + 2);
            if (!existingProblems.includes(problemTitle)) {
                existingProblems.push(problemTitle);
            }
        });
        
        // Add existing problems to our cache
        existingProblems.forEach(function(problemTitle) {
            if (!manuallyAddedProblems.includes(problemTitle)) {
                manuallyAddedProblems.push(problemTitle);
            }
        });
        
        // Don't override the initial server options, just add existing problems to cache
        // The initial HTML already has the server problems loaded
    }
    
    // Function to update dropdown options
    function updateDropdownOptions() {
        // Get current options (preserve server-side options)
        var currentOptions = $('#problem').html();
        var dropdownHtml = currentOptions;
        
        // Add manually added problems that aren't already in the dropdown
        manuallyAddedProblems.forEach(function(problemTitle) {
            if (!dropdownHtml.includes(`value="${problemTitle}"`)) {
                dropdownHtml += `<option value="${problemTitle}">${problemTitle}</option>`;
            }
        });
        
        // Update the dropdown
        $('#problem').html(dropdownHtml);
    }
    
    // Function to get all existing problems from the current list
    function getAllExistingProblems() {
        var existingProblems = [];
        $('.medial-history-card-problem ul li').each(function() {
            var problemText = $(this).find('span').text();
            var problemTitle = problemText.substring(problemText.indexOf('. ') + 2);
            if (!existingProblems.includes(problemTitle)) {
                existingProblems.push(problemTitle);
            }
        });
        return existingProblems;
    }
    
    // Initialize on page load
    initializeDropdownWithExistingProblems();

    // Add new problem on select
    $('#problem').on('change', function () {
        let problemName = $(this).val();
        let encounterId = $('#problem_encounter_id').val();
        let userId = $('#problem_user_id').val();

        if (problemName && problemName.trim()) {
            $.ajax({
                url: baseUrl + '/app/encounter-template/save-template-histroy',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: 'encounter_problem',
                    name: problemName.trim(),
                    encounter_id: encounterId,
                    template_id: encounterId,
                    user_id: userId
                },
                success: function (response) {
                    if (response?.status) {
                        // Update the problems list
                        updateProblemsList(response.medical_histroy);
                        
                        // Update dropdown options if constant_data exists
                        if (response.constant_data && response.constant_data.length > 0) {
                            updateDropdownOptions(response.constant_data);
                        }

                        // Clear the select2 selection
                        $('#problem').val('').trigger('change');
                        
                        // Show success message
                        if (window.successSnackbar) {
                            window.successSnackbar('{{ __("appointment.problem_added_successfully") }}');
                        }
                    } else {
                        if (window.errorSnackbar) {
                            window.errorSnackbar('{{ __("appointment.failed_to_save_problem") }}');
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

    // Helper function to update problems list
    function updateProblemsList(medicalHistory) {
        var listHtml = '';
        if (medicalHistory && medicalHistory.length > 0) {
            medicalHistory.forEach(function(problem, index) {
                listHtml += `
                <li class="mb-2 pb-2 border-bottom">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <span class="heading-color">${index + 1}. ${problem.title}</span>
                        <button class="btn btn-sm p-0 text-danger"
                            onclick="removeProblem(${problem.id})"
                            title="{{ __('messages.remove') }}">
                            <i class="ph ph-x-circle fs-5"></i>
                        </button>
                    </div>
                </li>`;
            });
            $('.medial-history-card-problem').html('<ul class="list-unstyled m-0">' + listHtml + '</ul>');
        } else {
            $('.medial-history-card-problem').html(`
                <div class="text-center py-4">
                    <p class="text-danger mb-0">{{ __('appointment.no_problem_found') }}</p>
                </div>
            `);
        }
    }

    // Helper function to update dropdown options
    function updateDropdownOptions(constantData) {
        var dropdownHtml = `<option value="">{{ __('appointment.select_problems') }}</option>`;
        constantData.forEach(function(problem) {
            dropdownHtml += `<option value="${problem.name}">${problem.name}</option>`;
        });
        $('#problem').html(dropdownHtml);
    }

    // Remove problem
    window.removeProblem = function (problemId) {
        if (!problemId) return;
        $.ajax({
            url: baseUrl + '/app/encounter-template/remove-template-histroy',
            type: 'GET',
            data: { id: problemId, type: 'encounter_problem' },
            success: function (response) {
                if (response?.status) {
                    // Update the problems list
                    updateProblemsList(response.medical_histroy);
                    
                    // Update dropdown options if constant_data exists
                    if (response.constant_data && response.constant_data.length > 0) {
                        updateDropdownOptions(response.constant_data);
                    }

                    // Clear the select2 selection
                    $('#problem').val('').trigger('change');
                    
                    // Show success message
                    if (window.successSnackbar) {
                        window.successSnackbar('{{ __("appointment.problem_removed_successfully") }}');
                    }
                } else {
                    if (window.errorSnackbar) {
                        window.errorSnackbar('{{ __("appointment.failed_to_remove_problem") }}');
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
    };

});
</script>
@endpush

