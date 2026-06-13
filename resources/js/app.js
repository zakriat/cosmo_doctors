

(function () {
  "use strict";
  $(document).on('change', '.datatable-filter [data-filter="select"]', function () {
    window.renderedDataTable.ajax.reload(null, false)
  })

  $(document).on('input', '.dt-search', function () {
    window.renderedDataTable.ajax.reload(null, false)
  })



  const confirmSwal = async (message) => {

    console.log(message);
    return await Swal.fire({
      title: message,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#858482',
      confirmButtonText: window.localMessagesUpdate?.messages?.yes || 'Yes',
      cancelButtonText: window.localMessagesUpdate?.messages?.cancel || 'Cancel',
      showClass: {
        popup: 'animate__animated animate__zoomIn'
      },
      hideClass: {
        popup: 'animate__animated animate__zoomOut'
      }
    }).then((result) => {
      return result
    })
  }

  window.confirmSwal = confirmSwal


  const confirmDeleteSwal = async (message) => {

    // console.log(message.message);
    return await Swal.fire({
      title: message.message,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#858482',
      confirmButtonText: window.localMessagesUpdate?.messages?.yes || 'Yes',
      cancelButtonText: window.localMessagesUpdate?.messages?.cancel || 'Cancel',
      showClass: {
        popup: 'animate__animated animate__zoomIn'
      },
      hideClass: {
        popup: 'animate__animated animate__zoomOut'
      }
    }).then((result) => {
      return result
    })
  }

  window.confirmDeleteSwal = confirmDeleteSwal


  $('#quick-action-form').on('submit', function (e) {
    e.preventDefault()
    const form = $(this)
    const url = form.attr('action')
    const message = $('[name="message_' + $('[name="action_type"]').val() + '"]').val()
    const rowdIds = $("#datatable_wrapper .select-table-row:checked").map(function () {
      return $(this).val();
    }).get();
    confirmSwal(message).then((result) => {
      if (!result.isConfirmed) return
      callActionAjax({ url: `${url}?rowIds=${rowdIds}`, body: form.serialize() })
    })
  })

  // Update status on switch
  $(document).on('change', '#datatable_wrapper .switch-status-featured', function (e) {
    if (!e.originalEvent) return
    let url = $(this).attr('data-url')
    let body = {
      featured: $(this).prop('checked') ? 1 : 0,
      _token: $(this).attr('data-token')
    }
    callActionAjax({ url: url, body: body })
  })

  // Update status on switch
  $(document).on('change', '#datatable_wrapper .switch-status-change', function (e) {
    if (!e.originalEvent) return
    let url = $(this).attr('data-url')
    let body = {
      status: $(this).prop('checked') ? 1 : 0,
      _token: $(this).attr('data-token')
    }
    callActionAjax({ url: url, body: body })
  })


  $(document).on('change', '#datatable_wrapper .change-select', function (e) {
    if (!e.originalEvent) return
    let url = $(this).attr('data-url')
    let body = {
      value: $(this).val(),
      _token: $(this).attr('data-token')
    }
    callActionAjax({ url: url, body: body })
  })

  function callActionAjax({ url, body }) {
    $.ajax({
      type: 'POST',
      url: url,
      data: body,
      success: function (res) {
        if (res.status) {
          window.successSnackbar(res.message)
          window.renderedDataTable.ajax.reload(resetActionButtons, false)
          const event = new CustomEvent('update_quick_action', { detail: { value: true } })
          document.dispatchEvent(event)
        } else {
          Swal.fire({
            title: 'Error',
            text: res.message,
            icon: "error",
            showClass: {
              popup: 'animate__animated animate__zoomIn'
            },
            hideClass: {
              popup: 'animate__animated animate__zoomOut'
            }
          })
          // window.errorSnackbar(res.message)
        }
      }
    })
  }

  // Update status on button click
  $(document).on('click', '#datatable_wrapper .button-status-change', function () {

    let url = $(this).attr('data-url')
    let body = {
      status: 1,
      _token: $(this).attr('data-token')
    }
    callActionAjax({ url: url, body: body })
  })

  function callActionAjax({ url, body }) {
    $.ajax({
      type: 'POST',
      url: url,
      data: body,
      success: function (res) {
        if (res.status) {
          window.successSnackbar(res.message)
          window.renderedDataTable.ajax.reload(resetActionButtons, false)
          const event = new CustomEvent('update_quick_action', { detail: { value: true } })
          document.dispatchEvent(event)
        } else {
          window.errorSnackbar(res.message)
        }
      }
    })
  }

  //select row in datatable
  const dataTableRowCheck = (id) => {
    checkRow();
    if ($(".select-table-row:checked").length > 0) {
      $("#quick-action-form").removeClass('form-disabled');
      //if at-least one row is selected
      document.getElementById("select-all-table").indeterminate = true;
      $("#quick-actions").find("input, textarea, button, select").removeAttr("disabled");
    } else {
      //if no row is selected
      document.getElementById("select-all-table").indeterminate = false;
      $("#select-all-table").attr("checked", false);
      resetActionButtons();
    }

    if ($("#datatable-row-" + id).is(":checked")) {
      $("#row-" + id).addClass("table-active");
    } else {
      $("#row-" + id).removeClass("table-active");
    }

  };
  window.dataTableRowCheck = dataTableRowCheck

  const selectAllTable = (source) => {
    const checkboxes = document.getElementsByName("datatable_ids[]");
    for (var i = 0, n = checkboxes.length; i < n; i++) {
      // if disabled property is given to checkbox, it won't select particular checkbox.
      if (!$("#" + checkboxes[i].id).prop('disabled')) {
        checkboxes[i].checked = source.checked;
      }
      if ($("#" + checkboxes[i].id).is(":checked")) {
        $("#" + checkboxes[i].id)
          .closest("tr")
          .addClass("table-active");
        $("#quick-actions")
          .find("input, textarea, button, select")
          .removeAttr("disabled");
        if ($("#quick-action-type").val() == "") {
          $("#quick-action-apply").attr("disabled", true);
        }
      } else {
        $("#" + checkboxes[i].id)
          .closest("tr")
          .removeClass("table-active");
        resetActionButtons();
      }
    }

    checkRow();
  };


  window.selectAllTable = selectAllTable

  const checkRow = () => {
    if ($(".select-table-row:checked").length > 0) {
      $("#quick-action-form").removeClass('form-disabled');
      $("#quick-action-apply").removeClass("btn-gray").addClass("btn-secondary");
    } else {
      $("#quick-action-form").addClass('form-disabled');
      $("#quick-action-apply").removeClass("btn-secondary").addClass("btn-gray");
    }
  }

  window.checkRow = checkRow

  //reset table action form elements
  const resetActionButtons = () => {
    checkRow()
    if (document.getElementById("select-all-table") !== undefined && document.getElementById("select-all-table") !== null) {
      document.getElementById("select-all-table").checked = false;
      $("#quick-action-form")[0].reset();
      $("#quick-actions")
        .find("input, textarea, button, select")
        .attr("disabled", "disabled");
      $("#quick-action-form").find("select").val(null).trigger("change")
    }
  };

  window.resetActionButtons = resetActionButtons

  const initDatatable = ({ url, finalColumns, advanceFilter, drawCallback = undefined, orderColumn }) => {


    const data_table_limit = $('meta[name="data_table_limit"]').attr('content');


    // console.log("test",advanceFilter);
    window.renderedDataTable = $('#datatable').DataTable({
      processing: true,
      serverSide: true,
      autoWidth: false,
      responsive: true,
      fixedHeader: true,
      lengthMenu: [
        [5, 10, 15, 20, 25, 50, 100, -1],
        [5, 10, 15, 20, 25, 50, 100, 'All'],
      ],
      order: orderColumn,
      pageLength: data_table_limit,
      dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2 pb-1" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
      ajax: {
        "type": "GET",
        "url": url,
        "data": function (d) {
          d.search = {
            value: $('.dt-search').val()
          };
          d.filter = {
            column_status: $('#column_status').val()
          }
          if (typeof advanceFilter == 'function' && advanceFilter() !== undefined) {
            d.filter = { ...d.filter, ...advanceFilter() }
          }
        },
      },

      drawCallback: function () {
        if (laravel !== undefined) {
          window.laravel.initialize();
        }
        if (drawCallback !== undefined && typeof drawCallback == 'function') {
          drawCallback()
        }
      },
      columns: finalColumns,
    });
  }

  // Dynamic footer positioning based on datatable size
  const adjustFooterPosition = () => {
    const footer = document.querySelector('.footer');
    const datatableWrapper = document.querySelector('#datatable_wrapper');
    const mainContent = document.querySelector('.main-content');

    if (footer && datatableWrapper && mainContent) {
      const datatableHeight = datatableWrapper.offsetHeight;
      const viewportHeight = window.innerHeight;
      const headerHeight = 120; // Approximate header height
      const footerHeight = footer.offsetHeight;

      // Calculate available space for content
      const availableSpace = viewportHeight - headerHeight - footerHeight;

      if (datatableHeight < availableSpace) {
        // If datatable is small, position footer at bottom of viewport
        footer.style.position = 'sticky';
        footer.style.bottom = '0';
        footer.style.marginTop = 'auto';
        mainContent.style.minHeight = 'calc(100vh - 120px)';
      } else {
        // If datatable is large, let footer follow content
        footer.style.position = 'relative';
        footer.style.marginTop = '20px';
        mainContent.style.minHeight = 'auto';
      }
    }
  };

  // Enhanced initDatatable function with footer positioning
  const enhancedInitDatatable = ({ url, finalColumns, advanceFilter, drawCallback = undefined, orderColumn }) => {
    const data_table_limit = $('meta[name="data_table_limit"]').attr('content');

    window.renderedDataTable = $('#datatable').DataTable({
      processing: true,
      serverSide: true,
      autoWidth: false,
      responsive: true,
      fixedHeader: true,
      lengthMenu: [
        [5, 10, 15, 20, 25, 50, 100, -1],
        [5, 10, 15, 20, 25, 50, 100, 'All'],
      ],
      order: orderColumn,
      pageLength: data_table_limit,
      dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2 pb-1" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
      ajax: {
        "type": "GET",
        "url": url,
        "data": function (d) {
          d.search = {
            value: $('.dt-search').val()
          };
          d.filter = {
            column_status: $('#column_status').val()
          }
          if (typeof advanceFilter == 'function' && advanceFilter() !== undefined) {
            d.filter = { ...d.filter, ...advanceFilter() }
          }
        },
      },
      drawCallback: function () {
        if (laravel !== undefined) {
          window.laravel.initialize();
        }
        if (drawCallback !== undefined && typeof drawCallback == 'function') {
          drawCallback()
        }
        // Adjust footer position after datatable draw
        setTimeout(adjustFooterPosition, 100);
      },
      columns: finalColumns,
    });
  };

  window.initDatatable = enhancedInitDatatable;
  window.adjustFooterPosition = adjustFooterPosition;

  // Call adjustFooterPosition on window resize
  $(window).on('resize', adjustFooterPosition);

  // Call adjustFooterPosition on page load
  $(document).ready(function () {
    setTimeout(adjustFooterPosition, 500);
  });


  function formatCurrency(number, noOfDecimal, decimalSeparator, thousandSeparator, currencyPosition, currencySymbol) {
    // Convert the number to a string with the desired decimal places
    let formattedNumber = parseFloat(number).toFixed(noOfDecimal);


    // Split the number into integer and decimal parts
    let [integerPart, decimalPart] = formattedNumber.split('.')

    // Add thousand separators to the integer part
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator)

    // Set decimalPart to an empty string if it is undefined
    decimalPart = decimalPart || ''

    // Construct the final formatted currency string
    let currencyString = ''

    if (currencyPosition === 'left' || currencyPosition === 'left_with_space') {
      currencyString += currencySymbol
      if (currencyPosition === 'left_with_space') {
        currencyString += ' '
      }
      currencyString += integerPart
      // Add decimal part and decimal separator if applicable
      if (noOfDecimal > 0) {
        currencyString += decimalSeparator + decimalPart
      }
    }

    if (currencyPosition === 'right' || currencyPosition === 'right_with_space') {
      // Add decimal part and decimal separator if applicable
      if (noOfDecimal > 0) {
        currencyString += integerPart + decimalSeparator + decimalPart
      }
      if (currencyPosition === 'right_with_space') {
        currencyString += ' '
      }
      currencyString += currencySymbol
    }

    return currencyString
  }

  window.formatCurrency = formatCurrency

  // Ensure formatCurrency is available globally with fallback
  if (typeof window.formatCurrency === 'undefined') {
    window.formatCurrency = formatCurrency;
  }

  // Also make it available as a global function for compatibility
  window.currencyFormat = formatCurrency;

})()
