$(document).on('submit', '#leave-request-form', function (e) {
    e.preventDefault();
    console.log('AJAX submit intercepted');

    var formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
            success: function (response) {
                console.log(response);
                showAlert(response.message, response.success ? 'primary' : 'danger');
                if (response.success) {
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert(response.message, response.success ? 'primary' : 'danger', 'delete-alert-container');
                }
            },
        error: function (err) {
            console.error('Error submitting the form:', err);
            showAlert('Failed to request Leave!', 'danger');
        }
    });
});
