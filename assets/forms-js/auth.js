$(document).ready(function () {

    $(document).on('click', '.accept-user-btn', async function () {
        var user_id = $(this).data('id');
        var url = $('#create-form').attr('action');
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                user_id: user_id,
                action: 'accept_user'
            },
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
            error: function (error) {
                console.error('Error Accepting the Account:', error);
            },
            complete: function (response) {
                console.log('Request complete:', response);
            }
        });
    });

    $(document).on('click', '.decline-user-btn', async function () {
        var user_id = $(this).data('id');
        var url = $('#create-form').attr('action');
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                user_id: user_id,
                action: 'decline_user'
            },
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
            error: function (error) {
                console.error('Error declining the Account:', error);
            },
            complete: function (response) {
                console.log('Request complete:', response);
            }
        });
    });
});