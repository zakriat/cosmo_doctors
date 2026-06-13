@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush

@section('content')
<div class="table-content mb-5">
      <x-backend.section-header>
        <div>
        @if(auth()->user()->can('edit_app_banner') || auth()->user()->can('delete_app_banner'))
          <x-backend.quick-action url="{{route('backend.app_banners.bulk_action')}}">
            <div class="">
              <select name="action_type" class="select2 form-select col-12" id="quick-action-type" style="width:100%">
                  <option value="">{{ __('messages.no_action') }}</option>
                  @can('edit_app_banner')
                  <option value="change-status">{{ __('messages.status') }}</option>
                  @endcan
                  @can('delete_app_banner')
                  <option value="delete">{{ __('messages.delete') }}</option>
                  @endcan
              </select>
            </div>
            <div class="select-status d-none quick-action-field" id="change-status-action">
                <select name="status" class="select2 form-select" id="status" style="width:100%">
                  <option value="1">{{ __('messages.active') }}</option>
                  <option value="0">{{ __('messages.inactive') }}</option>
                </select>
            </div>
          </x-backend.quick-action>
          @endif
        </div>

        <x-slot name="toolbar">
          <div class="input-group flex-nowrap">
            <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search" aria-describedby="addon-wrapping">
          </div>
          @hasPermission('add_app_banner')
            <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">{{ __('messages.new') }}</x-buttons.offcanvas>
          @endhasPermission
        </x-slot>
        </x-backend.section-header>
        <table id="datatable" class="table table-responsive">
        </table>
</div>
      <div data-render="app">
        <!-- <slider-form-offcanvas
             create-title="{{ __('messages.create') }} {{ __('messages.new') }} {{ __($module_title) }}"
             edit-title="{{ __('messages.edit') }} {{ __($module_title) }}">
        </slider-form-offcanvas> -->
      @include('slider::backend.sliders.sliderForm_offcanvas')

       </div>

@endsection

@push ('after-scripts')
<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>


<script src="{{ mix('modules/slider/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>

<script type="text/javascript">

