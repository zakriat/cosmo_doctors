<div class="text-end d-flex gap-3 align-items-center">

@if($data->incident_type != 2 && $data->incident_type != 3)
<a style="cursor:pointer" class="btn btn-icon text-danger p-0 fs-4" data-bs-placement="top" data-bs-toggle="tooltip" title="{{__('messages.lbl_reply') }}"
        onclick="replyPopup({{ $data->id }})">
        <i class="ph ph-chat"></i>
</a>
@endif

<a style="cursor:pointer" class="btn text-secondary p-0 fs-5 view-description"
   data-id="{{ $data->id }}"
   data-title="{{ $data->title }}"
   data-description="{{ $data->description }}"
   title="{{ __('messages.view_full_description') }}">
    <i class="ph ph-eye align-middle"></i>
</a>

</div>