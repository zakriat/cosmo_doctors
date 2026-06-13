<div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="clinic-session-detail" aria-labelledby="form-offcanvasLabel">
    <form id="clinic-session-form" method="POST" action="{{ route('backend.clinic-session.store') }}">
        @csrf
        <input type="hidden" name="clinic_id" value="{{ $clinic->id }}">
       
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="form-offcanvasLabel">
                {{ $clinic->name ?? '' }}' Sessions
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="card">
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($weekdays as $index => $day)
                        <li class="list-group-item p-0 mb-3">
                            <input type="hidden" name="weekdays[{{ $index }}][id]" value="{{ $day['id'] ?? '' }}">
                            <div class="form-group row align-items-center gy-1">
                                <div class="col-sm-3">
                                    <div class="d-flex align-items-center justify-content-sm-start justify-content-center gap-1">
                                        <div class="form-check">
                                            <input type="hidden" name="weekdays[{{ $index }}][is_holiday]" value="0">
                                            <input class="form-check-input toggle-holiday" 
                                                value="1"
                                                name="weekdays[{{ $index }}][is_holiday]"
                                                data-day="{{ $index }}"
                                                id="{{ $index }}-dayoff" type="checkbox"
                                                {{ !empty($day['is_holiday']) && $day['is_holiday'] ? 'checked' : '' }}>
                                        </div>
                                        <h6 class="text-capitalize m-0">{{ $index + 1 }}. {{ ucfirst($day['day']) }}</h6>
                                    </div>
                                </div>

                                {{-- Work hours --}}
                                <div class="col-sm-6 work-hours-{{ $index }}" @if(!empty($day['is_holiday']) && $day['is_holiday']) style="display:none;" @endif>
                                    <div class="d-flex align-items-center justify-content-sm-end justify-content-center gap-2">
                                        <input type="time" name="weekdays[{{ $index }}][start_time]" 
                                            class="form-control session-time"
                                            value="{{ $day['start_time'] ?? '09:00' }}">
                                        <input type="time" name="weekdays[{{ $index }}][end_time]" 
                                            class="form-control session-time"
                                            value="{{ $day['end_time'] ?? '18:00' }}">
                                    </div>
                                </div>

                                {{-- Add break link --}}
                                <div class="col-sm-3 text-sm-end text-center work-hours-{{ $index }}" @if(!empty($day['is_holiday']) && $day['is_holiday']) style="display:none;" @endif>
                                    <a href="#" class="clickable-text text-primary add-break" data-day="{{ $index }}">
                                        {{ __('clinic.lbl_add_break') }}
                                    </a>
                                </div>

                                {{-- Clinic closed message --}}
                                <div class="col-sm-9 clinic-closed-{{ $index }}" @if(empty($day['is_holiday']) || !$day['is_holiday']) style="display:none;" @endif>
                                    <p class="m-0 text-danger fw-bold">{{ __('clinic.closed') }}</p>
                                </div>
                            </div>

                            {{-- Breaks list --}}
                            <div id="breaks-{{ $index }}" class="work-hours-{{ $index }}" @if(!empty($day['is_holiday']) && $day['is_holiday']) style="display:none;" @endif>
                                {{-- Hidden input ensures empty [] is always submitted if no breaks --}}
                                <input type="hidden" class="breaks-empty" name="weekdays[{{ $index }}][breaks]" value='[]'>

                                @if(!empty($day['breaks']) && is_array($day['breaks']))
                                    @foreach($day['breaks'] as $bIndex => $break)
                                    <div class="row align-items-center break-row mb-2">
                                        <div class="col-sm-3 col-12 d-flex align-items-center">
                                            <span class="fw-bold">{{ __('clinic.lbl_break') }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="d-flex gap-2">
                                                <input type="time" class="form-control"
                                                    name="weekdays[{{ $index }}][breaks][{{ $bIndex }}][start_break]"
                                                    value="{{ $break['start_break'] ?? '12:00' }}">
                                                <input type="time" class="form-control"
                                                    name="weekdays[{{ $index }}][breaks][{{ $bIndex }}][end_break]"
                                                    value="{{ $break['end_break'] ?? '12:00' }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-12 text-end">
                                            <a href="#" class="text-danger remove-break" style="font-size: 0.95em;">
                                                {{ __('messages.remove') }}
                                            </a>
                                        </div>
                                    </div>
                                    
                                    @endforeach
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            {{-- Actions --}}
            <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-3 bg-body">
                <button type="button" class="btn btn-outline-secondary" id="clinic-session-cancel-btn" data-bs-dismiss="offcanvas">
                    {{ __('messages.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary" id="clinic-session-save-btn">
                    {{ __('messages.save') }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
$(function () {
    // Toggle holiday
    $(document).off('change.toggleHoliday').on('change.toggleHoliday', '.toggle-holiday', function () {
        let dayIndex = $(this).data('day');
        if ($(this).is(':checked')) {
            $('.work-hours-' + dayIndex).hide();
            $('.clinic-closed-' + dayIndex).show();
        } else {
            $('.work-hours-' + dayIndex).show();
            $('.clinic-closed-' + dayIndex).hide();
        }
    });

    // Add new break
    $(document).off('click.addBreak').on('click.addBreak', '.add-break', function (e) {
        e.preventDefault();
        let dayIndex = $(this).data('day');
        let breaksContainer = $('#breaks-' + dayIndex);

        // Remove empty marker
        breaksContainer.find('.breaks-empty').remove();

        // Count current breaks to get index
        let currentCount = breaksContainer.find('.break-row').length;
        let isFirst = (currentCount === 0);

        let newBreak = `
    <div class="row align-items-center break-row mb-2">
        <div class="col-sm-3 col-12 d-flex align-items-center">
            <span class="fw-bold">${isFirst ? '{{ __('clinic.lbl_break') }}' : ''}</span>
        </div>
        <div class="col-sm-6">
            <div class="d-flex gap-2">
                <input type="time" class="form-control"
                    name="weekdays[${dayIndex}][breaks][${currentCount}][start_break]"
                    value="13:00">
                <input type="time" class="form-control"
                    name="weekdays[${dayIndex}][breaks][${currentCount}][end_break]"
                    value="14:00">
            </div>
        </div>
        <div class="col-sm-3 col-12 text-end">
            <a href="#" class="text-danger remove-break" style="font-size: 0.95em;">
                {{ __('messages.remove') }}
            </a>
        </div>
    </div>
`;

        breaksContainer.append(newBreak);
    });

    // Remove break
    $(document).off('click.removeBreak').on('click.removeBreak', '.remove-break', function (e) {
        e.preventDefault();
        let breaksContainer = $(this).closest('[id^="breaks-"]');
        $(this).closest('.break-row').remove();

        // If no breaks left, re-add the empty marker
        if (breaksContainer.find('.break-row').length === 0) {
            let dayIndex = breaksContainer.attr('id').split('-')[1];
            breaksContainer.append(`<input type="hidden" class="breaks-empty" name="weekdays[${dayIndex}][breaks]" value='[]'>`);
        }
    });

    // Form submit
    $('#clinic-session-form').off('submit').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $btn = $('#clinic-session-save-btn');
        $btn.prop('disabled', true).text('{{ __("clinic.saving") }}');

        var formData = new FormData(this);

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '{{ __("clinic.clinic_session_added") }}',
                    text: '{{ __("clinic.clinic_session_added") }}',
                    confirmButtonText: 'OK'
                }).then(() => {
                    var offcanvasEl = document.getElementById('clinic-session-detail');
                    if (window.bootstrap && window.bootstrap.Offcanvas) {
                        (bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl)).hide();
                    } else {
                        $(offcanvasEl).hide();
                    }
                    if (window.jQuery && $.fn.DataTable && $('#datatable').length) {
                        $('#datatable').DataTable().ajax.reload(null, false);
                    } else if (typeof window.reloadClinicSessionTable === 'function') {
                        window.reloadClinicSessionTable();
                    }
                });
                $btn.prop('disabled', false).text('{{ __("messages.save") }}');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ __("clinic.an_error_occurred_try_again") }}'
                });
                $btn.prop('disabled', false).text('{{ __("messages.save") }}');
            }
        });
    });

    // Close offcanvas when clicking anywhere outside of it
    $(document).on('mousedown.clinicSessionOffcanvas', function(e) {
        var $offcanvas = $('#clinic-session-detail');
        // Only proceed if the offcanvas is shown
        if ($offcanvas.hasClass('show')) {
            // If the click target is not inside the offcanvas
            if (!$offcanvas.is(e.target) && $offcanvas.has(e.target).length === 0) {
                if (window.bootstrap && window.bootstrap.Offcanvas) {
                    (bootstrap.Offcanvas.getInstance($offcanvas[0]) || new bootstrap.Offcanvas($offcanvas[0])).hide();
                } else {
                    $offcanvas.hide();
                }
            }
        }
    });
});
</script>
