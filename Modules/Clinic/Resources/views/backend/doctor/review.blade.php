@extends('backend.layouts.app', ['isNoUISlider' => true])

@section('title')
{{ $module_title }}
@endsection



@push('after-styles')
<link rel="stylesheet" href="{{ mix('modules/service/style.css') }}">
@endpush

@section('content')
<div class="table-content mb-5">
    <x-backend.section-header>
      <div>
        <x-backend.quick-action url="{{ route('backend.doctor.bulk_action_review') }}">
          <div class="">
            <select name="action_type" class="form-select col-12" id="quick-action-type">
              <option value="">{{ __('messages.no_action') }}</option>
              <option value="delete">{{ __('messages.delete') }}</option>
            </select>
          </div>
        </x-backend.quick-action>
      </div>
      <x-slot name="toolbar"> 
          <div class="input-group flex-nowrap">
              <span class="input-group-text" id="addon-wrapping"><i
                      class="fa-solid fa-magnifying-glass"></i></span>
              <input type="text" class="form-control dt-search" placeholder= {{ (__('appointment.search')) }} aria-label="Search"
                  aria-describedby="addon-wrapping">
          </div>
      </x-slot>
    </x-backend.section-header>
    <table id="datatable" class="table table-responsive">
    </table>
</div>
@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>
  const columns = [
    @unless(auth()->user()->hasRole('doctor'))
    {
      name: 'check',
      data: 'check',
      title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
      width: '0%',
      exportable: false,
      orderable: false,
      searchable: false,
    }, 
    @endunless
     
    {
      data: 'user_id',
      name: 'user_id',
      title: "{{ __('sidebar.patient') }}",
      orderable: true,
      searchable: true,
    },
    {
      data: 'doctor_id',
      name: 'doctor_id',
      title: "{{ __('appointment.lbl_doctor') }}",
      orderable: true,
      searchable: true,
      // width: '10%'
    },
    {
      data: 'service_id',
      name: 'service_id',
      title: "{{ __('appointment.lbl_service') }}",
      orderable: true,
      searchable: true,
      // width: '10%'
    },
    {
      data: 'review_msg',
      name: 'review_msg',
      title: "{{ __('clinic.lbl_message') }}",
      width: '10%',
      className: 'description-column',
      render: function(data, type, row) {
        if (type !== 'display') return data;
        const plain = stripHtml(String(data ?? ''));
        const safe = escapeHtml(plain);
        const id = `rv_${row.id ?? Math.random().toString(36).slice(2)}`;
        return `
          <div class="review-wrapper" data-review-id="${id}">
            <div class="review-clamp">${safe}</div>
            <div class="review-actions mt-1">
              <a class="text-primary me-2 js-toggle-review" data-action="toggle">Read more</a>
            </div>
          </div>
        `;
      }

    },
    {
      data: 'rating',
      name: 'rating',
      title: "{{ __('clinic.lbl_rating') }}",
      width: '5%'
    },
    {
      data: 'updated_at',
      name: 'updated_at',
      title: "{{ __('messages.date_time') }}",
      orderable: true,
      visible: true,
    }

  ]

  const actionColumn = [
    @if(auth()->user()->hasRole(['admin','demo_admin']))
    {
    data: 'action',
    name: 'action',
    orderable: false,
    searchable: false,
    title: "{{ __('employee.lbl_action') }}",
    width: '5%'
    }
    @endif
  ]



  let finalColumns = [
    ...columns,
    ...actionColumn
  ]


  document.addEventListener('DOMContentLoaded', (event) => {
    initDatatable({
      url: '{{ route("backend.doctor.review_data", ["doctor_id" => $doctor_id ]) }}',
      finalColumns,
      orderColumn: [ 
        @if(auth()->user()->hasRole('doctor'))
          [4, "desc"]
        @else
          [5, "desc"]
        @endif
      ]
    });

    // escape HTML to prevent injection inside our custom renderer
    window.escapeHtml = function(str) {
      const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };
      return str.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // remove HTML tags if backend sends wrapped/HTML content
    window.stripHtml = function(html) {
      const div = document.createElement('div');
      div.innerHTML = html;
      return div.textContent || div.innerText || '';
    }

    // Delegate click handlers for read more / view
    $(document).on('click', '.js-toggle-review', function(e) {
      e.preventDefault();
      const wrapper = $(this).closest('.review-wrapper');
      const block = wrapper.find('.review-clamp');
      block.toggleClass('expanded');
      $(this).text(block.hasClass('expanded') ? 'Read less' : 'Read more');
    });

    $(document).on('click', '.js-view-review', function(e) {
      e.preventDefault();
      const wrapper = $(this).closest('.review-wrapper');
      const fullHtml = wrapper.find('.review-clamp').html();
      $('#fullReviewModal .modal-body').html(fullHtml);
      const modal = new bootstrap.Modal(document.getElementById('fullReviewModal'));
      modal.show();
    });

    function resetQuickAction() {
      const actionValue = $('#quick-action-type').val();
      if (actionValue != '') {
        $('#quick-action-apply').removeAttr('disabled');

        if (actionValue == 'change-status') {
          $('.quick-action-field').addClass('d-none');
          $('#change-status-action').removeClass('d-none');
        } else {
          $('.quick-action-field').addClass('d-none');
        }
      } else {
        $('#quick-action-apply').attr('disabled', true);
        $('.quick-action-field').addClass('d-none');
      }
    }

    $('#quick-action-type').change(function() {
      resetQuickAction()
    });
  })
</script>
@endpush

@push('after-content')
<!-- Full Review Modal -->
<div class="modal fade" id="fullReviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Full Review</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
  </div>
@endpush
