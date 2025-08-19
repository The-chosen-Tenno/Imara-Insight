$(document).ready(function () {

    $('#create-project').on('click', function () {
        var form = $('#create-form')[0] || null;
        if (!form) console.log('Something went wrong..');

        var url = $('#create-form').attr('action');
        if (form.checkValidity() && form.reportValidity()) {
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
                        $('#add-project').modal('hide');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function (error) {
                    console.error('Error submitting the form:', error);
                    showAlert('Failed to create Project!', 'danger');
                },
                complete: function (response) {
                    console.log('Request complete:', response);
                }
            });
        } else {
            showAlert('Form is not valid. Please check your inputs.', 'danger');
        }
    });

    $('.edit-project-btn').on('click', async function () {
        var projectId = $(this).data('id');
        await getProjectById(projectId);
    })

    $('#update-project').on('click', function () {
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
                        $('#edit-project-modal').modal('hide');
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

    $('.open-images').on('click', async function () {
        var project_id = $(this).data('id');
        console.log($(this).data('id'));
        await getImageByProjectId(project_id);
    })

    async function getProjectById(id) {
        var url = $('#update-form').attr('action');
        $('#edit-additional-fields').empty();
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                project_id: id,
                action: 'get_project'
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                showAlert(response.message, response.success ? 'primary' : 'danger');
                if (response.success) {
                    var ProjectId = response.data.id;
                    var UserID = response.data.user_id;
                    var ProjectName = response.data.project_name;
                    var ProjectStatus = response.data.status;


                    $('#edit-project-modal #ProjectId').val(ProjectId);
                    $('#edit-project-modal #UserID').val(UserID);
                    $('#edit-project-modal #ProjectName').val(ProjectName);
                    $('#edit-project-modal #ProjectStatus').val(ProjectStatus);
                } else {
                    $('#edit-additional-fields').empty();
                }
                $('#edit-user-modal').modal('show');

            },
            error: function (error) {
                console.error('Error submitting the form:', error);
            },
            complete: function (response) {
                console.log('Request complete:', response);
            }
        });
    }

    async function getImageByProjectId(id) {
        var url = $('#update-form').attr('action');
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                project_id: id,
                action: 'get_images'
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                showAlert(response.message, response.success ? 'primary' : 'danger');
                if (response.success) {
                    var ProjectId = response.data.id;
                    var UserID = response.data.user_id;
                    var ProjectName = response.data.project_name;
                    var ProjectStatus = response.data.status;


                    $('#edit-project-modal #ProjectId').val(ProjectId);
                    $('#edit-project-modal #UserID').val(UserID);
                    $('#edit-project-modal #ProjectName').val(ProjectName);
                    $('#edit-project-modal #ProjectStatus').val(ProjectStatus);
                } else {
                    $('#edit-additional-fields').empty();
                }
                $('#show-images').modal('show');

            },
            error: function (error) {
                console.error('Error submitting the form:', error);
            },
            complete: function (response) {
                console.log('Request complete:', response);
            }
        });
    }
    $('#add-sub-assignee-modal').on('shown.bs.modal', function () {
        $('#multiSelect').select2({
            placeholder: "Select project members",
            width: '100%',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#add-sub-assignee-modal') // ensures dropdown appears above modal
        });
    });
    $('#add-sub-assignee').on('click', function () {
        var projectId = $('#add-sub-assignee-modal').data('project-id');
        var selectedUsers = $('#multiSelect').val(); // array of selected user IDs
        var url = $('#add-sub-assignee-form').attr('action');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                project_id: projectId,
                user_id: selectedUsers,
                action: 'update_sub_assignees'
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#add-sub-assignee-modal').modal('hide');
                    alert('Sub-assignees updated!');
                    location.reload();
                } else {
                    alert('Failed to update sub-assignees!');
                }
            }
        });
    });

});