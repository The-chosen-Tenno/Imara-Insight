 $(document).ready(function () {
    $('#leave-request-form').on('submit', function (e) {
        e.preventDefault(); // stop the browser’s default submission

        var form = this;
        var formData = new FormData(form);
        var url = $(form).attr('action');

        $.ajax({
            url: url,
            type: 'POST', // force POST here
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                showAlert(response.message, response.success ? 'primary' : 'danger');
                if (response.success) {
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function (error) {
                console.error('Error submitting the form:', error);
                showAlert('Failed to submit leave request!', 'danger');
            },
            complete: function () {
                console.log('AJAX submit complete');
            }
        });
    });
});