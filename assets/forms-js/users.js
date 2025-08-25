$(document).ready(function () {

    $('#create').on('click', function () {
        var form = $('#create-form')[0]; 
        if (!form) {
            console.log('Something went wrong..');
            return;
        }

        var url = $('#create-form').attr('action');
        if (form.checkValidity()) { 
            var formData = new FormData(form);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (response) {
                    showAlert(response.message, response.success ? 'primary' : 'danger');
                    if (response.success) {
                        $('#add-book').modal('hide'); 
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function (error) {
                    console.error('Error submitting the form:', error);
                    showAlert('Something went wrong..!', 'danger');
                },
                complete: function (response) {
                    console.log('Request complete:', response);
                }
            });
        } else {
            form.reportValidity();
        }
    });

    $('.edit-user-btn').on('click', async function () {
        var user_id = $(this).data('id');
        await getUserById(user_id);
    })

$(document).on('click', '.edit-user_status-btn', function () {
    var id = $(this).data('id');
    $.ajax({
        url: $('#update-form').attr('action'),
        type: 'GET',
        data: { user_id: id, action: 'get_user' },
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#UserID').val(res.data.user_id);
                $('#user_Status').val(res.data.user_status); 
                $('#edit-user_status-modal').modal('show');
            } else {
                showAlert(res.message, 'danger', 'edit-alert-container');
            }
        }
    });
});

$(document).on('click', '.change-status-btn', function () {
    let userId = $(this).data('id');
    let userStatus = $(this).data('status');

    $('#UserID').val(userId);
    $('#user_Status').val(userStatus);

    $('#edit-user_status-modal').modal('show');
});


$('#update_user_status').on('click', function (e) {
    e.preventDefault();

    console.log("Sending:", {
        action: 'update_user_status',
        user_id: $('#UserID').val(),
        user_status: $('#user_Status').val()
    });

    $.ajax({
        url: $('#update-form').attr('action'),
        type: 'POST',
        data: {
            action: 'update_user_status',
            user_id: $('#UserID').val(),
            user_status: $('#user_Status').val()
        },
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                showAlert(res.message, 'success', 'edit-alert-container');
                $('#edit-user_status-modal').modal('hide');
                setTimeout(function () {
                    location.reload();
                }, 800);
            } else {
                showAlert(res.message, 'danger', 'edit-alert-container');
            }
        },
        error: function (xhr) {
            console.error('Error:', xhr.responseText);
            showAlert('Something went wrong..!', 'danger', 'edit-alert-container');
        }
    });
});






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
            var message = ('Form is not valid. Please check your inputs.');
            showAlert(message, 'danger');
        }
    });

    async function getUserById(id) {
        var url = $('#update-form').attr('action');
        $('#edit-additional-fields').empty();

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                user_id: id,
                action: 'get_user'
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);

                if (response.success) {
                    var user_id = response.data.id;
                    var username = response.data.user_name;
                    var email = response.data.email;
                    var user_status = response.data.user_status;

                    $('#edit-user-modal #user_id').val(user_id);
                    $('#edit-user-modal #user_name').val(username);
                    $('#edit-user-modal #email').val(email);

                    var statusDropdown = `
                        <div class="mb-3">
                            <label for="user_status" class="form-label">Status</label>
                            <select id="user_status" name="user_status" class="form-select">
                                <option value="active" ${user_status === 'active' ? 'selected' : ''}>Active</option>
                                <option value="inactive" ${user_status === 'inactive' ? 'selected' : ''}>Inactive</option>
                            </select>
                        </div>`;
                    $('#edit-additional-fields').html(statusDropdown);

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

    $('.delete-user-btn').on('click', async function () {
        var user_id = $(this).data('id');
        var is_confirm = confirm('Are you sure,Do you want to delete?');
        if (is_confirm) await deleteById(user_id);
    })

    async function deleteById(user_id) {
        var url = $('#update-form').attr('action');

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                user_id: user_id,
                action: 'delete_user',
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert(response.message, response.success ? 'primary' : 'danger', 'delete-alert-container');
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
