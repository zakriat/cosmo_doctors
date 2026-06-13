{{ html()->form('POST', route('landing_page_settings_updates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id', $landing_page->id)->placeholder('id')->class('form-control') }}
{{ html()->hidden('type', $tabpage)->placeholder('id')->class('form-control') }}

<div class="form-group">
    <div class="form-control d-flex align-items-center justify-content-between">
        <label class="mb-0" for="enable_section_6">Enable Doctor section</label>
        <div class="form-check form-switch m-0">
            <input type="checkbox" class="form-check-input section_6" name="status" id="section_6" data-type="section_6"
                {{ !empty($landing_page) && $landing_page->status == 1 ? 'checked' : '' }}>
            <label class="custom-control-label" for="section_6"></label>
        </div>
    </div>
</div>
<div class="form-section" id='enable_section_6'>
    <div class="form-group">
        {{ html()->label(trans('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label')->attribute('for', 'title') }}
        {{ html()->text('title', old('title'))->id('title')->placeholder(trans('messages.title'))->class('form-control') }}
        <small class="help-block with-errors text-danger"></small>
    </div>

    <div class="form-group">
        {{ html()->label(trans('messages.subtitle') . ' <span class="text-danger">*</span>', 'subtitle')->class('form-control-label')->attribute('for', 'subtitle') }}
        {{ html()->text('subtitle', old('subtitle'))->id('subtitle')->placeholder(trans('messages.subtitle'))->class('form-control') }}
        <small class="help-block with-errors text-danger"></small>
    </div>

    <div class="form-group" id="enable_select_service">
        {{ html()->label(__('messages.select_doctor_name', ['select' => __('messages.doctor')]) . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
        <br />
        {{ html()->select('doctor_id[]', [], old('doctor_id'))->class('select2js form-control doctor_id')->id('doctor_id')->attribute('data-placeholder', __('messages.select_doctor_name', ['select' => __('messages.doctor')]))->attribute('data-ajax--url', route('ajax-list', ['type' => 'doctor']))->multiple() }}
    </div>

</div>


{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
{{ html()->form()->close() }}

<script>
    var enable_section_6 = $("input[name='status']").prop('checked');
    checkSection6(enable_section_6);

    $('#section_6').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection6(value);

    });

    function checkSection6(value) {
        if (value == true) {
            $('#enable_section_6').removeClass('d-none');
            $('#title').prop('required', true);
            $('#subtitle').prop('required', true);
            $('#doctor_id').prop('required', true).trigger('change.select2');
        } else {
            $('#enable_section_6').addClass('d-none');
            $('#subtitle').prop('required', false);
            $('#title').prop('required', false);
            $('#doctor_id').prop('required', false).trigger('change.select2');
        }
    }

    ///// open select popular category ///////////
    $(document).ready(function() {
        $('.select2js').select2();

        $('#doctor_id').on('change', function() {
            var selectedOptions = $(this).val();
            if (selectedOptions && selectedOptions.length > 16) {
                selectedOptions.pop();
                $(this).val(selectedOptions).trigger('change.select2');
            }
        });


    });

    var get_value = $('input[name="status"]:checked').data("type");
    getConfig(get_value)
    $('.section_6').change(function() {
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
                var section_6 = title = subtitle = clinic_ids = '';

                if (response) {
                    if (response.data.key == 'section_6') {
                        obj = JSON.parse(response.data.value);
                    }
                    if (obj !== null) {
                        var title = obj.title;
                        var subtitle = obj.subtitle;
                        var clinic_ids = obj.doctor_id;
                    }
                    $('#title').val(title);
                    $('#subtitle').val(subtitle);
                    loadDoctor(clinic_ids);

                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }


    function loadDoctor(clinic_ids) {
        var service_route = "{{ route('ajax-list', ['type' => 'doctor']) }}";
        service_route = service_route.replace('amp;', '');
        var minRating = 3;
        var maxRating = 5;
        $.ajax({
            url: service_route,
            data: {
                top_rated: {
                    min: minRating,
                    max: maxRating
                },
                ids: clinic_ids,
            },
            success: function(result) {
                $('#doctor_id').select2({
                    width: '100%',
                    placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.doctor')]) }}",
                    data: result.results
                });
                $('#doctor_id').val(clinic_ids).trigger('change');

            }
        });

    }
</script>
