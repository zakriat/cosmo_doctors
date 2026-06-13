<h5 class="card-title mb-3">{{ __('appointment.note') }}</h5>
<div class="card bg-body">
    <input type="hidden" name="encounter_id" id="notes_encounter_id" value="{{ $data['id'] }}">
    <input type="hidden" name="user_id" id="notes_user_id" value="{{ $data['user_id'] }}">

    <div class="card-footer pb-0 excounter-note" v-if="EncounterDetails.status == 1">
        <div class="position-relative">
            <textarea class="form-control h-auto" rows="1" placeholder="{{ __('appointment.enter_note') }}" v-model="notes" name="notes"
                id="notes" style="min-height: max-content"></textarea>
            <button class="btn btn-sm btn-primary" onclick="addNotesValue()"><i
                    class="ph ph-plus me-2"></i>{{ __('appointment.add') }}</button>
        </div>
    </div>
    <div class="card-body medial-history-card medial-history-notes">
        @if (count($data['notesList']) > 0)
            <ul class="list-inline m-0 p-0">
                @foreach ($data['notesList'] as $index => $note)
                    <li class="mb-3">
                        <div class="d-flex align-items-start justify-content-between gap-1">
                            <span>{{ $index + 1 }}. {{ $note->title }}</span>
                            @if ($data['status'] == 1)
                                <button class="btn p-0 text-danger" onclick="removeNotes({{ $note->id }})">
                                    <i class="ph ph-x-circle"></i>
                                </button>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-4">
                <p class="text-danger mb-0">{{ __('appointment.no_notes_found') }}</p>
            </div>
        @endif
    </div>

</div>

@push('after-scripts')
<style>
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

        // Initialize the addNotesValue function immediately
        window.addNotesValue = function() {
            var notes = $('#notes').val();
            var encounterId = $('#notes_encounter_id').val();
            var userId = $('#notes_user_id').val();

            if (notes && notes.trim()) {
                $.ajax({
                    url: baseUrl + '/app/encounter-template/save-template-histroy',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: 'encounter_notes',
                        name: notes.trim(),
                        encounter_id: encounterId,
                        template_id: encounterId,
                        user_id: userId
                    },
                    success: function(response) {
                        if (response && response.status) {
                            // Clear the textarea
                            $('#notes').val('');
                            
                            // Update the notes list
                            updateNotesList(response.medical_histroy);
                            
                            // Show success message
                            if (window.successSnackbar) {
                                window.successSnackbar('{{ __("appointment.note_added_successfully") }}');
                            }
                        } else {
                            if (window.errorSnackbar) {
                                window.errorSnackbar('{{ __("appointment.failed_to_save_note") }}');
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
            } else {
                if (window.errorSnackbar) {
                    window.errorSnackbar('{{ __("appointment.please_enter_note") }}');
                }
            }
        };

        // Initialize the removeNotes function immediately
        window.removeNotes = function(Id) {
            if (Id) {
                $.ajax({
                    url: baseUrl + '/app/encounter-template/remove-template-histroy?id=' + Id +
                        '&type=encounter_notes',
                    method: 'GET',
                    success: function(response) {
                        if (response && response.status) {
                            // Update the notes list
                            updateNotesList(response.medical_histroy);
                            
                            // Show success message
                            if (window.successSnackbar) {
                                window.successSnackbar('{{ __("appointment.note_removed_successfully") }}');
                            }
                        } else {
                            if (window.errorSnackbar) {
                                window.errorSnackbar('{{ __("appointment.failed_to_remove_note") }}');
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
        };

        // Helper function to update notes list
        function updateNotesList(medicalHistory) {
            var listHtml = '';
            if (medicalHistory && medicalHistory.length > 0) {
                medicalHistory.forEach(function(note, index) {
                    listHtml += `
                    <li class="mb-3">
                        <div class="d-flex align-items-start justify-content-between gap-1">
                            <span class="heading-color">${index + 1}. ${note.title}</span>
                            <button class="btn p-0 text-danger"
                                onclick="removeNotes(${note.id})">
                                <i class="ph ph-x-circle"></i>
                            </button>
                        </div>
                    </li>`;
                });
                $('.medial-history-notes').html('<ul class="list-inline m-0 p-0">' + listHtml + '</ul>');
            } else {
                $('.medial-history-notes').html(`
                    <div class="text-center py-4">
                        <p class="text-danger mb-0">{{ __('appointment.no_notes_found') }}</p>
                    </div>
                `);
            }
        }

        // Initialize textarea auto-resize
        $('#notes').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
</script>
@endpush
