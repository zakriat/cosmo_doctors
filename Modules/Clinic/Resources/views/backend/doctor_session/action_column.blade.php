<div class="d-flex gap-3 align-items-center">
@hasPermission('edit_doctors_session')
<button type='button' class="btn text-success p-0 fs-5"  data-crud-id="{{$query->id}}" class='fs-5 text-info border-0 bg-transparent text-nowrap' data-bs-toggle='tooltip' title="{{ __('messages.edit') }}"> <i class="ph ph-pencil-line align-middle"></i>
</button>
@endhasPermission
</div>
