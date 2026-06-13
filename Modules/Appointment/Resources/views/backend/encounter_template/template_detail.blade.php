@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
<div class="row">
  <div class="col-lg-12 m-3 col-md-8">

    <h4 class="card-title mb-3">{{ __('appointment.other_detail') }}</h4>

    <div class="card shadow-sm border-0">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h4 class="card-title mb-0">{{ __('Clinic Details') }}</h4>
      </div>

      <div class="card-body">
        {{-- Clinic Details: Problems / Observations / Notes --}}
        <div class="row g-4">
          {{-- Problems --}}
          <div class="col-xl-4 col-lg-6">
            <div class="border rounded-3 p-3 bg-light-subtle h-100">
              <div class="d-flex align-items-center justify-content-between mb-2"> 
              </div>
              @include('appointment::backend.encounter_template.template_encounter_problem', [
                'data' => $data,
                'problem_list' => $problem_list,
              ])
            </div>
          </div>

          {{-- Observations --}}
          <div class="col-xl-4 col-lg-6">
            <div class="border rounded-3 p-3 bg-light-subtle h-100">
              <div class="d-flex align-items-center justify-content-between mb-2">
              </div>
              @include('appointment::backend.encounter_template.template_encounter_observation', [
                'data' => $data,
                'observation_list' => $observation_list,
              ])
            </div>
          </div>

          {{-- Notes --}}
          <div class="col-xl-4">
            <div class="border rounded-3 p-3 bg-light-subtle h-100">
              <div class="d-flex align-items-center justify-content-between mb-2">
              </div>
              @include('appointment::backend.encounter_template.template_encounter_note', ['data' => $data])
            </div>
          </div>
        </div>
      </div>

      {{-- Medical Report / Prescription --}}
      <div class="card-body border-top">
        <div class="card bg-body border-0 shadow-xs">
          <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">{{ __('appointment.prescription') }}</h5>
            <button class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 mb-2 mb-md-0"
                    data-bs-toggle="modal" data-bs-target="#addprescriptiontemplae"
                    style="margin-bottom: 0.5rem;">
              <i class="ph ph-plus"></i> {{ __('appointment.add_prescription') }}
            </button>
          </div>

          <div class="card-body pt-3">
            @if ($data['prescriptions'])
              <div id="prescription_table">
                @include('appointment::backend.encounter_template.template_prescription_table', ['data' => $data])
              </div>
            @else
              {{-- Friendly empty state if include renders nothing --}}
              <div class="text-center py-5 border rounded-3">
                <div class="fw-semibold text-muted mb-1">{{ __('No prescription found') }}</div>
                <div class="small text-muted mb-3">{{ __('Click “Add Prescription” to create one') }}</div>
                <button class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addprescriptiontemplae">
                  <i class="ph ph-plus"></i> {{ __('appointment.add_prescription') }}
                </button>
              </div>
            @endif
          </div>
        </div>

        <input type="hidden" name="template_id" id="template_id" value="{{ $data['id'] }}">

        {{-- Other Details --}}
        <div class="card bg-body border-0 shadow-xs mt-4">
          <div class="card-header bg-transparent">
            <h6 class="card-title mb-0">{{ __('appointment.other_information') }}</h6>
          </div>
          <div class="card-body">
            <textarea
              class="form-control"
              rows="3"
              placeholder="{{ __('appointment.enter_other_details') }}"
              name="other_details"
              id="other_details"
              style="min-height: 120px">{{ old('other_details', $data['other_details'] ?? '') }}</textarea>
            <div class="form-text">{{ __('Keep it concise. You can edit later.') }}</div>
          </div>
        </div>
      </div>

      {{-- Sticky footer actions --}}
      <div class="card-footer bg-white position-sticky bottom-0 z-1 border-top">
        <div class="d-grid d-sm-flex justify-content-sm-end gap-2">
          <a href="{{ route('backend.encounter-template.index') }}" class="btn btn-light">
            {{ __('messages.cancel') }}
          </a>
          <button class="btn btn-secondary" type="button" id="saveOtherDetailsBtn">
            {{ __('messages.save') }}
          </button>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Modals --}}
@include('appointment::backend.encounter_template.template_prescription', ['data' => $data])
{{-- @include('appointment::backend.patient_encounter.component.billing_details', ['data' => $data]) --}}
@endsection

@push('after-styles')
<style>
  /* Soft shadows for subtle elevation */
  .shadow-xs { box-shadow: 0 2px 12px rgba(0,0,0,.04); }

  /* Consistent Select2 look + full width */
  .select2-container { width: 100% !important; }
  .select2-selection--single, .select2-selection--multiple {
    min-height: 38px; border-radius: .5rem; border-color: #dee2e6;
  }
  .select2-selection__rendered { line-height: 36px !important; }
  .select2-selection__arrow { height: 36px !important; }

  /* Tighter table header */
  .table thead th { background: #4e5ab7; color: #fff; border: 0; }
  .table > :not(caption) > * > * { vertical-align: middle; }

  /* Card header spacing */
  .card-header { border-bottom: 0; padding-bottom: .25rem; }
  .card-body { padding-top: .75rem; }
</style>
@endpush

@push('after-scripts')
<script>
  $(function () {
    // Initialize Select2 on page load
    $('select').each(function () {
      const $el = $(this);
      $el.select2({
        placeholder: $el.data('placeholder') || '{{ __("Select") }}',
        tags: true,
        width: '100%',
        createTag: function (params) {
          // Prevent empty tag
          const term = $.trim(params.term);
          if (term === '') { return null; }
          return { id: term, text: term };
        }
      });
    });

    // Save "Other Details"
    $('#saveOtherDetailsBtn').on('click', function () {
      const otherDetails = $('#other_details').val();
      const templateId   = $('#template_id').val();

      $.ajax({
        url: "{{ route('backend.encounter-template.save-other-details') }}",
        type: "POST",
        data: {
          _token: "{{ csrf_token() }}",
          other_details: otherDetails,
          template_id: templateId
        },
        beforeSend: () => $(this).prop('disabled', true).addClass('disabled'),
        success: function (response) {
          if (response?.status) {
            window.successSnackbar?.('{{ __("EncounterTemplate saved successfully") }}');
          } else {
            window.errorSnackbar?.('{{ __("Something went wrong! Please check.") }}');
          }
        },
        error: function () {
          window.errorSnackbar?.('{{ __("Something went wrong! Please check.") }}');
        },
        complete: () => $('#saveOtherDetailsBtn').prop('disabled', false).removeClass('disabled')
      });
    });
  });
</script>
@endpush
