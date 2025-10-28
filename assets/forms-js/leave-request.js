$(document).ready(function () {
    $('#leave_duration').on('change', function () {
        if ($(this).val() === 'half') {
            $('#half_day_off').show().find('input').attr('required', true);
        } else {
            $('#half_day_off').hide().find('input').removeAttr('required');
        }
    });

    $('#leave_duration').on('change', function () {
        if ($(this).val() === 'multi') {
            $('#multi_day_off').show().find('input').attr('required', true);
            $('#date_off_div').hide().find('input').removeAttr('required', true);
        } else {
            $('#multi_day_off').hide().find('input').removeAttr('required');
            $('#date_off_div').show().find('input').attr('required');
        }
    });

    $('#leave-request-form').on('submit', function (e) {
        e.preventDefault();

        var form = this;
        var formData = new FormData(form);
        var url = $(form).attr('action');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                $('#leave-request-form').hide();
                $('#success-text').text(response.message);

                if (response.success) {
                    $('.text-success').show();
                    $('.text-failed').hide();
                } else {
                    $('.text-success').hide();
                    $('.text-failed').show();
                }

                $('#leave-success-message').show();
            },
            error: function (err) {
                console.error('AJAX error:', err);
                showAlert('Failed to submit leave request!', 'danger');
            }
        });
    });

    $('#request-again').on('click', function () {
        $('#leave-success-message').hide();
        $('#leave-request-form')[0].reset();
        $('#other_reason_div').hide().find('input').removeAttr('required');
        $('#half_day_off').hide().find('input').removeAttr('required');
        $('#leave-request-form').show();
    });

    $(document).on('click', '.approve-leave-btn', async function () {
        var id = $(this).data('id');
        var user_id = $(this).data('userId');
        $.ajax({
            url: "../../services/ajax_functions.php",
            type: 'POST',
            data: {
                id: id,
                user_id: user_id,
                action: 'approve_leave',
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                showAlert(response.message, response.success ? 'primary' : 'danger');

                if (response.success) {
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
            },
            error: function (error) {
                console.error('Error Accepting the Account:', error);
            }
        });
    });

    $(document).on('click', '.deny-leave-btn', async function () {
        var id = $(this).data('id');
        $.ajax({
            url: "../../services/ajax_functions.php",
            type: 'POST',
            data: {
                id: id,
                action: 'deny_leave',
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                showAlert(response.message, response.success ? 'primary' : 'danger');

                if (response.success) {
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
            },
            error: function (error) {
                console.error('Error declining the Account:', error);
            }
        });
    });
});