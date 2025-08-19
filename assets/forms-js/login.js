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
                         else if (response.message === 'pending') {
                        // Pending approval
                        $('.authentication-inner').hide();
                        $('#pending-message').show();

                         }else {
                        // Show error under password field
                            $("#password-error").after(
                        '<span class="error-message" style="color: red;">' + response.message + '</span>'
                        );
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

    $("#formAuthentication").on("submit", function(event) {
        // Prevent form from submitting
        event.preventDefault();

        // Clear previous error messages
        $(".error-message").remove();

        // Get form values
        const email = $("#email").val().trim();
        const password = $("#password").val().trim();
        let isValid = true;

        // Email validation
        if (email === "") {
            $("#email").after('<span class="error-message" style="color: red;">Type Email!</span>');
            isValid = false;
        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                $("#email").after('<span class="error-message" style="color: red;">Enter a valid Email!</span>');
                isValid = false;
            }
        }

            // Password validation
            if (password === "") {
                $("#password-error").after('<span class="error-message" style="color: red;">Enter Password!</span>');
                isValid = false;
            }

            // If all validations pass, submit the form
            if (isValid) {
                // Now, submit the form
                this.submit();
            }
        });
    });
