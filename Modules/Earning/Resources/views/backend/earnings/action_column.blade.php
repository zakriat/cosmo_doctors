<div class="text-end d-flex gap-3 align-items-center">
    @if($data['total_pay'] > 0)
      <span  class="btn text-primary p-0 fs-5"  data-crud-id="{{ $data->id }}" title="{{__('Payout')}}" data-bs-toggle="tooltip"><i class="ph ph-money"></i></span>
    @else
      <span  class="px-2">-</span>
    @endif
</div>



