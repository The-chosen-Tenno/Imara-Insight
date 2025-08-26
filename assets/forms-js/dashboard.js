$(document).ready(function () {

    $('#add-project').on('shown.bs.modal', function () {
        $('#createSubAssigneeSelect').select2({
            placeholder: "Select sub-assignees",
            width: '100%',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#add-project')
        });
        $('#CreateUserID').select2({
            placeholder: "Select assignee",
            width: '100%',
            allowClear: true,
            dropdownParent: $('#add-project')
        });
        $.ajax({
            url: $('#create-form').attr('action'),
            type: 'GET',
            data: { action: 'get_all_users' },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#createSubAssigneeSelect').empty();
                    res.data.forEach(user => {
                        $('#createSubAssigneeSelect').append(`<option value="${user.id}">${user.full_name}</option>`);
                    });
                    $('#createSubAssigneeSelect').trigger('change');
                    $('#CreateUserID').empty();
                    res.data.forEach(user => {
                        $('#CreateUserID').append(`<option value="${user.id}">${user.full_name}</option>`);
                    });
                    $('#CreateUserID').trigger('change');
                } else {
                    showAlert(res.message, 'danger', 'alert-container');
                }
            },
            error: function () { showAlert('Failed to fetch users.', 'danger', 'alert-container'); }
        });
    });

    $('#create-project').on('click', function () {
        var form = $('#create-form')[0];
        if (!form.checkValidity() || !form.reportValidity()) {
            return showAlert('Form is not valid.', 'danger', 'create-alert-container');
        }
        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: new FormData(form),
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                showAlert(res.message, res.success ? 'primary' : 'danger', 'create-alert-container');
                if (res.success) {
                    $('#add-project').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function() { showAlert('Failed to create project.', 'danger', 'create-alert-container'); }
        });
    });

    $(document).on('click', '.edit-project-btn', function () {
        var id = $(this).data('id');
        $.ajax({
            url: $('#update-form').attr('action'),
            type: 'GET',
            data: { project_id: id, action: 'get_project' },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#ProjectId').val(res.data.id);
                    $('#UserID').val(res.data.user_id);
                    $('#ProjectName').val(res.data.project_name);
                    $('#DescriptionUpdate').val(res.data.description);
                    $('#ProjectStatusUpdate').val(res.data.status);
                    $('#edit-project-modal').modal('show');
                } else {
                    showAlert(res.message, 'danger', 'edit-alert-container');
                }
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

    $('#add-sub-assignee-modal').on('shown.bs.modal', function () {
        $('#multiSelect').select2({
            placeholder: "Select project members",
            width: '100%',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#add-sub-assignee-modal')
        });
    });

    $('#add-sub-assignee-modal').on('hidden.bs.modal', function () {
        $('#multiSelect').val(null).trigger('change');
        $('#subassignee-alert-container').html('');
        $(this).find('form')[0].reset();
    });

    $(document).on('click', '.add-sub-assignee-btn', function () {
        var projectId = $(this).data('id');
        $('#add-sub-assignee-modal').data('project-id', projectId);
        $('#multiSelect').empty();
        $.ajax({
            url: $('#add-sub-assignee-form').attr('action'),
            type: 'GET',
            data: { action: 'get_available_sub_assignees', project_id: projectId },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    res.data.forEach(function (user) { $('#multiSelect').append(`<option value="${user.id}">${user.full_name}</option>`); });
                    $('#multiSelect').select2({ placeholder: "Select project members", width: '100%', allowClear: true, closeOnSelect: false, dropdownParent: $('#add-sub-assignee-modal') });
                    $('#add-sub-assignee-modal').modal('show');
                } else { showAlert(res.message, 'danger', 'subassignee-alert-container'); }
            },
            error: function () { showAlert('Failed to fetch users.', 'danger', 'subassignee-alert-container'); }
        });
    });

    $('#add-sub-assignee').on('click', function () {
        var form = $('#add-sub-assignee-form')[0];
        var projectId = $('#add-sub-assignee-modal').data('project-id');
        var userIds = $('#multiSelect').val();
        if (!userIds || userIds.length === 0) return showAlert('Please select at least one sub-assignee.', 'danger', 'subassignee-alert-container');
        var formData = new FormData();
        formData.append('action', 'add_sub_assignees');
        formData.append('project_id', projectId);
        userIds.forEach(id => formData.append('user_id[]', id));
        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                showAlert(res.message, res.success ? 'primary' : 'danger', 'subassignee-alert-container');
                if (res.success) { $('#add-sub-assignee-modal').modal('hide'); setTimeout(() => location.reload(), 1000); }
            },
            error: function () { showAlert('Failed to add sub-assignees.', 'danger', 'subassignee-alert-container'); }
        });
    });

    $(document).on('click', '.remove-sub-assignee-btn', function () {
        var projectId = $(this).data('id');
        $('#removeProjectId').val(projectId);
        $('#removeMultiSelect').empty();
        $.ajax({
            url: $('#remove-sub-assignee-form').attr('action'),
            type: 'GET',
            data: { action: 'get_sub_assignees', project_id: projectId },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    res.data.forEach(function (user) { $('#removeMultiSelect').append(`<option value="${user.id}">${user.full_name}</option>`); });
                    $('#removeMultiSelect').select2({ placeholder: "Select sub-assignees to remove", width: '100%', allowClear: true, closeOnSelect: false, dropdownParent: $('#remove-sub-assignee-modal') });
                    $('#remove-sub-assignee-modal').modal('show');
                } else { showAlert(res.message, 'danger', 'remove-alert-container'); }
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
                if (res.success) { $('#remove-sub-assignee-modal').modal('hide'); setTimeout(() => location.reload(), 1000); }
            }
        });
    });

    $('#project-images').on('change', function () {
        var files = this.files;
        var $descriptionsContainer = $('#image-descriptions-container');
        $descriptionsContainer.empty();
        $.each(files, function (index, file) {
            var $div = $('<div class="mb-3 d-flex align-items-center"></div>');
            var $img = $('<img class="me-2" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;" />');
            var reader = new FileReader();
            reader.onload = function (e) { $img.attr('src', e.target.result); };
            reader.readAsDataURL(file);
            var $info = $('<div class="flex-grow-1"></div>');
            $info.append(`<div class="fw-semibold">${file.name}</div>`);
            $info.append('<input type="text" class="form-control mt-1" name="project_images_description[]" placeholder="Image description" />');
            $div.append($img).append($info);
            $descriptionsContainer.append($div);
        });
    });

    $('#edit-project-images').on('change', function () {
        var files = this.files;
        var $descriptionsContainer = $('#edit-image-descriptions-container');
        $descriptionsContainer.empty();
        $.each(files, function (index, file) {
            var $div = $('<div class="mb-3 d-flex align-items-center"></div>');
            var $img = $('<img class="me-2" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;" />');
            var reader = new FileReader();
            reader.onload = function (e) { $img.attr('src', e.target.result); };
            reader.readAsDataURL(file);
            var $info = $('<div class="flex-grow-1"></div>');
            $info.append(`<div class="fw-semibold">${file.name}</div>`);
            $info.append('<input type="text" class="form-control mt-1" name="project_images_description[]" placeholder="Image description" />');
            $div.append($img).append($info);
            $descriptionsContainer.append($div);
        });
    });

});
