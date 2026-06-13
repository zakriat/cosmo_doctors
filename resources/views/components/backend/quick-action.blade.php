<form action="{{$url ?? ''}}" id="quick-action-form" class="form-disabled d-flex gap-3 align-items-stretch">
  @csrf
  {{$slot}}
  <input type="hidden" name="message_change-featured" value="{{ __('messages.are_you_sure_you_want_to_perform_this_action') }}">
  <input type="hidden" name="message_change-status" value="{{ __('messages.are_you_sure_you_want_to_perform_this_action') }}">
  <input type="hidden" name="message_delete" value="{{ __('messages.are_you_sure_you_want_to_delete_it')}}">
  <button class="btn btn-gray" id="quick-action-apply">{{ __('messages.apply') }}</button>
</form>
