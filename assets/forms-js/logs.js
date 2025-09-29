$(document).ready(function () {
    $('#create-project').on('click', function () {
        var form = $('#create-form')[0];
        if (!form.checkValidity() || !form.reportValidity())
            return showAlert('Form is not valid.', 'danger', 'create-alert-container');

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
                showAlert('Failed to create Project!', 'danger', 'create-alert-container');
            }
        });
    });

    $('.edit-project-btn').on('click', async function () {
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
                if (res.success) {
                    $('#ProjectId').val(res.data.id);
                    $('#UserID').val(res.data.user_id);
                    $('#ProjectName').val(res.data.project_name);
                    $('#ProjectStatus').val(res.data.status);
                    $('#DescriptionUpdate').val(res.data.description);
                    $('#edit-project-modal').modal('show');
                    $('#ProjectTypeEdit').val(res.data.project_type);
                } else {
                    showAlert(res.message, 'danger', 'edit-alert-container');
                }
            }
        });
    });

    $('#update-project').on('click', function () {
        var form = $('#update-form')[0];
        if (!form.checkValidity() || !form.reportValidity())
            return showAlert('Form is not valid.', 'danger', 'edit-alert-container');

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

    $('.add-sub-assignee-btn').on('click', function () {
        var projectId = $(this).data('id');
        $('#add-sub-assignee-modal').data('project-id', projectId);

        // clear old options
        $('#multiSelect').empty();

        // fetch users not already in the project
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
                    res.data.forEach(function (user) {
                        $('#multiSelect').append(`<option value="${user.id}">${user.full_name}</option>`);
                    });

                    $('#multiSelect').select2({
                        placeholder: "Select project members",
                        width: '100%',
                        allowClear: true,
                        closeOnSelect: false,
                        dropdownParent: $('#add-sub-assignee-modal')
                    });

                    $('#add-sub-assignee-modal').modal('show');
                } else {
                    showAlert(res.message, 'danger', 'subassignee-alert-container');
                }
            },
            error: function () {
                showAlert('Failed to fetch users.', 'danger', 'subassignee-alert-container');
            }
        });
    });

    $('#add-sub-assignee').on('click', function () {
        var form = $('#add-sub-assignee-form')[0];
        var projectId = $('#add-sub-assignee-modal').data('project-id');
        var userIds = $('#multiSelect').val();

        if (!userIds || userIds.length === 0) {
            showAlert('Please select at least one sub-assignee.', 'danger', 'subassignee-alert-container');
            return;
        }

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
                if (res.success) {
                    $('#add-sub-assignee-modal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function () {
                showAlert('Failed to add sub-assignees.', 'danger', 'subassignee-alert-container');
            }
        });
    });

    $('.remove-sub-assignee-btn').on('click', function () {
        var projectId = $(this).data('id');
        $('#removeProjectId').val(projectId);

        // clear old options
        $('#removeMultiSelect').empty();

        // fetch current sub-assignees via AJAX
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
                    res.data.forEach(function (user) {
                        $('#removeMultiSelect').append(`<option value="${user.id}">${user.full_name}</option>`);
                    });

                    $('#removeMultiSelect').select2({
                        placeholder: "Select sub-assignees to remove",
                        width: '100%',
                        allowClear: true,
                        closeOnSelect: false,
                        dropdownParent: $('#remove-sub-assignee-modal')
                    });

                    $('#remove-sub-assignee-modal').modal('show');
                } else {
                    showAlert(res.message, 'danger', 'remove-alert-container');
                }
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

    $('#add-project').on('shown.bs.modal', function () {

        // Generic function to initialize a Select2 dropdown
        function initSelect2(selector, placeholder, multiple = false) {
            $(selector).select2({
                placeholder: placeholder,
                width: '100%',
                allowClear: true,
                closeOnSelect: !multiple,
                dropdownParent: $('#add-project')
            });
        }

        // Generic function to fetch data and populate a Select2
        function populateSelect2(selector, action) {
            $.ajax({
                url: $('#create-form').attr('action'),
                type: 'GET',
                data: {
                    action: action
                },
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        const $select = $(selector);
                        $select.empty();
                        res.data.forEach(item => {
                            $select.append(`<option value="${item.id}">${item.full_name || item.name}</option>`);
                        });
                        $select.trigger('change'); // refresh select2
                    } else {
                        showAlert(res.message, 'danger', 'alert-container');
                    }
                },
                error: function () {
                    showAlert(`Failed to fetch ${action.replace('get_all_', '')}.`, 'danger', 'alert-container');
                }
            });
        }

        // Initialize Select2s
        initSelect2('#createSubAssigneeSelect', 'Select sub-assignees', true);
        initSelect2('#CreateUserID', 'Select assignee');
        initSelect2('#addTags', 'Tags', true);

        // Populate Select2s with AJAX
        populateSelect2('#createSubAssigneeSelect', 'get_all_users');
        populateSelect2('#CreateUserID', 'get_all_users');
        populateSelect2('#addTags', 'get_all_tags');

    });

});