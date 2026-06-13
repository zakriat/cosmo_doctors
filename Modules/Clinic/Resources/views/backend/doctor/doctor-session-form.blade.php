{{-- resources/views/clinic/backend/doctor/doctor-session-form.blade.php --}}

<form id="doctor-sessions-form" class="business-hour h-100 d-flex flex-column" autocomplete="off">
    @csrf
    <input type="hidden" id="doctor_id" name="doctor_id" value="">
    <input type="hidden" id="session_id" name="session_id" value="">

    <div class="flex-grow-1 p-4">
        <!-- Clinic Selection -->
        <div class="form-group mb-4">
            <label class="form-label fw-semibold">
                {{ __('clinic.lbl_clinic_name') }}<span class="text-danger">*</span>
            </label>
            <select id="clinic_id" name="clinic_id" class="form-select" required>
                <option value="" selected>{{ __('clinic.lbl_select_clinic') }}</option>
            </select>
            <div class="invalid-feedback select-hidden" id="clinic_id_error"></div>
        </div>

        <!-- Weekdays Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div id="weekdays-container" class="px-4 py-3">
                    <!-- Weekdays will render here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="border-top d-flex justify-content-end gap-2 px-4 py-3" >
        <button type="button" class="btn btn-white" data-bs-dismiss="offcanvas"  >
            {{ __('messages.cancel') }}
        </button>
        <button type="submit" class="btn btn-secondary" id="submit-btn" data-doctor-id=""  >
            <span class="spinner-border spinner-border-sm me-2 select-hidden" id="submit-spinner"></span>
            {{ __('messages.save') }}
        </button>
    </div>
</form>

