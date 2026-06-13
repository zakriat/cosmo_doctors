{{ html()->form('POST', route('landing_page_settings_updates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id', $landing_page->id)->placeholder('id')->class('form-control') }}
{{ html()->hidden('type', $tabpage)->placeholder('id')->class('form-control') }}

<div class="form-group">
    <div class="form-control d-flex align-items-center justify-content-between">
        <label class="mb-0" for="enable_section_7">Enable FAQ Section</label>
        <div class="form-check form-switch m-0">
            <input type="checkbox" class="form-check-input section_7" name="status" id="section_7" data-type="section_7"
                {{ !empty($landing_page) && $landing_page->status == 1 ? 'checked' : '' }}>
            <label class="custom-control-label" for="section_7"></label>
        </div>
    </div>
</div>
<div class="form-section" id='enable_section_7'>
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

    <div class="form-group">
        {{ html()->label(trans('messages.description') . ' <span class="text-danger">*</span>', 'description')->class('form-control-label')->attribute('for', 'description') }}
        {{ html()->text('description', old('description'))->id('description')->placeholder(trans('messages.description'))->class('form-control') }}
        <small class="help-block with-errors text-danger"></small>
    </div>

</div>


{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
{{ html()->form()->close() }}

<script>
    var enable_section_7 = $("input[name='status']").prop('checked');
    checkSection7(enable_section_7);

    $('#section_7').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection7(value);

    });

    function checkSection7(value) {
        if (value == true) {
            $('#enable_section_7').removeClass('d-none');
            $('#title').prop('required', true);
            $('#subtitle').prop('required', true);
            $('#description').prop('required', true);
        } else {
            $('#enable_section_7').addClass('d-none');
            $('#title').prop('required', false);
            $('#subtitle').prop('required', false);
            $('#description').prop('required', false);
        }
    }

    var get_value = $('input[name="status"]:checked').data("type");
    getConfig(get_value)
    $('.section_7').change(function() {
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
                var section_7 = title = subtitle = description = '';

                if (response) {
                    if (response.data.key == 'section_7') {
                        obj = JSON.parse(response.data.value);
                    }
                    if (obj !== null) {
                        var title = obj.title;
                        var subtitle = obj.subtitle;
                        var description = obj.description;
                    }
                    $('#title').val(title);
                    $('#subtitle').val(subtitle);
                    $('#description').val(description);

                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }
</script>
