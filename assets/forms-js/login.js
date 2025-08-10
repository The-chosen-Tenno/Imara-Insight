$(document).ready(function () {
    $('#login').on('click', function () {
        var form = $('#formAuthentication')[0]; // Get the form element

        if (!form) {
            console.log('Fill ..');
            return;
        } else {
            var url = $('#formAuthentication').attr('action');
            if (form.checkValidity()) { // Only submit if the form is valid
                var formData = new FormData(form); // Prepare form data

                $.ajax({
                    url: url, // Target URL (ajax_functions.php)
                    type: 'POST',
                    data: formData,
                    contentType: false, // Don't set content type
                    processData: false, // Don't process the data
                    dataType: 'json', // Expect JSON response
                    success: function (response) {
                        showAlert(response.message, response.success ? 'primary' : 'danger');
                        if (response.success) {
                            setTimeout(function () {
                                location.reload(); // Reload page after 1 second
                            }, 5000);
                        }
                    },
                    error: function (error) {
                        // Handle any errors in the request
                        console.error('Error submitting the form:', error);
                        showAlert('Something went wrong..!', 'danger');
                    },
                    complete: function (response) {
                        console.log('Request complete:', response); // Log the request completion
                    }
                });
            } else {
                form.reportValidity(); // Show form validation errors if invalid
            }
        }
    });
});