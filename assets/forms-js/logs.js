$(document).ready(function () {
    $("#searchInput").on("input", function () {
        var term = $(this).val().toLowerCase();
        $("tbody tr").each(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(term) > -1);
        });
    });

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
                showAlert('Failed to create Project!', 'danger', 'create-alert-container');
            }
        });
    });

    $('.edit-project-btn').on('click', async function () {
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
                    $('#ProjectStatus').val(res.data.status);
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

    $('.add-sub-assignee-btn').on('click', function () {
        var projectId = $(this).data('id');
        $('#add-sub-assignee-modal').data('project-id', projectId).modal('show');
    });

    $('#add-sub-assignee').on('click', function () {
        var projectId = $('#add-sub-assignee-modal').data('project-id');
        var selectedUsers = $('#multiSelect').val();
        if (!selectedUsers || selectedUsers.length === 0) return;

        var formData = new FormData();
        formData.append('project_id', projectId);
        selectedUsers.forEach(u => formData.append('user_id[]', u));
        formData.append('action', 'add_sub_assignees');

        $.ajax({
            url: $('#update-form').attr('action'),
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
                alert('Something went wrong while updating sub-assignees.');
            }
        });
    });
});
