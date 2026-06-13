{{ html()->form('POST', route('landing_page_settings_updates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id', $landing_page->id)->placeholder('id')->class('form-control') }}
{{ html()->hidden('type', $tabpage)->placeholder('id')->class('form-control') }}

<div class="form-group">
    <div class="form-control d-flex align-items-center justify-content-between">
        <label class="mb-0" for="enable_section_8">Enable Rating Section</label>
        <div class="form-check form-switch m-0">
            <input type="checkbox" class="form-check-input section_8" name="status" id="section_8" data-type="section_8"
                {{ !empty($landing_page) && $landing_page->status == 1 ? 'checked' : '' }}>
            <label class="custom-control-label" for="section_8"></label>
        </div>
    </div>
</div>

{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
{{ html()->form()->close() }}

<script>
    var enable_section_8 = $("input[name='status']").prop('checked');
    checkSection7(enable_section_8);

    $('#section_8').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection7(value);

    });

    function checkSection7(value) {
        if (value == true) {
            $('#enable_section_8').removeClass('d-none');
        } else {
            $('#enable_section_8').addClass('d-none');
        }
    }

    var get_value = $('input[name="status"]:checked').data("type");
    getConfig(get_value)
    $('.section_8').change(function() {
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
                var section_8 = '';

                if (response) {
                    if (response.data.key == 'section_8') {
                        obj = JSON.parse(response.data.value);
                    }
                    // if (obj !== null) {
                    //     var title = obj.title;
                    //     var subtitle = obj.subtitle;
                    //     var description = obj.description;
                    // }
                    // $('#title').val(title);
                    // $('#subtitle').val(subtitle);
                    // $('#description').val(description);

                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }
</script>
