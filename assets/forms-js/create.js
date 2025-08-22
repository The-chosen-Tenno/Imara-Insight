$(document).ready(function () {
    $('#create-user-btn').on('click', function (e) {
        e.preventDefault();

        var form = $('#createUserForm')[0];
        if (!form) {
            console.error('Form not found!');
            return;
        }

        // Clear errors
        $('#username-error').text('');
        $('#email-error').text('');
        $('#password-error').text('');
        $('.alert').remove();



        if (form.checkValidity()) {
            var formData = new FormData(form);

            $.ajax({
                url: $('#createUserForm').attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#createUserForm').prepend(
                            '<div class="alert alert-success" role="alert">' + response.message + '</div>'
                        );
                        setTimeout(function () {
                            window.location.href = "../auth/login.php";
                        }, 1500);
                        form.reset();
                    } else {
                        if (response.field === 'username') {
                            $('#username-error').text(response.message);
                        } else if (response.field === 'email') {
                            $('#email-error').text(response.message);
                        } else if (response.field ==='password'){
                            $('#password-error').text(response.message);
                        } 
                          else {
                            $('#createUserForm').prepend(
                                '<div class="alert alert-danger" role="alert">' + response.message + '</div>'
                            );
                        }
                    }
                },
                error: function (xhr) {
                    console.error('AJAX error:', xhr.responseText);
                    $('#createUserForm').prepend(
                        '<div class="alert alert-danger" role="alert">Something went wrong! Please try again.</div>'
                    );
                }
            });
        } else {
            form.reportValidity();
        }

        // // Optional: strong password check (uppercase, lowercase, number, special char)
        // var strongPasswordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).+$/;
        // if (!strongPasswordRegex.test(password)) {
        //     $('#password-error').text('Password must contain uppercase, lowercase, number, and special character.');
        //     return;
        // }
        
    });
});
