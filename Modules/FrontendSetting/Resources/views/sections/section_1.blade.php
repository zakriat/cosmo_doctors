<div class="section-content">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('landing_page_settings_updates') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <input type="hidden" name="id" value="{{ $landing_page->id }}" class="form-control"
                        placeholder="id">
                    <input type="hidden" name="type" value="section_1" class="form-control" placeholder="section_1">

                    <div class="form-group">
                        <div class="form-control d-flex align-items-center justify-content-between">
                            <label class="mb-0" for="enable_section_1">Enable Home Slider</label>
                            <div class="form-check form-switch m-0">
                                <input type="checkbox" class="form-check-input section_1" name="status" id="section_1"
                                    data-type="section_1"
                                    {{ !empty($landing_page) && $landing_page->status == 1 ? 'checked' : '' }}>
                                <label class="custom-control-label" for="section_1"></label>
                            </div>
                        </div>
                    </div>

                    <div class="row" id='enable_section_1'>
                        <div class="form-group">
                            <label for="title" class="form-control-label">Title<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                placeholder="{{ trans('messages.title') }}" class="form-control">
                            <small class="help-block with-errors text-danger"></small>
                        </div>

                        <div class="form-group">
                            <div class="form-control d-flex align-items-center justify-content-between">
                                <label for="enable_search" class="mb-0">Enable Search</label>
                                <div class="form-check form-switch m-0">
                                    <input type="checkbox" class="form-check-input" name="enable_search"
                                        id="enable_search"> <label class="form-check-label" for="enable_search"></label>
                                </div>
                            </div>
                        </div>


                        <div class="form-group mt-3">
                            <label for="instant_link" class="form-label">Instant Link</label>
                            <select class="form-select select2" id="instant_link" name="instant_link[]" multiple>
                                <option value="doctors"
                                    {{ in_array('doctors', old('instant_link', [])) ? 'selected' : '' }}>Doctor</option>
                                <option value="services"
                                    {{ in_array('services', old('instant_link', [])) ? 'selected' : '' }}>Service
                                </option>
                                <option value="clinics"
                                    {{ in_array('clinics', old('instant_link', [])) ? 'selected' : '' }}>Clinic</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="form-control d-flex align-items-center justify-content-between">
                                <label for="enable_quick_booking" class="mb-0">Enable Quick Booking</label>
                                <div class="form-check form-switch m-0">
                                    <input type="checkbox" class="form-check-input" name="enable_quick_booking"
                                        id="enable_quick_booking">
                                    <label class="custom-control-label" for="enable_quick_booking"></label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#instant_link').select2();
    });

    var enable_section_1 = $("input[name='status']").prop('checked');
    checkSection1(enable_section_1);

    $('#section_1').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection1(value);
    });

    function checkSection1(value) {
        if (value == true) {
            $('#enable_section_1').removeClass('d-none');
            $('#title').prop('required', true);
        } else {
            $('#enable_section_1').addClass('d-none');
            $('#title').prop('required', false);
        }
    }

    var get_value = $('input[name="status"]:checked').data("type");
    getConfig(get_value)
    $('.section_1').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        type = $(this).data("type");
        getConfig(type)

    });

    function getConfig(type) {
        var _token = $('meta[name="csrf-token"]').attr('content');
        var page = "{{ $tabpage }}";
        var getDataRoute = "{{ route('getLandingLayoutPageConfig') }}";
        $.ajax({
            url: getDataRoute,
            type: "POST",
            data: {
                type: type,
                page: page,
                _token: _token
            },
            success: function(response) {
                var obj = '';
                var section_1 = title = enable_search = enable_quick_booking = '';
                var instant_link = [];

                if (response) {
                    if (response.data.key == 'section_1') {
                        obj = JSON.parse(response.data.value);
                    }
                    if (obj !== null) {
                        var title = obj.title;
                        var enable_search = obj.enable_search;
                        var instant_link = obj.instant_link;
                        var enable_quick_booking = obj.enable_quick_booking;
                        console.log(instant_link);
                    }
                    $('#title').val(title)
                    $('#enable_search').prop('checked', enable_search == 'on');
                    if (instant_link && Array.isArray(instant_link)) {
                        $('#instant_link').val(instant_link).trigger('change');
                    }
                    $('#enable_quick_booking').prop('checked', enable_quick_booking == 'on');
                    $('#instant_link').select2();
                    $('#instant_link').trigger('change');
                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }
</script>
