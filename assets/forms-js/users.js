$(document).ready(function () {

    // Create user
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
                }
            });
        } else {
            form.reportValidity();
        }
    });

    // Open edit modal and fill status
    $(document).on('click', '.edit-user_status-btn', function () {
        var id = $(this).data('id');
        $.ajax({
            url: $('#update-form').attr('action'),
            type: 'GET',
            data: {
                user_id: id,
                action: 'get_user'
            },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    var user = res.data;

                    $('#UserID').val(user.user_id);

                    // Build status dropdown
                    var statusDropdown = `
                        <select id="user_Status" class="form-select">
                            <option value="active" ${user.user_status === 'active' ? 'selected' : ''}>Active</option>
                            <option value="inactive" ${user.user_status === 'inactive' ? 'selected' : ''}>Inactive</option>
                        </select>`;
                    $('#edit-alert-container').html(statusDropdown); // put it inside alert container or another div

                    $('#edit-user_status-modal').modal('show');
                } else {
                    showAlert(res.message, 'danger', 'edit-alert-container');
                }
            }
        });
    });

    // Quick-change status button
    $(document).on('click', '.change-status-btn', function () {
        let userId = $(this).data('id');
        let userStatus = $(this).data('status');

        $('#UserID').val(userId);
        $('#user_Status').val(userStatus);

        $('#edit-user_status-modal').modal('show');
    });

    // Update status
    $('#update_user_status').on('click', function (e) {
        e.preventDefault();

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
});
