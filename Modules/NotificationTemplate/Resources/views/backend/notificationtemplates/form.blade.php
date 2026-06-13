
 <div class="row">
    <div class="col-lg-12">
        <div class="card card-block card-stretch">
            <div class="card-body p-0">
                <!-- <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                    <h5 class="font-weight-bold">{{ $pageTitle ?? __('messages.edit') }}</h5>
                    <a href="{{ route('backend.notification-templates.index') }}" class="float-right btn btn-sm btn-primary">
                        <i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}
                    </a>
                </div> -->
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <form action="{{ route('backend.notification-templates.update', $data->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <input type="hidden" name="id" value="{{ $data->id ?? null }}">
            <input type="hidden" name="type" value="{{ $data->type ?? null }}">
            <input type="hidden" name="defaultNotificationTemplateMap[template_id]" value="{{ $data->id ?? null }}">

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('Type') }} : <span class="text-danger">*</span></label>
                        <select name="type" class="select2 form-select" id="type" data-ajax--url="{{ route('backend.notificationtemplates.ajax-list', ['type' => 'constants_key', 'data_type' => 'notification_type']) }}" data-ajax--cache="true" required disabled>
                            @if (isset($data->type))
                                <option value="{{ $data->type }}" selected>{{ $data->constant->name ?? '' }}</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('To') }} :</label><br>
                        <select name="to[]" id="toSelect" class="select2 form-select" data-ajax--url="{{ route('backend.notificationtemplates.ajax-list', ['type' => 'constants_key', 'data_type' => 'notification_to']) }}" data-ajax--cache="true" multiple>
                            @if (isset($data) && $data->to != null)
                                @foreach (json_decode($data->to) as $to)
                                    <option value="{{ $to }}" selected>{{ $to }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        @php
                            $toValues = json_decode($data->to, true) ?? [];
                        @endphp
                        <label for="user_type">{{ __('messages.user_type') }} <span class="text-danger">*</span></label>
                        <select name="defaultNotificationTemplateMap[user_type]" id="userTypeSelect" class="select2 form-select" required>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">{{ __('messages.status') }} :</label>
                        <select class="form-control" name="status" id="status">
                            <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        <label>{{ __('Parameters') }} :</label><br>
                        <div class="main_form">
                            @if (isset($buttonTypes))
                                @include('notificationtemplate::backend.notificationtemplates.perameters-buttons', ['buttonTypes' => $buttonTypes])
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mt-3">
                    <div class="form-group mb-3">
                        <h4>{{ __('messages.notification_template') }}</h4>
                    </div>
                    <div class="form-group">
                        <label class="float-left">{{ __('messages.subject') }} :</label>
                        <input type="text" name="defaultNotificationTemplateMap[subject]" value="{{ $data->defaultNotificationTemplateMap['subject'] ?? '' }}" class="form-control">
                        <input type="hidden" name="defaultNotificationTemplateMap[status]" value="1" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.template') }} :</label>
                        <input type="hidden" name="defaultNotificationTemplateMap[language]" value="en">
                        <textarea name="defaultNotificationTemplateMap[template_detail]" class="form-control textarea tinymce-template" id="mytextarea">{{ $data->defaultNotificationTemplateMap['template_detail'] ?? '' }}</textarea>
                    </div>
                </div>

                <div class="col-md-6 mt-3">
                    <div class="form-group mb-3">
                        <h4>{{ __('messages.mail_template') }}</h4>
                    </div>
                    <div class="form-group">
                        <label class="float-left">{{ __('messages.subject') }} :</label>
                        <input type="text" name="defaultNotificationTemplateMap[mail_subject]" value="{{ $data->defaultNotificationTemplateMap['mail_subject'] ?? '' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.template') }} :</label>
                        <input type="hidden" name="defaultNotificationTemplateMap[language]" value="en">
                        <textarea name="defaultNotificationTemplateMap[mail_template_detail]" class="form-control textarea tinymce-template" id="mytextarea_mail">{{ $data->defaultNotificationTemplateMap['mail_template_detail'] ?? '' }}</textarea>
                    </div>
                </div>

                <div class="col-md-6 mt-5">
                    <div class="form-group mb-3">
                        <h4>{{ __('messages.sms_template') }}</h4>
                    </div>
                    <div class="form-group">
                        <label class="float-left">{{ __('messages.subject') }} :</label>
                        <input type="text" name="defaultNotificationTemplateMap[sms_subject]" value="{{ $data->defaultNotificationTemplateMap['sms_subject'] ?? '' }}" class="form-control">
                        <input type="hidden" name="defaultNotificationTemplateMap[status]" value="1" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.template') }} :</label>
                        <input type="hidden" name="defaultNotificationTemplateMap[language]" value="en">
                        <textarea name="defaultNotificationTemplateMap[sms_template_detail]" class="form-control textarea tinymce-template" id="mytextarea_sms">{{ $data->defaultNotificationTemplateMap['sms_template_detail'] ?? '' }}</textarea>
                    </div>
                </div>

                <div class="col-md-6 mt-5">
                    <div class="form-group mb-3">
                        <h4>{{ __('messages.whatsapp_template') }}</h4>
                    </div>
                    <div class="form-group">
                        <label class="float-left">{{ __('messages.subject') }} :</label>
                        <input type="text" name="defaultNotificationTemplateMap[whatsapp_subject]" value="{{ $data->defaultNotificationTemplateMap['whatsapp_subject'] ?? '' }}" class="form-control">
                        <input type="hidden" name="defaultNotificationTemplateMap[status]" value="1" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.template') }} :</label>
                        <input type="hidden" name="defaultNotificationTemplateMap[language]" value="en">
                        <textarea name="defaultNotificationTemplateMap[whatsapp_template_detail]" class="form-control textarea tinymce-template" id="mytextarea_whatsapp">{{ $data->defaultNotificationTemplateMap['whatsapp_template_detail'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 pt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="far fa-save"></i> {{ __('save') }}<i class="md md-lock-open"></i>
                    </button>
                    <button type="button" onclick="window.history.back();" class="btn btn-outline-primary">
                        <i class="fa-solid fa-angles-left"></i> {{ __('close') }}<i class="md md-lock-open"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


                {{--<div class="text-left">
                    <label>{{ (__('messages.template')) }} :</label>
                    <input type="hidden" name="defaultNotificationTemplateMap[language]" value="en" class="form-control" />
                </div>
                <div class="form-group">
                <textarea name="defaultNotificationTemplateMap[template_detail]" class="form-control textarea" id="mytextarea">{{ old('defaultNotificationTemplateMap.template_detail', optional($data->defaultNotificationTemplateMap)->template_detail ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label class="float-left">{{ (__('messages.notification_body')) }} :</label>
                    <input type="text" name="defaultNotificationTemplateMap[notification_message]" value="{{ old('defaultNotificationTemplateMap.notification_message', optional($data->defaultNotificationTemplateMap)->notification_message ?? '') }}" id="en-notification_message" class="form-control notification_message" />

                </div>
                <div class="form-group">
                    <label class="float-left">{{ (__('messages.notification_link')) }} :</label>
                    <input type="text" name="defaultNotificationTemplateMap[notification_link]" value="{{ old('defaultNotificationTemplateMap.notification_link', optional($data->defaultNotificationTemplateMap)->notification_link ?? '') }}" id="en-notification_link" class="form-control notification_link" />

                </div>--}}
 
@push('after-scripts')
<script>
    $(document).ready(function() {
        // Initialize TinyMCE

        (function($) {
            $(document).ready(function() {
                tinymceEditor('.tinymce-templates', ' ', function(ed) {
                }, 450)
            });

        })(jQuery);

        // Initialize Select2
        $('.select2').select2({
            width: '100%'
        });
        $('.select2-tag').select2({
            tags: true,
            dropdownAutoWidth: true,
            width: '100%',
            createTag: function(params) {
                if (params.term.length > 2) {
                    return {
                        id: params.term,
                        text: params.term,
                        newTag: true
                    };
                }
                return null;
            }
        });

        // Handle change event for 'user_type' select
        $('select[name="defaultNotificationTemplateMap[user_type]"]').on('change', function() {
            var userType = $(this).val();
            var type = $('select[name="type"]').val();
            $.ajax({
                url: "{{ route('backend.notificationtemplates.fetchnotification_data') }}",
                method: "GET",
                data: {
                    user_type: userType,
                    type: type
                },
                success: function(response) {
                if (response.success) {
                    var data = response.data;
                    $("input[name='defaultNotificationTemplateMap[subject]']").val(data.subject || '');
                    tinymce.get('mytextarea').setContent(data.template_detail || '');
                    $("input[name='defaultNotificationTemplateMap[notification_message]']").val(data.notification_message || '');
                    $("input[name='defaultNotificationTemplateMap[notification_link]']").val(data.notification_link || '');
                    $("input[name='defaultNotificationTemplateMap[mail_subject]']").val(data.mail_subject || '');
                    tinymce.get('mytextarea_mail').setContent(data.mail_template_detail || '');
                    $("input[name='defaultNotificationTemplateMap[sms_subject]']").val(data.sms_subject || '');
                    tinymce.get('mytextarea_sms').setContent(data.sms_template_detail || '');
                    $("input[name='defaultNotificationTemplateMap[whatsapp_subject]']").val(data.whatsapp_subject || '');
                    tinymce.get('mytextarea_whatsapp').setContent(data.whatsapp_template_detail || '');
                } else {
                    // Clear fields if no data is returned
                    $("input[name='defaultNotificationTemplateMap[subject]']").val('');
                    tinymce.get('mytextarea').setContent('');
                    $("input[name='defaultNotificationTemplateMap[notification_message]']").val('');
                    $("input[name='defaultNotificationTemplateMap[notification_link]']").val('');
                    $("input[name='defaultNotificationTemplateMap[mail_subject]']").val('');
                    tinymce.get('mytextarea_mail').setContent('');
                    $("input[name='defaultNotificationTemplateMap[sms_subject]']").val('');
                    tinymce.get('mytextarea_sms').setContent('');
                    $("input[name='defaultNotificationTemplateMap[whatsapp_subject]']").val('');
                    tinymce.get('mytextarea_whatsapp').setContent('');
                }
            },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        // Update userTypeSelect options based on 'toSelect' changes
        var toSelect = $('#toSelect');
    var userTypeSelect = $('#userTypeSelect');

        function updateUserTypeOptions(selectedValues) {
            userTypeSelect.empty();
            if (selectedValues) {
                selectedValues.forEach(function(value) {
                    userTypeSelect.append(new Option(value, value));
                });
            }
            userTypeSelect.trigger('change');
        }

        var initialSelectedValues = toSelect.val();
        updateUserTypeOptions(initialSelectedValues);

        toSelect.on('change', function() {
            var selectedValues = $(this).val();
            updateUserTypeOptions(selectedValues);
        });
         // If 'userTypeSelect' has a value preselected, trigger its change event to load data
    var preselectedUserType = userTypeSelect.val();
    if (preselectedUserType) {
        $('select[name="defaultNotificationTemplateMap[user_type]"]').trigger('change');
    }
    });
</script>
<style>
    /* Allow Select2 multi-select to expand and wrap selected items */
    .select2-selection--multiple {
        min-height: 80px !important;
        overflow-y: auto !important;
    }
    .select2-selection__rendered {
        white-space: normal !important;
    }
</style>
@endpush
