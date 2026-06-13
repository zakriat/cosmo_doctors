@if(!in_array($data->status, ['completed', 'check_in', 'checkout', 'cancelled']))
    <select name="branch_for" 
            class="form-select form-select-sm change-select select2"
            data-placeholder="{{ __('messages.select_status') }}"
            data-token="{{ csrf_token() }}"
            data-id="{{ $data->id }}"
            data-charge="{{ $data->getCancellationCharges() ?? 0 }}"
            style="width: 100%;">
        <option value=""></option>
        @foreach ($status as $value)
            <option value="{{ $value->name }}" {{ $data->status == $value->name ? 'selected' : '' }}>
                {{ __('appointment.' . $value->name) }}
            </option>
        @endforeach
    </select>
@elseif($data->status == 'cancelled')
    <span class="badge bg-danger-subtle text-capitalize p-2">
        {{ __('appointment.cancelled') }}
    </span>
@elseif($data->status == 'check_in')
    <span class="badge bg-info-subtle text-capitalize p-2">
        {{ __('appointment.check_in') }}
    </span>
@else
    <span class="badge bg-success-subtle text-capitalize p-2">
        {{ __('appointment.checkout') }}
    </span>
@endif

<!-- Cancel Modal -->
<div id="cancelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.2); z-index: 1055; align-items: center; justify-content: center;">

  <div style="background: #fff; width: 500px; max-width: 90%; height: auto; padding: 30px 25px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); position: relative; text-align: center; animation: fadeIn 0.3s ease;">

    <!-- Circle X Icon (centered at the top) -->
    <div style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; border: 2px solid #6c8cff;">
      <span style="font-size: 28px; color: #6c8cff; font-weight: 300;">✕</span>
    </div>

    <h5 style="font-weight: 500; font-size: 20px; color: #333; margin-bottom: 10px;">{{ __('messages.cancel_appointment') }}</h5>

    <p style="color: #777; font-size: 14px; margin-bottom: 20px; line-height: 1.4;">
      {{ __('messages.cancel_this_appointment') }}
      <br>
      {{ __('messages.cancellation_charge') }}
    </p>

    <p id="cancel_charge_info" class="text-danger fw-semibold text-center mb-3" style="font-size: 14px;"></p>

    <!-- Input Reason -->
    <div style="text-align: left; margin-bottom: 20px;">
      <label for="cancel_reason" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 5px; color: #333;">
        {{ __('messages.lbl_reason') }}<span style="color: red;">*</span>
      </label>
      <input type="text" id="cancel_reason" class="form-control" placeholder=' {{ __('messages.lbl_emergency') }}' style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;" />
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; gap: 12px; margin-top: 5px;">
      <button onclick="cancelAbort()" style="flex: 1; padding: 10px; border-radius: 6px; background: white; border: 1px solid #ddd; cursor: pointer; font-weight: 500; color: #555;">
        {{ __('messages.cancel') }}
      </button>
      <button id="confirm_btn" onclick="submitCancellation()" style="flex: 1; padding: 10px; border-radius: 6px; background: #f87f7f; border: none; cursor: pointer; font-weight: 500; color: white;">
        {{ __('messages.lbl_confirm') }}
      </button>
    </div>

  </div>
</div>






