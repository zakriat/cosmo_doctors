;(function ($) {
  ;('use strict')
  function createCustomEvent(eventName, data) {
    document.dispatchEvent(new CustomEvent(eventName, { detail: data }))
  }

  function setEditID({ data, resetData, event }) {
    if (data.form_id !== '') {
      createCustomEvent(event, data)
    } else {
      removeEditID(resetData, event)
    }
  }
  function removeEditID(resetData, event) {
    createCustomEvent(event, resetData)
  }

  const resetData = {
    show: false
  }

  $(document).on('click', '[data-modal="import"]', function () {
    const data = {
      show: true
    }
    console.log(data, resetData)

    setEditID({ data: data, resetData: resetData, event: 'import_modal' })
  })

  $(document).on('click', '[data-modal="export"]', function () {
    const data = {
      show: true
    }
    console.log(data, resetData)
    setEditID({ data: data, resetData: resetData, event: 'export_modal' })
  })
})(window.$)

function tableReload() {
  $('#datatable').DataTable().ajax.reload();
}

function getCsrfToken() {
  return $('meta[name="csrf-token"]').attr('content');
}

function handleAction(URL, method, confirmationMessage, successMessage) {
  Swal.fire({
    title: confirmationMessage,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: window.localMessagesUpdate?.messages?.yes || "Yes",
    cancelButtonText: window.localMessagesUpdate?.messages?.cancel || "Cancel"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: URL,
        method: method,
        data: {
          _token: getCsrfToken()
        },
        dataType: 'json',
        success: function(res) {
          Swal.fire({
            title: "Success!",
            text: successMessage,
            icon: "success"
          });
          tableReload();
        },
        error: function(xhr, status, error) {
          Swal.fire({
            title: "Error!",
            text: "An error occurred while processing your request.",
            icon: "error"
          });
        }
      });
    }
  });
}

$(document).on('click', '.restore-tax', function(event) {
  event.preventDefault();
  const URL = $(this).attr('href');
  
  let confirmMessage = "Are you sure you want to proceed?";
  let successMessage = "Action completed successfully!";
  
  if ($(this).data('confirm-message')) {
    confirmMessage = $(this).data('confirm-message');
  }
  
  if ($(this).data('success-message')) {
    successMessage = $(this).data('success-message');
  }
  
  handleAction(URL, 'POST', confirmMessage, successMessage);
});
