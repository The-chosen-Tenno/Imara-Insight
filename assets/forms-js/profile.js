$(document).ready(function () {

    // open edit modal
    $('.edit-user-btn').on('click', async function () {
        var user_id = $(this).data('id');
        await getUserById(user_id);
    });

    // update user
    $('#update-user').on('click', function () {
        var form = $('#update-form')[0];
        form.reportValidity();

        if (form.checkValidity()) {
            var url = $('#update-form').attr('action');
            var formData = new FormData($('#update-form')[0]);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function (response) {
                    showAlert(response.message, response.success ? 'primary' : 'danger', 'edit-alert-container');
                    if (response.success) {
                        $('#edit-user-modal').modal('hide');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function (error) {
                    console.error('Error submitting the form:', error);
                },
                complete: function (response) {
                    console.log('Request complete:', response);
                }
            });
        } else {
            showAlert('Form is not valid. Please check your inputs.', 'danger');
        }
    });

    // fetch user details by id
    async function getUserById(id) {
        var url = $('#update-form').attr('action');
        $('#edit-additional-fields').empty();

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                user_id: id,
                action: 'get_user_by_id'
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);

                if (response.success) {
                    var user = response.data;

                    $('#edit-user-modal #user_id').val(user.id);
                    $('#edit-user-modal #user_name').val(user.user_name);
                    $('#edit-user-modal #email').val(user.email);

                    $('#edit-user-modal').modal('show');
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function (error) {
                console.error('Error submitting the form:', error);
            },
            complete: function (response) {
                console.log('Request complete:', response);
            }
        });
    }
});
