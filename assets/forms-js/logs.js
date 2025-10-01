$(document).ready(function () {
    function showAlert(message, type = 'primary', containerId = 'alert-container') {
        $('#' + containerId).html(`<div class="alert alert-${type}">${message}</div>`);
    }
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
            error: function () { showAlert('Failed to create Project!', 'danger', 'create-alert-container'); }
        });
    });
    $('#add-project').on('shown.bs.modal', function () {
        $('#createSubAssigneeSelect').select2({ placeholder: "Select sub-assignees", width: '100%', allowClear: true, closeOnSelect: false, dropdownParent: $('#add-project') });
        $('#CreateUserID').select2({ placeholder: "Select assignee", width: '100%', allowClear: true, dropdownParent: $('#add-project') });
        $.ajax({
            url: $('#create-form').attr('action'),
            type: 'GET',
            data: { action: 'get_all_users' },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#createSubAssigneeSelect').empty();
                    $('#CreateUserID').empty();
                    res.data.forEach(user => {
                        $('#createSubAssigneeSelect').append(`<option value="${user.id}">${user.full_name}</option>`);
                        $('#CreateUserID').append(`<option value="${user.id}">${user.full_name}</option>`);
                    });
                    $('#createSubAssigneeSelect, #CreateUserID').trigger('change');
                } else showAlert(res.message, 'danger', 'alert-container');
            },
            error: function () { showAlert('Failed to fetch users.', 'danger', 'alert-container'); }
        });
        $.ajax({
            url: $('#create-form').attr('action'),
            type: 'GET',
            data: { action: 'get_all_tags' },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#addTags').empty();
                    res.data.forEach(tag => $('#addTags').append(`<option value="${tag.id}">${tag.name}</option>`));
                    $('#addTags').trigger('change');
                } else showAlert(res.message, 'danger', 'alert-container');
            },
            error: function () { showAlert('Failed to fetch tags.', 'danger', 'alert-container'); }
        });
        $('#addTags').select2({ placeholder: "Tags", width: '100%', allowClear: true, closeOnSelect: false, dropdownParent: $('#add-project'), tags: true, createTag: function (params) { return { id: params.term, text: params.term, newTag: true }; } });
    });
    $('.edit-project-btn').on('click', function () {
        var projectId = $(this).data('id');
        $.ajax({
            url: $('#update-form').attr('action'),
            type: 'GET',
            data: { action: 'get_project', project_id: projectId },
            dataType: 'json',
            success: function (res) {
                if (!res.success) return showAlert(res.message, 'danger', 'edit-alert-container');
                $('#ProjectId').val(res.data.id);
                $('#UserID').val(res.data.user_id);
                $('#ProjectName').val(res.data.project_name);
                $('#ProjectStatus').val(res.data.status);
                $('#DescriptionUpdate').val(res.data.description);
                $('#ProjectTypeEdit').val(res.data.project_type);
                $('#addTagsEdit').empty();
                if (res.all_tags) res.all_tags.forEach(tag => $('#addTagsEdit').append(`<option value="${tag.id}">${tag.name}</option>`));
                if (res.data.tags) $('#addTagsEdit').val(res.data.tags);
                $('#addTagsEdit').select2({ placeholder: "Add tags", width: '100%', allowClear: true, closeOnSelect: false, dropdownParent: $('#edit-project-modal'), tags: true, createTag: function (params) { return { id: params.term, text: params.term, newTag: true }; } });
                $('#removeTagsEdit').empty();
                if (res.data.tags) res.data.tags.forEach(tagId => {
                    let tagName = res.all_tags.find(t => t.id == tagId)?.name || 'Unknown';
                    $('#removeTagsEdit').append(`<option value="${tagId}">${tagName}</option>`);
                });
                $('#removeTagsEdit').select2({ placeholder: "Remove tags", width: '100%', allowClear: true, closeOnSelect: false, dropdownParent: $('#edit-project-modal'), tags: false });
                $('#edit-project-modal').modal('show');
            },
            error: function () { showAlert('Failed to fetch project data.', 'danger', 'edit-alert-container'); }
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
    function initSubAssigneeModal(modalId, selectId, actionBtnId, fetchAction, submitAction, alertId) {
        $(modalId).on('shown.bs.modal', function () { $(selectId).select2({ placeholder: "Select users", width: '100%', dropdownParent: $(modalId), closeOnSelect: false, allowClear: true }); });
        $(modalId).on('hidden.bs.modal', function () { $(selectId).val(null).trigger('change'); $(modalId).find('form')[0].reset(); $('#' + alertId).html(''); });
        $(actionBtnId).on('click', function () {
            var projectId = $(modalId).data('project-id');
            var userIds = $(selectId).val();
            if (!userIds || userIds.length === 0) return showAlert('Select at least one user', 'danger', alertId);
            var formData = new FormData();
            formData.append('action', submitAction);
            formData.append('project_id', projectId);
            userIds.forEach(id => formData.append('user_id[]', id));
            $.ajax({
                url: $(modalId + ' form').attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (res) { showAlert(res.message, res.success ? 'primary' : 'danger', alertId); if (res.success) { $(modalId).modal('hide'); setTimeout(() => location.reload(), 1000); } }
            });
        });
    }
    initSubAssigneeModal('#add-sub-assignee-modal', '#multiSelect', '#add-sub-assignee', 'get_available_sub_assignees', 'add_sub_assignees', 'subassignee-alert-container');
    initSubAssigneeModal('#remove-sub-assignee-modal', '#removeMultiSelect', '#remove-sub-assignee', 'get_sub_assignees', 'remove_sub_assignee', 'remove-alert-container');
    $('.add-sub-assignee-btn, .remove-sub-assignee-btn').on('click', function () {
        var modal = $(this).hasClass('add-sub-assignee-btn') ? '#add-sub-assignee-modal' : '#remove-sub-assignee-modal';
        var projectId = $(this).data('id');
        $(modal).data('project-id', projectId);
        var selectId = modal === '#add-sub-assignee-modal' ? '#multiSelect' : '#removeMultiSelect';
        var alertId = modal === '#add-sub-assignee-modal' ? 'subassignee-alert-container' : 'remove-alert-container';
        $(selectId).empty();
        $.ajax({
            url: $(modal + ' form').attr('action'),
            type: 'GET',
            data: { action: modal === '#add-sub-assignee-modal' ? 'get_available_sub_assignees' : 'get_sub_assignees', project_id: projectId },
            dataType: 'json',
            success: function (res) {
                if (!res.success) return showAlert(res.message, 'danger', alertId);
                res.data.forEach(user => $(selectId).append(`<option value="${user.id}">${user.full_name}</option>`));
                $(selectId).trigger('change');
                $(modal).modal('show');
            }
        });
    });
});
