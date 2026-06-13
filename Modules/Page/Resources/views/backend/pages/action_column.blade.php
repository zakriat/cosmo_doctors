<div class="d-flex gap-3 align-items-center">
<button type="button" class="btn bg-tranparent p-0 fs-5" title="{{ __('page.lbl_page_url') }}"  data-bs-toggle="tooltip"  onclick="copyUrl(event, {{$data->id}})"> <a class="text-success"   id="myLink_{{$data->id}}" href='<?= route('pages', ['slug' => $data->slug]) ?>' target='_blank'><i class="ph ph-clipboard"></i></a></button>
    @hasPermission('edit_pages')
        <button type="button" class="btn text-primary p-0 fs-5" data-crud-id="{{$data->id}}" title="{{ __('messages.edit') }} {{ __($module_title) }}" data-bs-toggle="tooltip"> <i class="ph ph-pencil-simple-line"></i></button>
    @endhasPermission
    @hasPermission('delete_pages')
        <a href="{{route("backend.$module_name.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.delete')}} {{ __($module_title) }}" data-confirm="{{ __('messages.are_you_sure?', ['form' => ($data->name) ?? __('Unknown'), 'module' => __('page.title')])  }}"> <i class="ph ph-trash"></i></a>
    @endhasPermission
</div>
