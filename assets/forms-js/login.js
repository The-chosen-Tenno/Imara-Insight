$(document).ready(function () {
    $('#login').on('click', function () {
        var form = $('#formAuthentication')[0];

        if (!form) {
            console.log('Form not found!');
            return;
        }

        if (form.checkValidity()) {
            var formData = new FormData(form);

            $.ajax({
                url: $('#formAuthentication').attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (response) {
                    console.log(response);

                    if (response.success) {
                        // Redirect to index.php on successful login
                        window.location.href = "../../index.php";
                    } else if (response.message === 'pending') {
                        // Pending approval
                        $('#login-card').hide();
                        $('#pending-message').show();
                    } else {
                        // Any other error
                        alert(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', xhr.responseText);
                    alert('Something went wrong!');
                }
            });
        } else {
            form.reportValidity();
        }
    });
});

$(document).ready(function () {
    $('#back-home').on('click', function() {
        location.reload(); // Refreshes the current page
    });
});
