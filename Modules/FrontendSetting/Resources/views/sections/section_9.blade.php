{{ html()->form('POST', route('landing_page_settings_updates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id', $landing_page->id)->placeholder('id')->class('form-control') }}
{{ html()->hidden('type', $tabpage)->placeholder('id')->class('form-control') }}

<div class="form-group">
    <div class="form-control d-flex align-items-center justify-content-between">
        <label class="mb-0" for="enable_section_9">Enable Blog Section</label>
        <div class="form-check form-switch m-0">
            <input type="checkbox" class="form-check-input section_9" name="status" id="section_9" data-type="section_9"
                {{ !empty($landing_page) && $landing_page->status == 1 ? 'checked' : '' }}>
            <label class="custom-control-label" for="section_9"></label>
        </div>
    </div>
</div>
<div class="form-section" id='enable_section_9'>
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

    <div class="form-group" id='enable_select_blog'>
        {{ html()->label(__('messages.select_blog_name', ['select' => __('messages.blog')]) . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
        <br />
        {{ html()->select('blog_id[]', [], old('blog_id'))->class('select2js form-control blog_id')->id('blog_id')->attribute('data-placeholder', __('messages.select_blog_name', ['select' => __('messages.blog')]))->attribute('data-ajax--url', route('ajax-list', ['type' => 'blog', 'is_featured' => 1]))->multiple() }}
    </div>

</div>


{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
{{ html()->form()->close() }}

<script>
    var enable_section_9 = $("input[name='status']").prop('checked');
    checkSection7(enable_section_9);

    $('#section_9').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection7(value);

    });

    function checkSection7(value) {
        if (value == true) {
            $('#enable_section_9').removeClass('d-none');
            $('#title').prop('required', true);
            $('#subtitle').prop('required', true);
            $('#blog_id').prop('required', true).trigger('change.select2');
        } else {
            $('#enable_section_9').addClass('d-none');
            $('#title').prop('required', false);
            $('#subtitle').prop('required', false);
            $('#blog_id').prop('required', false).trigger('change.select2');
        }
    }

    var get_value = $('input[name="status"]:checked').data("type");
    getConfig(get_value)
    $('.section_9').change(function() {
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
                var section_9 = title = subtitle = description = '';

                if (response) {
                    if (response.data.key == 'section_9') {
                        obj = JSON.parse(response.data.value);
                    }
                    if (obj !== null) {
                        var title = obj.title;
                        var subtitle = obj.subtitle;
                        var blog_ids = obj.blog_id;
                    }
                    $('#title').val(title);
                    $('#subtitle').val(subtitle);
                    loadBlog(blog_ids);
                    if (blog_ids && blog_ids.length > 0) {}

                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }

    function loadBlog(blog_ids) {
        console.log(blog_ids);
        var blog_route = "{{ route('ajax-list', ['type' => 'blog']) }}";
        // if (blog_ids && blog_ids.length > 0) {
        $.ajax({
            url: blog_route,
            data: {
                is_featured: 1,
                ids: blog_ids
            },
            success: function(result) {
                $('#blog_id').select2({
                    width: '100%',
                    placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.blog')]) }}",
                    data: result.results
                });
                $('#blog_id').val(blog_ids).trigger('change');
            }
        });
        // } else {
        //     $('#blog_id').empty().trigger('change');
        // }
    }
</script>
