$(document).ready(function () {

    function showAlert(message, type = 'primary', containerId = 'alert-container') {
        $('#' + containerId).html(`<div class="alert alert-${type}">${message}</div>`);
    }

    $('#add-short-leave').on('show.bs.modal', function () {
        const leaveTime = new Date();
        $('#leave-time').html('Are you sure you want to take a short leave at <br>' + '<strong>' + leaveTime.toLocaleString() + '</strong>');
    });

    // ---------- Add Project Modal ----------
    $('#add-project').on('shown.bs.modal', function () {
        // Sub-assignees select
        $('#createSubAssigneeSelect').select2({
            placeholder: "Select sub-assignees",
            width: '100%',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#add-project')
        });

        // Main assignee select
        $('#CreateUserID').select2({
            placeholder: "Select assignee",
            width: '100%',
            allowClear: true,
            dropdownParent: $('#add-project')
        });

        // Fetch all users
        $.ajax({
            url: $('#create-form').attr('action'),
            type: 'GET',
            data: {
                action: 'get_all_users'
            },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#createSubAssigneeSelect, #CreateUserID').empty();
                    res.data.forEach(user => {
                        $('#createSubAssigneeSelect').append(`<option value="${user.id}">${user.full_name}</option>`);
                        $('#CreateUserID').append(`<option value="${user.id}">${user.full_name}</option>`);
                    });
                    $('#createSubAssigneeSelect, #CreateUserID').trigger('change');
                } else showAlert(res.message, 'danger', 'alert-container');
            },
            error: function () {
                showAlert('Failed to fetch users.', 'danger', 'alert-container');
            }
        });

        // Fetch all tags
        $.ajax({
            url: $('#create-form').attr('action'),
            type: 'GET',
            data: {
                action: 'get_all_tags'
            },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#addTags').empty();
                    res.data.forEach(tag => $('#addTags').append(`<option value="${tag.id}">${tag.name}</option>`));
                    $('#addTags').trigger('change');
                } else showAlert(res.message, 'danger', 'alert-container');
            },
            error: function () {
                showAlert('Failed to fetch tags.', 'danger', 'alert-container');
            }
        });

        // Tags select2
        $('#addTags').select2({
            placeholder: "Tags",
            width: '100%',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#add-project'),
            tags: true,
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newTag: true
                };
            }
        });
    });
    // Create project
    $('#create-project').on('click', function () {
        var form = $('#create-form')[0];
        if (!form.checkValidity() || !form.reportValidity()) return showAlert('Form is not valid.', 'danger', 'create-alert-container');

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: new FormData(form),
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                showAlert(res.message, res.success ? 'primary' : 'danger', 'create-alert-container');
                if (res.success) {
                    $('#add-project').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function () {
                showAlert('Failed to create project.', 'danger', 'create-alert-container');
            }
        });
    });

    // ---------- Edit Project ----------
    $(document).on('click', '.edit-project-btn', function () {
        var id = $(this).data('id');
        $.ajax({
            url: $('#update-form').attr('action'),
            type: 'GET',
            data: {
                project_id: id,
                action: 'get_project'
            },
            dataType: 'json',
            success: function (res) {
                if (!res.success) return showAlert(res.message, 'danger', 'edit-alert-container');

                $('#ProjectId').val(res.data.id);
                $('#UserID').val(res.data.user_id);
                $('#ProjectName').val(res.data.project_name);
                $('#DescriptionUpdate').val(res.data.description);
                $('#ProjectStatusUpdate').val(res.data.status);

                // Add tags select
                $.ajax({
                    url: $('#update-form').attr('action'),
                    type: 'GET',
                    data: {
                        action: 'get_all_tags'
                    },
                    dataType: 'json',
                    success: function (tagRes) {
                        $('#addTagsEdit').empty();
                        if (tagRes.success) {
                            tagRes.data.forEach(tag => $('#addTagsEdit').append(`<option value="${tag.id}">${tag.name}</option>`));
                            $('#addTagsEdit').val(res.data.tags || []).trigger('change');
                        }
                        $('#addTagsEdit').select2({
                            placeholder: "Add tags",
                            width: '100%',
                            allowClear: true,
                            closeOnSelect: false,
                            dropdownParent: $('#edit-project-modal'),
                            tags: true,
                            createTag: function (params) {
                                return {
                                    id: params.term,
                                    text: params.term,
                                    newTag: true
                                };
                            }
                        });
                    }
                });

                // Remove tags select
                $.ajax({
                    url: $('#update-form').attr('action'),
                    type: 'GET',
                    data: {
                        action: 'get_all_tags_to_remove',
                        project_id: id
                    },
                    dataType: 'json',
                    success: function (tagRes) {
                        $('#removeTagsEdit').empty();
                        if (tagRes.success && tagRes.data.length > 0) {
                            tagRes.data.forEach(tag => $('#removeTagsEdit').append(`<option value="${tag.id}">${tag.name}</option>`));
                            if (res.data.tags && res.data.tags.length > 0) {
                                const selectedIds = res.data.tags.map(id => parseInt(id));
                                $('#removeTagsEdit').val(selectedIds);
                            }
                        }
                        $('#removeTagsEdit').select2({
                            placeholder: "Remove tags",
                            width: '100%',
                            allowClear: true,
                            closeOnSelect: false,
                            dropdownParent: $('#edit-project-modal'),
                            tags: false
                        });
                    }
                });

                $('#edit-project-modal').modal('show');
            },
            error: function () {
                showAlert('Failed to fetch project data.', 'danger', 'edit-alert-container');
            }
        });
    });

    $('#update-project').on('click', function () {
        var form = $('#update-form')[0];
        if (!form.checkValidity() || !form.reportValidity()) return showAlert('Form is not valid.', 'danger', 'edit-alert-container');

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: new FormData(form),
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                showAlert(res.message, res.success ? 'primary' : 'danger', 'edit-alert-container');
                if (res.success) {
                    $('#edit-project-modal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                }
            }
        });
    });

    $('#short-leave-form').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#short-alert-container').html('<div class="text-success">Short leave recorded successfully!</div>');
                    setTimeout(() => {
                        $('#add-short-leave').modal('hide');
                        $('#short-alert-container').html('');
                    }, 1500);
                    $('#add-short-leave form')[0].reset();
                } else {
                    $('#alert-container').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function (xhr, status, error) {
                $('#alert-container').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });

    // ---------- Add Sub-Assignees ----------
    $(document).on('click', '.add-sub-assignee-btn', function () {
        var projectId = $(this).data('id');
        $('#add-sub-assignee-modal').data('project-id', projectId);
        $('#multiSelect').empty();

        $.ajax({
            url: $('#add-sub-assignee-form').attr('action'),
            type: 'GET',
            data: {
                action: 'get_available_sub_assignees',
                project_id: projectId
            },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    res.data.forEach(user => $('#multiSelect').append(`<option value="${user.id}">${user.full_name}</option>`));
                    $('#multiSelect').select2({
                        placeholder: "Select project members",
                        width: '100%',
                        allowClear: true,
                        closeOnSelect: false,
                        dropdownParent: $('#add-sub-assignee-modal')
                    });
                    $('#add-sub-assignee-modal').modal('show');
                } else showAlert(res.message, 'danger', 'subassignee-alert-container');
            }
        });
    });

    $('#add-sub-assignee').on('click', function () {
        var projectId = $('#add-sub-assignee-modal').data('project-id');
        var userIds = $('#multiSelect').val();
        if (!userIds || userIds.length === 0) return showAlert('Please select at least one sub-assignee.', 'danger', 'subassignee-alert-container');

        var formData = new FormData();
        formData.append('action', 'add_sub_assignees');
        formData.append('project_id', projectId);
        userIds.forEach(id => formData.append('user_id[]', id));

        $.ajax({
            url: $('#add-sub-assignee-form').attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                showAlert(res.message, res.success ? 'primary' : 'danger', 'subassignee-alert-container');
                if (res.success) {
                    $('#add-sub-assignee-modal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                }
            }
        });
    });

    // ---------- Remove Sub-Assignees ----------
    $(document).on('click', '.remove-sub-assignee-btn', function () {
        var projectId = $(this).data('id');
        $('#removeProjectId').val(projectId);
        $('#removeMultiSelect').empty();

        $.ajax({
            url: $('#remove-sub-assignee-form').attr('action'),
            type: 'GET',
            data: {
                action: 'get_sub_assignees',
                project_id: projectId
            },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    res.data.forEach(user => $('#removeMultiSelect').append(`<option value="${user.id}">${user.full_name}</option>`));
                    $('#removeMultiSelect').select2({
                        placeholder: "Select sub-assignees to remove",
                        width: '100%',
                        allowClear: true,
                        closeOnSelect: false,
                        dropdownParent: $('#remove-sub-assignee-modal')
                    });
                    $('#remove-sub-assignee-modal').modal('show');
                } else showAlert(res.message, 'danger', 'remove-alert-container');
            }
        });
    });

    $('#remove-sub-assignee').on('click', function () {
        var form = $('#remove-sub-assignee-form')[0];
        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: new FormData(form),
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                showAlert(res.message, res.success ? 'primary' : 'danger', 'remove-alert-container');
                if (res.success) {
                    $('#remove-sub-assignee-modal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                }
            }
        });
    });

    // ---------- Project Images Preview ----------
    function setupImagePreview(inputId, containerId) {
        $(inputId).on('change', function () {
            var files = this.files;
            var $container = $(containerId).empty();
            $.each(files, function (index, file) {
                var $div = $('<div class="mb-3 d-flex align-items-center"></div>');
                var $img = $('<img class="me-2" style="width:60px;height:60px;object-fit:cover;border-radius:4px;">');
                var reader = new FileReader();
                reader.onload = function (e) {
                    $img.attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
                var $info = $('<div class="flex-grow-1"></div>');
                $info.append(`<div class="fw-semibold">${file.name}</div>`);
                $info.append('<input type="text" class="form-control mt-1" name="project_images_description[]" placeholder="Image description" />');
                $div.append($img).append($info);
                $container.append($div);
            });
        });
    }
    setupImagePreview('#project-images', '#image-descriptions-container');
    setupImagePreview('#edit-project-images', '#edit-image-descriptions-container');

});