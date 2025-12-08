$(document).ready(function () {
    let projectIds = [];
    const permission = $('#app').data('permission');
    if (permission == 'admin') {
        $('.dropzone').sortable({
            connectWith: '.dropzone',
            handle: '.drag-handle',
            placeholder: 'sortable-placeholder',
            forcePlaceholderSize: true,
            tolerance: 'pointer',
            update: function (event, ui) {
                let cardId = ui.item.data('id');
                var form = $('#update-form')[0];
                let newColumn = ui.item.closest('.column-container').attr('id');
                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: {
                        project_id: cardId,
                        new_status: newColumn,
                        action: 'project_status_update'
                    },
                    success: function (response) {
                        console.log('Updated project', cardId, 'to', newColumn);
                    },
                    error: function (err) {
                        console.error('Error updating project status:', err);
                    }
                });
            }
        }).disableSelection();
    }

    function showAlert(message, type = 'primary', containerId = 'alert-container') {
        $('#' + containerId).html(`<div class="alert alert-${type}">${message}</div>`);
    }

    $('#filter-button').on('click', function () {
        var panel = $('#filter-div');
        if (panel.hasClass('d-none')) {
            panel.removeClass('d-none').addClass('d-block');
        } else {
            panel.removeClass('d-block').addClass('d-none');
        }
    });

    $('.filter-select').select2({
        width: '100%',
        allowClear: true,
        closeOnSelect: false
    });

    $('.filter-input').on('change', function () {
        var assignee = $('#assignee-filter').val();
        var sub = $('#sub-assignee-filter').val();
        var status = $('#status-filter').val();
        var tags = $('#tags-filter').val();
        var created_at = $('#created_at_filter').val();
        var updated_at = $('#updated_at_filter').val();

        if (
            (!assignee || assignee.length === 0) &&
            (!sub || sub.length === 0) &&
            (!status || status.length === 0) &&
            (!tags || tags.length === 0) &&
            (!created_at || created_at === '') &&
            (!updated_at || updated_at === '')
        ) {
            $('#app').html($('#app').data('original') || '');
            return;
        }

        let projectIds = [];
        var form = $('#create-form')[0];

        $.ajax({
            url: $(form).attr('action'),
            type: 'GET',
            dataType: 'JSON',
            data: {
                action: 'filter_projects',
                assignee: assignee,
                sub: sub,
                status: status,
                tags: tags,
                created_at: created_at,
                updated_at: updated_at
            },
            success: function (response) {
                if (response.success) {
                    $.post('DraggableLogsTest.php', {
                        projectIds: JSON.stringify(response.data)
                    }, function (html) {
                        // $('#app').html(html);
                    });
                }
            }
        });
    });

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

    $('.edit-project-btn').on('click', function () {
        var projectId = $(this).data('id');

        $.ajax({
            url: $('#update-form').attr('action'),
            type: 'GET',
            data: {
                action: 'get_project',
                project_id: projectId
            },
            dataType: 'json',
            success: function (res) {
                if (!res.success) return showAlert(res.message, 'danger', 'edit-alert-container');

                $('#ProjectId').val(res.data.id);
                $('#UserID').val(res.data.user_id);
                $('#ProjectName').val(res.data.project_name);
                $('#ProjectStatus').val(res.data.status);
                $('#DescriptionUpdate').val(res.data.description);
                $('#ProjectTypeEdit').val(res.data.project_type);

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
                            $('#addTagsEdit').val(res.data.tags || []);
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

                $.ajax({
                    url: $('#update-form').attr('action'),
                    type: 'GET',
                    data: {
                        action: 'get_all_tags_to_remove',
                        project_id: projectId
                    },
                    dataType: 'json',
                    success: function (tagRes) {
                        $('#removeTagsEdit').empty();
                        if (tagRes.success && tagRes.data.length > 0) {
                            tagRes.data.forEach(tag => {
                                $('#removeTagsEdit').append(`<option value="${tag.id}">${tag.name}</option>`);
                            });
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

    $('.add-sub-assignee-btn').on('click', function () {
        var projectId = $(this).data('id');
        $('#add-sub-assignee-modal').data('project-id', projectId).modal('show');
        $('#multiSelect').select2({
            placeholder: "Select users",
            width: '100%',
            dropdownParent: $('#add-sub-assignee-modal'),
            closeOnSelect: false,
            allowClear: true
        });

        $.getJSON($('#add-sub-assignee-modal form').attr('action'), {
            action: 'get_available_sub_assignees',
            project_id: projectId
        }, function (res) {
            $('#multiSelect').empty();
            if (res.success) res.data.forEach(u => $('#multiSelect').append(`<option value="${u.id}">${u.full_name}</option>`));
            $('#multiSelect').trigger('change');
        });
    });

    $('#add-sub-assignee').on('click', function () {
        var projectId = $('#add-sub-assignee-modal').data('project-id');
        var users = $('#multiSelect').val();
        if (!users || users.length === 0) return showAlert('Select at least one user', 'danger', 'subassignee-alert-container');

        var fd = new FormData();
        fd.append('action', 'add_sub_assignees');
        fd.append('project_id', projectId);
        users.forEach(u => fd.append('user_id[]', u));

        $.ajax({
            url: $('#add-sub-assignee-modal form').attr('action'),
            type: 'POST',
            data: fd,
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

    $('.remove-sub-assignee-btn').on('click', function () {
        var projectId = $(this).data('id');
        $('#remove-sub-assignee-modal').data('project-id', projectId).modal('show');
        $('#removeMultiSelect').select2({
            placeholder: "Select users",
            width: '100%',
            dropdownParent: $('#remove-sub-assignee-modal'),
            closeOnSelect: false,
            allowClear: true
        });

        $.getJSON($('#remove-sub-assignee-modal form').attr('action'), {
            action: 'get_sub_assignees',
            project_id: projectId
        }, function (res) {
            $('#removeMultiSelect').empty();
            if (res.success) res.data.forEach(u => $('#removeMultiSelect').append(`<option value="${u.id}">${u.full_name}</option>`));
            $('#removeMultiSelect').trigger('change');
        });
    });

    $('#remove-sub-assignee').on('click', function () {
        var projectId = $('#remove-sub-assignee-modal').data('project-id');
        var users = $('#removeMultiSelect').val();
        if (!users || users.length === 0) return showAlert('Select at least one user', 'danger', 'remove-alert-container');

        var fd = new FormData();
        fd.append('action', 'remove_sub_assignee');
        fd.append('project_id', projectId);
        users.forEach(u => fd.append('user_id[]', u));

        $.ajax({
            url: $('#remove-sub-assignee-modal form').attr('action'),
            type: 'POST',
            data: fd,
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
});