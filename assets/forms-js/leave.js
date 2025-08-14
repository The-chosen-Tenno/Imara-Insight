$(document).on("submit", "#leave-request-form", function (e) {
  e.preventDefault();
  console.log("AJAX submit intercepted");

  var form = $(this);
  var formData = new FormData(this);

  $.ajax({
    url: form.attr("action"),
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (response) {
      console.log(response);
      
      // Show alert message
      showAlert(response.message, response.success ? "primary" : "danger");

      if (response.success) {
        // Reset form
        form[0].reset();

        // Update leave requests list dynamically
        // (Assumes your table has id="leave-requests-table" and tbody to append to)
        if (response.data) {
          let newRow = `
            <tr>
              <td>${response.data.date || ''}</td>
              <td>${response.data.reason || ''}</td>
              <td>${response.data.status || 'Pending'}</td>
            </tr>
          `;
          $("#leave-requests-table tbody").append(newRow);
        }

        // Optionally scroll to table
        $("html, body").animate(
          { scrollTop: $("#leave-requests-table").offset().top },
          500
        );
      } else {
        // Optional: show error alert in a different container
        showAlert(
          response.message,
          response.success ? "primary" : "danger",
          "delete-alert-container"
        );
      }
    },
    error: function (err) {
      console.error("Error submitting the form:", err);
      showAlert("Failed to request Leave!", "danger");
    },
  });
});