// Intercept global offcanvas handler to avoid null instance errors (attach once, capture phase)
if (!window.__sliderOffcanvasIntercept) {
    document.addEventListener('click', function(e) {
        const targetEl = e.target && e.target.closest ? e.target.closest('[data-crud-id], [data-bs-target]') : null;
        if (!targetEl) return;
        const target = targetEl.getAttribute('data-bs-target') || targetEl.getAttribute('data-target');
        const crudId = targetEl.getAttribute('data-crud-id');
        if (target === '#form-offcanvas' || crudId === '0') {
            e.preventDefault();
            e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
            // Defer to ensure our functions exist
            setTimeout(function() {
                if (typeof openOffcanvasOptimized === 'function') {
                    if (crudId === '0' || !crudId) openOffcanvasOptimized();
                    else openOffcanvasOptimized(crudId);
                }
            }, 0);
        }
    }, true);
    window.__sliderOffcanvasIntercept = true;
}

        const columns = [
        {
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                width: '5%',
                exportable: false,
                orderable: false,
                searchable: false,
        },
        {
            data: 'image',
            name: 'image',
            title: "{{ __('slider.lbl_file_url') }}",
            width: '10%',
            orderable: false,
            searchable: false,
            exportable: false
        },
        { data: 'name', name: 'name', title: "{{ __('slider.lbl_name') }}",  width:'15%' },
        { data: 'link', name: 'link', title: "{{ __('slider.lbl_link') }}",  width:'15%' },
        { data: 'type', name: 'type', title: "{{ __('slider.lbl_type') }}",  width:'15%'},
        { data: 'link_id', name: 'link_id', title: "{{ __('slider.lbl_link_id') }}",searchable: true,  width:'15%'},
        { data: 'status', name: 'status', title: "{{ __('slider.lbl_status') }}", width:'15%'},
        {
            data: 'updated_at',
            name: 'updated_at',
            title: "{{ __('tax.lbl_updated') }}",
            width: '5%',
            visible: false,
        },

      ]

      const actionColumn = [
            { data: 'action', name: 'action', orderable: false, searchable: false, title: "{{ __('slider.lbl_action') }}"}
        ]

        let customFieldColumns = []
        try {
            const raw = String.raw`@json($columns)`
            customFieldColumns = typeof raw === 'string' ? JSON.parse(raw) : []
        } catch (_) { customFieldColumns = [] }

        function toArraySafe(val) {
            if (Array.isArray(val)) return val
            if (val == null) return []
            if (typeof val === 'string') {
                try { const parsed = JSON.parse(val); return Array.isArray(parsed) ? parsed : [] } catch { return [] }
            }
            return []
        }

        const finalColumns = [
            ...toArraySafe(columns),
            ...toArraySafe(customFieldColumns),
            ...toArraySafe(actionColumn)
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            // Initialize Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2({
                    width: '100%'
                });
            }

            window.datatable = initDatatable({
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                orderColumn: [[ 6, "desc" ]],
            })
            
            // Override the index.js behavior for our specific offcanvas
            const formOffcanvas = document.getElementById('form-offcanvas');
            if (formOffcanvas) {
                // Remove any existing event listeners from index.js
                const existingInstance = bootstrap.Offcanvas.getInstance(formOffcanvas);
                if (existingInstance) {
                    existingInstance.dispose();
                }
                
                // Prevent index.js from handling our offcanvas
                formOffcanvas.setAttribute('data-custom-handled', 'true');
            }
            
            // Prevent index.js from handling clicks on elements that target our offcanvas
            $(document).on('click', '[data-crud-id]', function(e) {
                // Check if this element targets our specific offcanvas or is a new button
                const target = $(this).data('bs-target') || $(this).attr('data-bs-target');
                const crudId = $(this).attr('data-crud-id');
                
                // Handle if it targets our offcanvas OR if it's a new button (crud-id="0")
                if (target === '#form-offcanvas' || crudId === '0') {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Handle the click ourselves with optimized loading
                    if (crudId === '0') {
                        // This is a "new" button - load create form
                        openOffcanvasOptimized();
                    } else {
                        // This is an edit button - load edit form
                        openOffcanvasOptimized(crudId);
                    }
                }
            });
            
            // Add global event listener for offcanvas disposal
            document.addEventListener('hidden.bs.offcanvas', function (event) {
                if (event.target.id === 'form-offcanvas') {
                    // Only dispose if it's not being handled by our custom handlers
                    setTimeout(() => {
                        const offcanvas = bootstrap.Offcanvas.getInstance(event.target);
                        if (offcanvas && !event.target.hasAttribute('data-custom-handled')) {
                            offcanvas.dispose();
                        }
                    }, 100);
                }
            });
        })

        // Global function to refresh datatable
        window.refreshDatatable = function() {
            if (window.datatable) {
                window.datatable.ajax.reload(null, false); // false = stay on current page
            } else {
                // Fallback: try to find datatable by ID
                if (typeof $ !== 'undefined' && $('#datatable').length) {
                    const table = $('#datatable').DataTable();
                    if (table) {
                        table.ajax.reload(null, false);
                    }
                }
            }
        }

  // Ensure slider form inside offcanvas submits via AJAX in both create and edit modes
  function bindSliderFormAjax() {
      const form = document.getElementById('sliderForm');
      if (!form) { return; }
      // avoid duplicate
      $(form).off('submit.slider click.slider');

      // Unified AJAX submit handler
      const handleSliderFormSubmission = function() {
          // Prevent multiple submissions
          if (form.dataset.submitting === 'true') {
              return;
          }
          form.dataset.submitting = 'true';
          
          const $form = $(form);
          const submitBtn = $form.find('button[type="submit"]').get(0);

          // Get file input for manual handling
          const fileInput = form.querySelector('[name="file_url"]');

          // Clear previous validation
          $form.find('.is-invalid').removeClass('is-invalid');
          $form.find('.validation-error').text('').hide();

          // Client-side required field checks
          const typeInput = form.querySelector('[name="type"]');
          if (typeInput && (!typeInput.value || typeInput.value === '')) {
              typeInput.classList.add('is-invalid');
              const errorSpan = typeInput.parentElement ? typeInput.parentElement.querySelector('.validation-error') : null;
              if (errorSpan) {
                  errorSpan.textContent = '{{ __("slider.lbl_type") }} {{ __("messages.required") ?? "is required." }}';
                  errorSpan.style.display = 'block';
              }
              return;
          }

          // Prepare form data
          const fd = new FormData(form);
          
          
          // Manual form data collection as backup
          const formData = {
              name: form.querySelector('[name="name"]')?.value || '',
              link: form.querySelector('[name="link"]')?.value || '',
              type: form.querySelector('[name="type"]')?.value || '',
              link_id: form.querySelector('[name="link_id"]')?.value || '',
              status: form.querySelector('[name="status"]')?.checked ? '1' : '0'
          };
          
          
          // Add manual form data to FormData
          Object.keys(formData).forEach(key => {
              if (formData[key] !== '') {
                  fd.append(key, formData[key]);
              }
          });
          
          // Manual file handling - check if file input has a file
          if (fileInput && fileInput.files && fileInput.files.length > 0) {
              fd.append('file_url', fileInput.files[0]);
          }
          
          // Ensure token exists
          if (!fd.has('_token')) { fd.append('_token', '{{ csrf_token() }}'); }

          // Determine create vs edit
          let idInput = form.querySelector('[name="id"]');
          if (!idInput) {
              // Backstop: try to infer from action URL placeholder filled earlier
              const injectedId = (form.getAttribute('data-edit-id') || '').trim();
              if (injectedId) {
                  idInput = document.createElement('input');
                  idInput.type = 'hidden';
                  idInput.name = 'id';
                  idInput.value = injectedId;
                  form.appendChild(idInput);
              }
          }
          let actionUrl = "{{ route('backend.app-banners.store') }}";
          if (idInput && idInput.value) {
              fd.set('_method', 'PUT');
              actionUrl = "{{ route('backend.app-banners.update', ':id') }}".replace(':id', idInput.value);
          }

          // Spinner
          let originalText = '';
          if (submitBtn) {
              originalText = submitBtn.textContent;
              submitBtn.disabled = true;
              submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{ __("messages.saving") }}';
          }

          return fetch(actionUrl, {
              method: 'POST',
              body: fd,
              headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'Accept': 'application/json'
              }
          })
          .then(resp => {
              if (resp.status === 422) return resp.json().then(j => ({ status: false, errors: j.errors || {} }));
              if (!resp.ok) throw new Error('Network response was not ok');
              return resp.json();
          })
          .then(data => {
              if (data && (data.status === true || data.status === 'success')) {
                  // Close offcanvas
                  const el = document.getElementById('form-offcanvas');
                  if (el) {
                      const offc = bootstrap.Offcanvas.getInstance(el) || bootstrap.Offcanvas.getOrCreateInstance(el);
                      offc.hide();
                  }
                  if (typeof window.refreshDatatable === 'function') {
                      window.refreshDatatable();
                  }
                  if (typeof window.successSnackbar === 'function') {
                      window.successSnackbar(data.message || '{{ __("messages.saved_successfully") }}');
                  }
              } else if (data && data.errors) {
                  Object.keys(data.errors).forEach(field => {
                      const input = form.querySelector(`[name="${field}"]`);
                      const errorSpan = input ? input.parentElement.querySelector('.validation-error') : null;
                      if (input) input.classList.add('is-invalid');
                      if (errorSpan) { errorSpan.textContent = data.errors[field][0]; errorSpan.style.display = 'block'; }
                  });
                  if (typeof window.errorSnackbar === 'function') {
                      window.errorSnackbar('{{ __("messages.validation_error") ?? "Please fix the validation errors." }}');
                  }
              }
          })
          .catch(err => {
              if (typeof window.errorSnackbar === 'function') {
                  window.errorSnackbar('{{ __("messages.error_occurred") }}');
              }
          })
          .finally(() => {
              if (submitBtn) {
                  submitBtn.disabled = false;
                  submitBtn.textContent = originalText || submitBtn.textContent;
              }
              // Reset submitting flag
              form.dataset.submitting = 'false';
          });
      };

      // Submit intercept
      $(form).on('submit.slider', function(e) {
          e.preventDefault(); e.stopPropagation();
          handleSliderFormSubmission();
          return false;
      });

      // Click submit button safety net
      $(form).find('button[type="submit"]').on('click.slider', function(e) {
          e.preventDefault(); e.stopPropagation();
          handleSliderFormSubmission();
          return false;
      });
  }


      function resetQuickAction () {
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

    $('#quick-action-type').change(function () {
      resetQuickAction()
    });

    // Function to properly dispose of offcanvas instance
    function disposeOffcanvas() {
        const offcanvasElement = document.getElementById('form-offcanvas');
        if (offcanvasElement) {
            const existingOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
            if (existingOffcanvas) {
                existingOffcanvas.dispose();
            }
        }
    }

    // Optimized function to open offcanvas with lazy loading
    function openOffcanvasOptimized(sliderId = null) {
        const offcanvasElement = document.getElementById('form-offcanvas');
        
        if (offcanvasElement) {
            // Mark as custom handled and reuse single instance
            offcanvasElement.setAttribute('data-custom-handled', 'true');
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);

            // Defer content loading until fully shown to avoid swapping during transition
            const onShown = function() {
                offcanvasElement.removeEventListener('shown.bs.offcanvas', onShown);
                if (typeof loadFormContent === 'function') {
                    loadFormContent(sliderId);
                } else {
                    const url = sliderId ? 
                        "{{ route('backend.app-banners.edit', ':id') }}".replace(':id', sliderId) : 
                        "{{ route('backend.app-banners.create') }}";
                    $.get(url, function (response) {
                        // Replace only inner content, keep container and instance
                        const $wrapper = $('#form-offcanvas');
                        if ($wrapper.length) {
                            const $resp = $(response);
                            const headerHtml = $resp.find('#form-offcanvas .offcanvas-header').html() || $resp.find('.offcanvas-header').html();
                            const bodyHtml = $resp.find('#form-offcanvas .offcanvas-body').html() || $resp.find('.offcanvas-body').html() || response;
                            if (headerHtml) { $wrapper.find('.offcanvas-header').html(headerHtml); }
                            $wrapper.find('.offcanvas-body').html(bodyHtml);
                            
                            // Re-initialize Select2 for the newly loaded form
                            if (typeof $.fn.select2 !== 'undefined') {
                                $('#form-offcanvas .select2').select2({
                                    width: '100%',
                                    dropdownParent: $('#form-offcanvas')
                                });
                            }
                            
                            // Ensure hidden id reflects edit vs create
                            const $idInput = $('#sliderForm').find('input[name="id"]');
                            if (sliderId) {
                                if ($idInput.length) { $idInput.val(sliderId); }
                                else { $('<input type="hidden" name="id" />').val(sliderId).appendTo('#sliderForm'); }
                            } else {
                                if ($idInput.length) { $idInput.val(''); }
                            }
                            bindSliderFormAjax();
                        }
                    }).fail(function(xhr, status, error) {
                        console.error('Error loading form:', error);
                    });
                }
            };
            offcanvasElement.addEventListener('shown.bs.offcanvas', onShown);
            offcanvas.show();

            // When hidden, do not dispose or swap DOM; just clean flags
            if (!offcanvasElement.__sliderHiddenBound) {
                offcanvasElement.addEventListener('hidden.bs.offcanvas', function () {
                    offcanvasElement.removeAttribute('data-custom-handled');
                });
                offcanvasElement.__sliderHiddenBound = true;
            }
        }
    }

    // Legacy function for backward compatibility
    function openOffcanvas(response) {
        // Dispose any existing instance first
        disposeOffcanvas();
        
        // Replace the offcanvas form content
        const parentElement = $('#form-offcanvas').parent();
        parentElement.html(response);
        
        // Re-initialize Select2 for the newly loaded form
        if (typeof $.fn.select2 !== 'undefined') {
            $('#form-offcanvas .select2').select2({
                width: '100%',
                dropdownParent: $('#form-offcanvas')
            });
        }
        
        // Bind AJAX submission for the newly injected form
        bindSliderFormAjax();
        
        // Create new instance and show
        const offcanvasElement = document.getElementById('form-offcanvas');
        
        if (offcanvasElement) {
            // Mark as custom handled to avoid conflicts
            offcanvasElement.setAttribute('data-custom-handled', 'true');
            
            // Use getOrCreateInstance to avoid conflicts with index.js
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);
            
            // Add event listener for when offcanvas is hidden
            offcanvasElement.addEventListener('hidden.bs.offcanvas', function () {
                disposeOffcanvas();
                offcanvasElement.removeAttribute('data-custom-handled');
            });
            
            // Show the offcanvas
            offcanvas.show();
        }
    }

    // Handle custom edit-slider clicks (for buttons without data-crud-id)
    $(document).on('click', '.edit-slider', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Prevent multiple rapid clicks
        if ($(this).hasClass('loading')) {
            return;
        }
        
        $(this).addClass('loading');
        let url = $(this).data('url');
        
        // Prevent index.js from interfering by temporarily disabling its handlers
        const offcanvasElement = document.getElementById('form-offcanvas');
        if (offcanvasElement) {
            offcanvasElement.setAttribute('data-custom-handled', 'true');
        }
        
        $.get(url, function (response) {
            openOffcanvas(response);
            
            // Re-initialize Select2 after opening offcanvas
            setTimeout(function() {
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#form-offcanvas .select2').select2({
                        width: '100%',
                        dropdownParent: $('#form-offcanvas')
                    });
                }
            }, 100);
            
            // ensure form is bound after injection
            bindSliderFormAjax();
        }).fail(function(xhr, status, error) {
            // Handle error silently or show user notification
        }).always(function() {
            // Remove loading class
            $('.edit-slider').removeClass('loading');
        });
    });

    // Fallback handler for any button that targets our offcanvas but wasn't caught above
    $(document).on('click', 'button[data-crud-id="0"]', function(e) {
        // Only handle if not already handled by the main handler
        if (!e.isDefaultPrevented()) {
            e.preventDefault();
            e.stopPropagation();
            
            const offcanvasElement = document.getElementById('form-offcanvas');
            if (offcanvasElement) {
                offcanvasElement.setAttribute('data-custom-handled', 'true');
            }
            
            $.get("{{ route('backend.app-banners.create') }}", function (response) {
                openOffcanvas(response);
                
                // Re-initialize Select2 after opening offcanvas
                setTimeout(function() {
                    if (typeof $.fn.select2 !== 'undefined') {
                        $('#form-offcanvas .select2').select2({
                            width: '100%',
                            dropdownParent: $('#form-offcanvas')
                        });
                    }
                }, 100);
            }).fail(function(xhr, status, error) {
                // Handle error silently or show user notification
            });
        }
    });

    // Notification function for toast messages
    function showNotification(type, message) {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '1080';
            document.body.appendChild(toastContainer);
        }

        // Create toast element
        const toastId = 'toast-' + Date.now();
        const toastClass = type === 'success' ? 'text-bg-success' : 'text-bg-danger';
        const iconClass = type === 'success' ? 'ph-check-circle' : 'ph-x-circle';
        
        const toastHtml = `
            <div id="${toastId}" class="toast ${toastClass}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="ph ${iconClass} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        // Show the toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 3000
        });
        toast.show();
        
        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }

    // Handle delete operations with toast notification
// ✅ Handle delete operations using only SweetAlert2 (no alert, no toast)
$(document).on('click', 'a[data-type="ajax"][data-method="DELETE"]', function(e) {
    e.preventDefault();

    const deleteLink = $(this);
    const url = deleteLink.attr('href');
    const token = deleteLink.data('token');
    const confirmMessage = deleteLink.data('confirm') || "Are you sure you want to delete this item?";

    // 🔸 SweetAlert2 confirmation popup
    Swal.fire({
        title: 'Confirm Delete',
        text: confirmMessage,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: window.localMessagesUpdate?.messages?.yes || 'Yes, delete it!',
        cancelButtonText: window.localMessagesUpdate?.messages?.cancel || 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // 🔸 Perform AJAX delete
            $.ajax({
                url: url,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': token },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message || 'The record has been deleted successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Reload DataTable after a short delay
                        setTimeout(() => {
                            if (typeof window.refreshDatatable === 'function') {
                                window.refreshDatatable();
                            }
                        }, 1000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to delete the record.',
                        });
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    const message = response && response.message 
                        ? response.message 
                        : 'An error occurred while deleting.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                    });
                }
            });
        }
    });
});


  </script>
@endpush