@push('after-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let weekdays = getDefaultWeekdays();
    let clinicHolidays = [];
    let isEditMode = false;
    let currentSessionId = null;

    function getDefaultWeekdays() {
        return [
            { day:'monday', start_time:'09:00', end_time:'18:00', is_holiday:false, breaks:[] },
            { day:'tuesday', start_time:'09:00', end_time:'18:00', is_holiday:false, breaks:[] },
            { day:'wednesday', start_time:'09:00', end_time:'18:00', is_holiday:false, breaks:[] },
            { day:'thursday', start_time:'09:00', end_time:'18:00', is_holiday:false, breaks:[] },
            { day:'friday', start_time:'09:00', end_time:'18:00', is_holiday:false, breaks:[] },
            { day:'saturday', start_time:'09:00', end_time:'18:00', is_holiday:false, breaks:[] },
            { day:'sunday', start_time:'09:00', end_time:'18:00', is_holiday:true, breaks:[] }
        ];
    }

    // Helper: Get localized label for a day
    function getDayLabel(day) {
        const labels = {
            monday:    '{{ __("clinic.monday") }}',
            tuesday:   '{{ __("clinic.tuesday") }}',
            wednesday: '{{ __("clinic.wednesday") }}',
            thursday:  '{{ __("clinic.thursday") }}',
            friday:    '{{ __("clinic.friday") }}',
            saturday:  '{{ __("clinic.saturday") }}',
            sunday:    '{{ __("clinic.sunday") }}'
        };
        return labels[day] || day.charAt(0).toUpperCase() + day.slice(1);
    }

    // Render the weekdays UI
    // Render the weekdays UI
function renderWeekdays() {
    const container = document.getElementById('weekdays-container');
    container.innerHTML = '';
    weekdays.forEach((d, idx) => {
        const isHoliday = !!d.is_holiday || clinicHolidays.includes(d.day);

        // Build breaks
        let breaksHtml = '';
        if (!isHoliday && d.breaks && d.breaks.length) {
            breaksHtml = d.breaks.map((br, brIdx) => `
                <div class="row align-items-center break-row mb-2 ms-4">
                    <div class="col-sm-3 col-12 d-flex align-items-center">
                        ${brIdx === 0 ? `<span class="fw-bold">{{ __('clinic.lbl_break') }}</span>` : ''}
                    </div>
                    <div class="col-sm-3 col-6">
                        <input type="time" 
                            class="form-control form-control-sm"
                            name="weekdays[${idx}][breaks][${brIdx}][start_break]"
                            value="${br.start_break ?? '13:00'}"
                            data-day-idx="${idx}" data-break-idx="${brIdx}" data-type="break_start">
                    </div>
                    <div class="col-sm-3 col-6">
                        <input type="time" 
                            class="form-control form-control-sm"
                            name="weekdays[${idx}][breaks][${brIdx}][end_break]"
                            value="${br.end_break ?? '14:00'}"
                            data-day-idx="${idx}" data-break-idx="${brIdx}" data-type="break_end">
                    </div>
                    <div class="col-sm-3 col-12 text-end">
                        <a href="javascript:void(0)" class="text-danger remove-break-btn" 
                           data-day-idx="${idx}" data-break-idx="${brIdx}">
                           {{ __('clinic.lbl_remove') }}
                        </a>
                    </div>
                </div>
            `).join('');
        }

        // Main row
        container.innerHTML += `
            <li class="list-group-item p-0 mb-3 border-bottom pb-2">
                <input type="hidden" name="weekdays[${idx}][day]" value="${d.day}">
                <div class="form-group row align-items-center gy-1">
                    <!-- Day + holiday checkbox -->
                    <div class="col-sm-3">
                        <div class="d-flex align-items-center justify-content-sm-start justify-content-center gap-1">
                            <input type="checkbox" id="holiday_${idx}" ${isHoliday ? 'checked' : ''} data-idx="${idx}" class="form-check-input holiday-checkbox">
                            <h6 class="text-capitalize m-0">${getDayLabel(d.day)}</h6>
                        </div>
                    </div>

                    <!-- Work hours -->
                    <div class="col-sm-6 work-hours-${idx} ${isHoliday ? 'select-hidden' : ''}">
                        <div class="d-flex align-items-center justify-content-sm-end justify-content-center gap-2">
                            <input type="time" class="form-control session-time" id="start_time_${idx}" value="${d.start_time}" data-idx="${idx}" data-type="start">
                            <input type="time" class="form-control session-time" id="end_time_${idx}" value="${d.end_time}" data-idx="${idx}" data-type="end">
                        </div>
                    </div>

                    <!-- Add break link -->
                    <div class="col-sm-3 text-sm-end text-center work-hours-${idx} ${isHoliday ? 'select-hidden' : ''}">
                        <a href="javascript:void(0)" class="clickable-text add-break-btn" data-idx="${idx}">
                            {{ __('clinic.lbl_add_break') }}
                        </a>
                    </div>

                    <!-- Clinic closed message -->
                    <div class="col-sm-9 clinic-closed-${idx} ${!isHoliday ? 'select-hidden' : ''}">
                        <p class="m-0 text-danger fw-bold">{{ __('clinic.unavailable') }}</p>
                    </div>
                </div>

                <!-- Breaks section -->
                <div id="breaks-${idx}" class="work-hours-${idx} ${isHoliday ? 'select-hidden' : ''}">
                    ${breaksHtml}
                </div>
            </li>
        `;
    });
}


    // Toggle holiday for a day
    function toggleHoliday(idx) {
        weekdays[idx].is_holiday = !weekdays[idx].is_holiday;
        if (weekdays[idx].is_holiday) {
            weekdays[idx].breaks = [];
        }
        renderWeekdays();
    }

    // Add a break for a day
    function addInlineBreak(idx) {
        if (!weekdays[idx].breaks) weekdays[idx].breaks = [];
        // Store as {start_break, end_break}
        weekdays[idx].breaks.push({ start_break: '13:00', end_break: '14:00' });
        renderWeekdays();
    }

    // Remove a break for a day
    function removeBreak(dayIdx, breakIdx) {
        if (weekdays[dayIdx] && weekdays[dayIdx].breaks && weekdays[dayIdx].breaks[breakIdx]) {
            weekdays[dayIdx].breaks.splice(breakIdx, 1);
            renderWeekdays();
        }
    }

    // Update time for a day or break
    function updateTime(idx, type, value) {
        if (type === 'start') {
            weekdays[idx].start_time = value;
        } else if (type === 'end') {
            weekdays[idx].end_time = value;
        }
    }

    // Update break time
    function updateBreakTime(dayIdx, breakIdx, type, value) {
        if (weekdays[dayIdx] && weekdays[dayIdx].breaks && weekdays[dayIdx].breaks[breakIdx]) {
            if (type === 'break_start') {
                weekdays[dayIdx].breaks[breakIdx].start_break = value;
                if ('start_time' in weekdays[dayIdx].breaks[breakIdx]) delete weekdays[dayIdx].breaks[breakIdx].start_time;
            } else if (type === 'break_end') {
                weekdays[dayIdx].breaks[breakIdx].end_break = value;
                if ('end_time' in weekdays[dayIdx].breaks[breakIdx]) delete weekdays[dayIdx].breaks[breakIdx].end_time;
            }
        }
    }

    window.toggleHoliday = toggleHoliday;
    window.addInlineBreak = addInlineBreak;

    // =====================
    // Fetch Clinics by Doctor ID and show sessions for selected clinic
    // =====================
    function fetchClinicsAndSessionsByDoctorId(doctorId, scope, selectedClinicId = null) {
        if (!doctorId) doctorId = null;

        const clinicSelect = scope?.querySelector('#clinic_id');
        const placeholder  = `{{ __('clinic.lbl_select_clinic') }}`;

        if (!clinicSelect) return;

        function resetSelect() {
            clinicSelect.innerHTML = '';
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = placeholder;
            opt.selected = true;
            clinicSelect.appendChild(opt);
        }

        // Isolated select2 functionality for clinic dropdown
        function destroyClinicSelect2() {
            // Clean up custom dropdown if it exists
            if (clinicSelect._customDropdown && clinicSelect._customDropdown.destroy) {
                clinicSelect._customDropdown.destroy();
                delete clinicSelect._customDropdown;
            }
            
            // Remove any existing select2 instances
            if (window.jQuery && window.jQuery(clinicSelect).hasClass('select2-hidden-accessible')) {
                window.jQuery(clinicSelect).select2('destroy');
            }
            // Remove any existing choices instances
            if (clinicSelect.choices && typeof clinicSelect.choices.destroy === 'function') {
                clinicSelect.choices.destroy();
            }
            // Clean up any wrapper elements
            const wrap = clinicSelect.closest('.choices');
            if (wrap) {
                wrap.parentNode.insertBefore(clinicSelect, wrap);
                wrap.remove();
            }
            // Remove any select2 wrapper
            const select2Container = clinicSelect.closest('.select2-container');
            if (select2Container) {
                select2Container.replaceWith(clinicSelect);
            }
            
            // Show the original select element
            clinicSelect.classList.remove('select-hidden');
        }

        function initClinicSelect2() {
            // Use vanilla JavaScript to create a custom searchable dropdown
            if (!clinicSelect) return;

            // Create custom dropdown container
            const dropdownContainer = document.createElement('div');
            dropdownContainer.className = 'clinic-custom-dropdown';

            // Create search input
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.className = 'form-control clinic-search-input';
            searchInput.placeholder = '{{ __("clinic.type_to_search") }}';

            // Create dropdown list
            const dropdownList = document.createElement('div');
            dropdownList.className = 'clinic-dropdown-list select-hidden';

            // Hide original select
            clinicSelect.classList.add('select-hidden');

            // Insert custom dropdown after original select
            clinicSelect.parentNode.insertBefore(dropdownContainer, clinicSelect.nextSibling);
            dropdownContainer.appendChild(searchInput);
            dropdownContainer.appendChild(dropdownList);

            // Populate dropdown with options
            function populateDropdown() {
                dropdownList.innerHTML = '';
                const options = clinicSelect.querySelectorAll('option');
                
                options.forEach((option, index) => {
                    if (option.value === '') return; // Skip placeholder
                    
                    const item = document.createElement('div');
                    item.className = 'clinic-dropdown-item';
                    item.textContent = option.textContent;
                    item.dataset.value = option.value;
                 
                     
                    // Click handler
                    item.addEventListener('click', function() {
                        clinicSelect.value = this.dataset.value;
                        searchInput.value = this.textContent;
                        dropdownList.classList.add('select-hidden');
                        
                        // Trigger change event
                        const changeEvent = new Event('change', { bubbles: true });
                        clinicSelect.dispatchEvent(changeEvent);
                    });
                    
                    dropdownList.appendChild(item);
                });
            }

            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const items = dropdownList.querySelectorAll('.clinic-dropdown-item');
                
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.classList.remove('select-hidden');
                    } else {
                        item.classList.add('select-hidden');
                    }
                });
            });

            // Show/hide dropdown
            searchInput.addEventListener('focus', function() {
                dropdownList.classList.remove('select-hidden');
                populateDropdown();
            });

            searchInput.addEventListener('click', function() {
                dropdownList.classList.remove('select-hidden');
                populateDropdown();
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdownContainer.contains(e.target)) {
                    dropdownList.classList.add('select-hidden');
                }
            });

            // Initialize with current value
            const selectedOption = clinicSelect.querySelector('option:checked');
            if (selectedOption && selectedOption.value !== '') {
                searchInput.value = selectedOption.textContent;
            }

            // Update search input when select value changes
            clinicSelect.addEventListener('change', function() {
                const selectedOption = this.querySelector('option:checked');
                if (selectedOption) {
                    searchInput.value = selectedOption.textContent;
                }
            });

            // Store references for cleanup
            clinicSelect._customDropdown = {
                container: dropdownContainer,
                searchInput: searchInput,
                dropdownList: dropdownList,
                destroy: function() {
                    // Remove the entire custom dropdown container
                    if (this.container && this.container.parentNode) {
                        this.container.parentNode.removeChild(this.container);
                    }
                    // Show the original select element
                    clinicSelect.classList.remove('select-hidden');
                    // Clear any event listeners
                    if (this.searchInput) {
                        this.searchInput.removeEventListener('input', this.searchInput._inputHandler);
                        this.searchInput.removeEventListener('focus', this.searchInput._focusHandler);
                    }
                }
            };
        }

        destroyClinicSelect2();
        resetSelect();

        if (doctorId === null) {
            initClinicSelect2();
            return;
        }

        const base = "{{ route('backend.doctor-session.doctor_clinics') }}";
        const url  = `${base}?doctor_id=${encodeURIComponent(doctorId)}`;

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(json => {
                resetSelect();
                let items = [];
                if (json && Array.isArray(json.clinic_mapping_data)) items = json.clinic_mapping_data;
                else if (json && Array.isArray(json.data)) items = json.data;
                else if (Array.isArray(json)) items = json;

                if (!items.length) {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = '{{ __("clinic.no_clinic_found") }}';
                    opt.disabled = true;
                    clinicSelect.appendChild(opt);
                } else {
                    for (const m of items) {
                        const val = String(m.clinic_id ?? m.id);
                        const label = m.clinic_name ?? m.name ?? `{{ __('clinic.clinic_number') }}${val}`;
                        const opt = document.createElement('option');
                        opt.value = val;
                        opt.textContent = label;
                        // Remove the selected attribute here, will set after plugin init
                        clinicSelect.appendChild(opt);
                    }
                }
                initClinicSelect2();

                // If only one clinic, select it and show its sessions
                if (items.length === 1) {
                    clinicSelect.value = String(items[0].clinic_id ?? items[0].id);
                    // Update custom dropdown display
                    if (clinicSelect._customDropdown) {
                        const selectedOption = clinicSelect.querySelector('option:checked');
                        if (selectedOption) {
                            clinicSelect._customDropdown.searchInput.value = selectedOption.textContent;
                        }
                    }
                    showSessionsForClinic(json, clinicSelect.value);
                } else if (selectedClinicId) {
                    clinicSelect.value = selectedClinicId;
                    // Update custom dropdown display
                    if (clinicSelect._customDropdown) {
                        const selectedOption = clinicSelect.querySelector('option:checked');
                        if (selectedOption) {
                            clinicSelect._customDropdown.searchInput.value = selectedOption.textContent;
                        }
                    }
                    showSessionsForClinic(json, selectedClinicId);
                } else {
                    weekdays = getDefaultWeekdays();
                    renderWeekdays();
                }

                // Listen for clinic change
                clinicSelect.onchange = function() {
                    showSessionsForClinic(json, this.value);
                };
            })
            .catch(err => {
                console.error('fetchClinicsAndSessionsByDoctorId failed:', err);
                resetSelect();
                initClinicSelect2();
                weekdays = getDefaultWeekdays();
                renderWeekdays();
            });
    }

    // Show sessions for selected clinic from the fetched JSON
    function showSessionsForClinic(json, clinicId) {
        if (!clinicId) {
            weekdays = getDefaultWeekdays();
            renderWeekdays();
            return;
        }
        // sessions is an object keyed by clinic_id
        if (json && json.sessions && json.sessions[clinicId]) {
            weekdays = json.sessions[clinicId].map(day => ({
                day: day.day,
                start_time: day.start_time ?? '09:00',
                end_time: day.end_time ?? '18:00',
                is_holiday: !!day.is_holiday,
                breaks: Array.isArray(day.breaks)
                    ? day.breaks.map(br => ({
                        start_break: br.start_break ?? '',
                        end_break: br.end_break ?? '',
                    }))
                    : []
            }));
        } else {
            weekdays = getDefaultWeekdays();
        }
        renderWeekdays();
    }

    // =====================
    // Hook: When Offcanvas opens → Load clinics for that doctor and show sessions for selected clinic
    // =====================
    document.addEventListener('shown.bs.offcanvas', function (event) {
        const panel = event.target;
        if (!panel.contains(document.getElementById('doctor-sessions-form'))) return;

        const opener  = event.relatedTarget;
        const doctorId = opener?.getAttribute('data-doctor-id') || '';
        const sessionId = opener?.getAttribute('data-session-id') || '';
        const hiddenInput = document.getElementById('doctor_id');
        const sessionInput = document.getElementById('session_id');
        if (hiddenInput) hiddenInput.value = doctorId;
        if (sessionInput) sessionInput.value = sessionId || '';

        // Also set doctor id on the submit button for fallback
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) submitBtn.setAttribute('data-doctor-id', doctorId);

        // Always fetch clinics and sessions for this doctor
        fetchClinicsAndSessionsByDoctorId(doctorId, panel);
    });

    // ========== Event Delegation for dynamic elements ==========
    document.getElementById('weekdays-container').addEventListener('change', function(e) {
        const target = e.target;
        // Holiday checkbox
        if (target.classList.contains('holiday-checkbox')) {
            const idx = parseInt(target.getAttribute('data-idx'));
            toggleHoliday(idx);
        }
        // Start/end time for day
        if (target.hasAttribute('data-type') && target.hasAttribute('data-idx')) {
            const idx = parseInt(target.getAttribute('data-idx'));
            const type = target.getAttribute('data-type');
            updateTime(idx, type, target.value);
        }
        // Break time
        if (target.hasAttribute('data-day-idx') && target.hasAttribute('data-break-idx') && target.hasAttribute('data-type')) {
            const dayIdx = parseInt(target.getAttribute('data-day-idx'));
            const breakIdx = parseInt(target.getAttribute('data-break-idx'));
            const type = target.getAttribute('data-type');
            updateBreakTime(dayIdx, breakIdx, type, target.value);
        }
    });

    document.getElementById('weekdays-container').addEventListener('click', function(e) {
        // Add break
        if (e.target.classList.contains('add-break-btn')) {
            const idx = parseInt(e.target.getAttribute('data-idx'));
            addInlineBreak(idx);
        }
        // Remove break
        if (e.target.classList.contains('remove-break-btn') || (e.target.closest && e.target.closest('.remove-break-btn'))) {
            const btn = e.target.classList.contains('remove-break-btn') ? e.target : e.target.closest('.remove-break-btn');
            const dayIdx = parseInt(btn.getAttribute('data-day-idx'));
            const breakIdx = parseInt(btn.getAttribute('data-break-idx'));
            removeBreak(dayIdx, breakIdx);
        }
    });

    // ========== Form Submission ==========
    document.getElementById('doctor-sessions-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = document.getElementById('submit-btn');
        const spinner = document.getElementById('submit-spinner');
        const clinicId = form.clinic_id.value;
        const sessionId = form.session_id.value;

        // Always get doctor id from hidden input, fallback to button data attribute if empty
        let doctorId = form.doctor_id.value;
        if (!doctorId) {
            doctorId = submitBtn.getAttribute('data-doctor-id') || '';
            form.doctor_id.value = doctorId; // update hidden input for consistency
        }

        // Validation
        let valid = true;
        if (!clinicId) {
            document.getElementById('clinic_id_error').textContent = '{{ __("clinic.lbl_clinic_required") }}';
            document.getElementById('clinic_id_error').classList.remove('select-hidden');
            valid = false;
        } else {
            document.getElementById('clinic_id_error').classList.add('select-hidden');
        }
        if (!doctorId) {
            alert('{{ __("clinic.doctor_id_missing") }}');
            valid = false;
        }
        if (!valid) return;

        // --- Fix: Always get latest breaks/times from DOM before submit ---
        function getWeekdaysFromDOM() {
            const result = [];
            for (let i = 0; i < weekdays.length; i++) {
                const dayObj = Object.assign({}, weekdays[i]);
                // Get is_holiday from checkbox
                const holidayCheckbox = document.getElementById('holiday_' + i);
                dayObj.is_holiday = holidayCheckbox ? holidayCheckbox.checked : false;

                // If not holiday, get start/end time from inputs
                if (!dayObj.is_holiday) {
                    const startInput = document.getElementById('start_time_' + i);
                    const endInput = document.getElementById('end_time_' + i);
                    dayObj.start_time = startInput ? startInput.value : dayObj.start_time;
                    dayObj.end_time = endInput ? endInput.value : dayObj.end_time;
                }

                // Get breaks from DOM
                dayObj.breaks = [];
                if (!dayObj.is_holiday) {
                    const breakStartInputs = document.querySelectorAll(`input[data-day-idx="${i}"][data-type="break_start"]`);
                    const breakEndInputs = document.querySelectorAll(`input[data-day-idx="${i}"][data-type="break_end"]`);
                    for (let b = 0; b < breakStartInputs.length; b++) {
                        const startVal = breakStartInputs[b].value;
                        const endVal = breakEndInputs[b] ? breakEndInputs[b].value : '';
                        if (startVal && endVal) {
                            dayObj.breaks.push({ start_break: startVal, end_break: endVal });
                        }
                    }
                }
                if (Array.isArray(dayObj.breaks)) {
                    dayObj.breaks = dayObj.breaks.map(br => {
                        return {
                            start_break: br.start_break ?? '',
                            end_break: br.end_break ?? ''
                        };
                    });
                }
                result.push(dayObj);
            }
            return result;
        }

        // Prepare data
        const data = {
            doctor_id: doctorId,
            clinic_id: clinicId,
            weekdays: getWeekdaysFromDOM()
        };
        if (sessionId) {
            data.session_id = sessionId;
        }

        // Submit via AJAX
        submitBtn.disabled = true;
        spinner.classList.remove('select-hidden');
        let url, method;
        if (sessionId) {
            url = "{{ route('backend.doctor-session.update', ['doctor_session' => 'SESSION_ID']) }}".replace('SESSION_ID', encodeURIComponent(sessionId));
            method = 'PUT';
        } else {
            url = "{{ route('backend.doctor-session.store') }}";
            method = 'POST';
        }
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(async response => {
            spinner.classList.add('select-hidden');
            submitBtn.disabled = false;
            if (response.ok) {
                if (window.bootstrap && window.bootstrap.Offcanvas) {
                    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(form.closest('.offcanvas'));
                    offcanvas.hide();
                } else {
                    document.querySelector('[data-bs-dismiss="offcanvas"]')?.click();
                }
                document.dispatchEvent(new CustomEvent('doctorSessionSaved', { detail: data }));
            } else {
                const json = await response.json();
                if (json && json.errors) {
                    if (json.errors.clinic_id) {
                        document.getElementById('clinic_id_error').textContent = json.errors.clinic_id[0];
                        document.getElementById('clinic_id_error').classList.remove('select-hidden');
                    }
                    if (json.errors.doctor_id) {
                        alert(json.errors.doctor_id[0]);
                    }
                }
            }
        })
        .catch(err => {
            spinner.classList.add('select-hidden');
            submitBtn.disabled = false;
            alert('{{ __("clinic.save_error") }}');
            console.error(err);
        });
    });

    // Initial render
    renderWeekdays();

    // Disable scroll on time inputs
    document.addEventListener('wheel', function(e) {
        if (e.target.type === 'time') {
            e.preventDefault();
        }
    }, { passive: false });

    // Also disable scroll on time inputs when they are focused
    document.addEventListener('focusin', function(e) {
        if (e.target.type === 'time') {
            e.target.addEventListener('wheel', function(event) {
                event.preventDefault();
            }, { passive: false });
        }
    });
});
</script>
@endpush

 