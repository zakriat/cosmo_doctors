{{ html()->form('POST', route('footer_page_settings'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('myForm')->open() }}

{{ html()->hidden('id', $landing_page_data ?? null)->placeholder('id')->class('form-control') }}
{{ html()->hidden('type', $page)->placeholder('id')->class('form-control') }}

<div class="form-group">
    <div class="form-control d-flex justify-content-between">
        <label class="mb-0" for="enable_footer_setting">{{ __('messages.enable_footer_setting') }}</label>
        <div class="form-check form-switch m-0">
            <input type="checkbox" class="form-check-input footer_setting" name="status" id="footer_setting"
                data-type="footer_setting"
                {{ !empty($landing_page_data) && $landing_page_data->status ? 'checked' : '' }}>
            <label class="custom-control-label" for="footer_setting"></label>
        </div>
    </div>
</div>

<div class="row" id='enable_footer_setting'>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body setting-pills">
                <div class="form-group">
                    <div class="form-control d-flex align-items-center justify-content-between">
                        <label class="mb-0" for="enable_quick_link">{{ __('messages.enable_quick_link') }}</label>
                        <div class="form-check form-switch m-0">
                            <input type="checkbox" class="form-check-input" name="enable_quick_link"
                                id="enable_quick_link"
                                {{ !empty($landing_page_data->enable_quick_link) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="enable_quick_link"></label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-control d-flex align-items-center justify-content-between">
                        <label class="mb-0"
                            for="enable_top_category">{{ __('messages.enable_top_categories') }}</label>
                        <div class="form-check form-switch m-0">
                            <input type="checkbox" class="form-check-input" name="enable_top_category"
                                id="enable_top_category"
                                {{ !empty($landing_page_data->enable_top_category) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="enable_top_category"></label>
                        </div>
                    </div>
                </div>

                @php
                    $selectedCategoryIds = [];
                    $categoryNames = [];
                    if ($landing_page_data) {
                        $decodedData = json_decode($landing_page_data->value, true);
                        $selectedCategoryIds = isset($decodedData['category_id']) ? $decodedData['category_id'] : [];
                        if ($selectedCategoryIds) {
                            $categoryNames = \Modules\Clinic\Models\ClinicsCategory::where('status', 1)
                                ->whereIn('id', $selectedCategoryIds)
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                    }
                @endphp

                <div class="form-group" id="enable_select_category">
                    {{ html()->label(__('messages.select_category', ['select' => __('messages.category')]) . ' <span class="text-danger">*</span>', 'category_id[]')->class('form-control-label') }}
                    <br />
                    {{ html()->select('category_id[]', $categoryNames, old('category_id', $selectedCategoryIds))->class('select2js form-control category_id')->id('category_id')->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.category')]))->attribute('data-ajax--url', route('ajax-list', ['type' => 'category']))->multiple() }}
                </div>
                <div class="form-group">
                    <div class="form-control d-flex align-items-center justify-content-between">
                        <label class="mb-0" for="enable_top_service">{{ __('messages.enable_top_services') }}</label>
                        <div class="form-check form-switch m-0">
                            <input type="checkbox" class="form-check-input" name="enable_top_service"
                                id="enable_top_service"
                                {{ !empty($landing_page_data->enable_top_service) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="enable_top_service"></label>
                        </div>
                    </div>
                </div>
                @php
                    $selectedServiceIds = [];
                    $serviceNames = [];

                    if ($landing_page_data) {
                        $decodedData = json_decode($landing_page_data->value, true);
                        $selectedServiceIds = isset($decodedData['service_id']) ? $decodedData['service_id'] : [];
                        if ($selectedServiceIds) {
                            $serviceNames = \Modules\Clinic\Models\ClinicsService::whereIn('id', $selectedServiceIds)
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                    }
                @endphp
                <div class="form-group col-md-12" id='enable_select_provider'>
                    {{ html()->label(__('messages.select_service', ['select' => __('messages.service')]) . ' <span class="text-danger">*</span>', 'service_id[]')->class('form-control-label') }}
                    <br />
                    {{ html()->select('service_id[]', $serviceNames, old('service_id', $selectedServiceIds))->class('select2js form-control service_id')->id('service_id')->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.provider')]))->attribute('data-ajax--url', route('ajax-list', ['type' => 'service']))->multiple() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end') }}
{{ html()->form()->close() }}

<script>
    var enable_footer_setting = $("input[name='status']").prop('checked');
    checkSection1(enable_footer_setting);

    $('#footer_setting').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection1(value);
    });

    function checkSection1(value) {
        if (value == true) {
            $('#enable_footer_setting').removeClass('d-none');
        } else {
            $('#enable_footer_setting').addClass('d-none');
        }
    }

    var enable_top_category = $("input[name='enable_top_category']").prop('checked');
    checkEnableService(enable_top_category);

    $('#enable_top_category').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkEnableService(value);
    });

    function checkEnableService(value) {
        if (value == true) {
            $('#enable_select_category').removeClass('d-none');
            $('#category_id').prop('required', true);
        } else {
            $('#enable_select_category').addClass('d-none');
            $('#category_id').prop('required', false);
        }
    }


    ///// open select popular provider ///////////

    var enable_top_service = $("input[name='enable_top_service']").prop('checked');
    checkEnableProvider(enable_top_service);

    $('#enable_top_service').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkEnableProvider(value);
    });

    function checkEnableProvider(value) {
        if (value == true) {
            $('#enable_select_provider').removeClass('d-none');
            $('#service_id').prop('required', true);
        } else {
            $('#enable_select_provider').addClass('d-none');
            $('#service_id').prop('required', false);
        }
    }

    $(document).ready(function() {
        $('.select2js').select2();
        $('#category_id').on('change', function() {

            var selectedOptions = $(this).val();
            if (selectedOptions && selectedOptions.length > 6) {
                selectedOptions.pop();
                $(this).val(selectedOptions).trigger('change.select2');
            }
        });
        $('#service_id').on('change', function() {

            var selectedOptions = $(this).val();
            if (selectedOptions && selectedOptions.length > 6) {
                selectedOptions.pop();
                $(this).val(selectedOptions).trigger('change.select2');
            }
        });

    });

    $(document).on('click', '[data-toggle="tabajax"]', function(e) {
        e.preventDefault();
        var selectDiv = this;
        ajaxMethodCall(selectDiv);
    });
    
    function ajaxMethodCall(selectDiv) {
        var $this = $(selectDiv),
            loadurl = $this.attr('data-href'),
            targ = $this.attr('data-target'),
            id = selectDiv.id || '';

        // Get CSRF token from the meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: loadurl,
            type: 'POST',
            data: {
                // Add CSRF token to the request body
                _token: csrfToken
            },
            success: function(data) {
                $(targ).html(data);
                $('form').append('<input type="hidden" name="active_tab" value="'+id+'" />');
            },
            error: function(xhr, status, error) {
                console.log("Error: " + status + ", " + error);
            }
        });

        $this.tab('show');
        return false;
    }
</script>
