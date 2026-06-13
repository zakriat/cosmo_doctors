<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header border-bottom">
    @if(isset($title))
      {{ $title }}
    @endif
    <button type="button" data-bs-dismiss="offcanvas" aria-label="Close" class="btn-close-offcanvas"><i class="ph ph-x-circle"></i></button>
  </div>
  <div class="offcanvas-body">
    {{ $slot }}
  </div>
  <div class="offcanvas-body">
    @if(isset($footer))
      {{$footer}}
    @endif
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var resetButton = document.getElementById('reset-filter');
    resetButton.addEventListener('click', function(event) {
        var offcanvas = bootstrap.Offcanvas.getInstance('#offcanvasExample');
        offcanvas.hide();
    });
});
const offcanvasElem = document.querySelector('#offcanvasExample')
if (offcanvasElem) {
    offcanvasElem.addEventListener('shown.bs.offcanvas', function() {
        // Destroy any existing Select2 instances first
        $('#offcanvasExample .select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
        
        // Re-initialize Select2 for dropdowns INSIDE the offcanvas
        setTimeout(function() {
            $('#offcanvasExample .select2').each(function() {
                var $select = $(this);
                var ajaxUrl = $select.data('ajax--url');
                
                // Get the field label for placeholder
                var fieldLabel = $select.closest('.form-group').find('label').text().trim();
                if (!fieldLabel) {
                    fieldLabel = $select.attr('name') || 'Select an option';
                }
                
                var select2Options = {
                    dropdownParent: $('#offcanvasExample'),
                    minimumResultsForSearch: 0,
                    width: '100%',
                    allowClear: false,
                    placeholder: fieldLabel
                };
                
                // If it has AJAX URL, add AJAX configuration
                if (ajaxUrl) {
                    select2Options.ajax = {
                        url: ajaxUrl,
                        dataType: 'json',
                        delay: 250,
                        cache: $select.data('ajax--cache') === true,
                        data: function (params) {
                            return {
                                q: params.term || '', // search term
                                page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            console.log('AJAX Response:', data); // Debug log
                            return {
                                results: data.results || data || [],
                                pagination: {
                                    more: data.pagination ? data.pagination.more : false
                                }
                            };
                        },
                        error: function(xhr, status, error) {
                            console.error('Select2 AJAX Error:', error);
                        }
                    };
                }
                
                $select.select2(select2Options);
            });
        }, 100); // Small delay to ensure DOM is ready
    });
}
</script>