<div class="d-flex gap-2 align-items-center justify-content-end">
  @if(!$data->trashed())
  {{-- @hasPermission('edit_faqs') --}}
      <!-- Edit Button -->
      <a class="btn text-success p-0 fs-5" href="{{ route('backend.faqs.edit', $data->id) }}">
          <i class="ph ph-pencil-simple-line align-middle"></i>
      </a>
  {{-- @endhasPermission --}}

  {{-- @hasPermission('delete_faqs') --}}
      <!-- Soft Delete (Trash) -->
      <a href="{{ route('backend.faqs.destroy', $data->id) }}" id="delete-faq-{{ $data->id }}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}" data-confirm="{{ __('messages.are_you_sure_delete') }}">
          <i class="ph ph-trash align-middle"></i>
      </a>
      
  {{-- @endhasPermission --}}
  @else
  {{-- @hasPermission('restore_faqs') --}}
      <!-- Restore Button -->
    <a class="btn text-secondary p-0 fs-5 restore-tax" data-bs-toggle="tooltip" title="{{__('messages.restore')}}" href="{{ route('backend.faqs.restore', $data->id) }}" data-confirm-message="{{__('messages.are_you_sure_restore')}}" 
        data-success-message="{{__('messages.restore_form')}}">
           <i class="ph ph-arrow-clockwise align-middle"></i>
       </a>
      
  {{-- @endhasPermission --}}

  {{-- @hasPermission('force_delete_faqs') --}}
      <!-- Force Delete Button -->
      <a href="{{ route('backend.faqs.force_delete', $data->id) }}" id="force-delete-faq-{{ $data->id }}" class="btn text-danger p-0 fs-5" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}" data-bs-toggle="tooltip" title="{{ __('messages.permanent_delete') }}" data-confirm="{{ __('messages.are_you_sure_delete') }}">
          <i class="ph ph-trash align-middle"></i>
      </a>
  {{-- @endhasPermission --}}
  @endif
</div>
