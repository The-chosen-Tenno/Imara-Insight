$(document).ready(function () {
    $('#formAuthentication').on('submit', function (e) {
        e.preventDefault();

        let email = $('#email').val().trim();
        let password = $('#password').val().trim();
        let error = false;

        $('#password-error').text('');

        // Basic validation
        if (!email || !password) {
            $('#password-error').text('Please enter both email and password.');
            error = true;
        }

        if (error) return;

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: { Email: email, Password: password },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    window.location.href = res.redirect;
                } else {
                    if (res.message === 'pending') {
                        $('#login-card').fadeOut(200, function() {
                            $('#pending-card').fadeIn(200);
                        });
                    } else {
                        $('#password-error').text(res.message);
                    }
                }
            },
            error: function () {
                $('#password-error').text('Something went wrong. Try again.');
            }
        });
    });

    // Back to login
    $('#back-home').on('click', function () {
        $('#pending-card').fadeOut(200, function() {
            $('#login-card').fadeIn(200);
        });
    });

    // Contact HR
    $('#contact-hr').on('click', function () {
        alert('Please contact your HR department.');
    });
});