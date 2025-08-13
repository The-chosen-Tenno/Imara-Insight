$(document).ready(function () {
    var $form = $('#leave-request-form');

    $form.on('submit', function (e) {
        e.preventDefault();
        if (!$form.length) return console.error('Form not found');

        if (!$form[0].checkValidity()) {
            showAlert('Form is not valid. Please check your inputs.', 'danger');
            return;
        }

        var formData = new FormData($form[0]);
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                showAlert(response.message, response.success ? 'primary' : 'danger');
                if (response.success) {
                    $('#add-project').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function (err) {
                console.error('Error submitting the form:', err);
                showAlert('Failed to request Leave!', 'danger');
            },
            complete: function (resp) {
                console.log('Request complete:', resp);
            }
        });
    });
});
