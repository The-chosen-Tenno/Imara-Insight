$(document).ready(function() {
    $('#reason_type').on('change', function() {
        if ($(this).val() === 'other') {
            $('#other_reason_div').show().find('input').attr('required', true);
        } else {
            $('#other_reason_div').hide().find('input').removeAttr('required');
        }
    });

    $('#leave-request-form').on('submit', function(e) {
        e.preventDefault();

        var form = this;
        var formData = new FormData(form);
        var url = $(form).attr('action');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                $('#leave-request-form').hide();
                $('#success-text').text(response.message);

                if (response.success) {
                    $('.text-success').show();
                    $('.text-failed').hide();
                } else {
                    $('.text-success').hide();
                    $('.text-failed').show();
                }

                $('#leave-success-message').show();
            },
            error: function(err) {
                console.error('AJAX error:', err);
                showAlert('Failed to submit leave request!', 'danger');
            }
        });
    });

    $('#request-again').on('click', function() {
        $('#leave-success-message').hide();
        $('#leave-request-form')[0].reset();
        $('#other_reason_div').hide().find('input').removeAttr('required');
        $('#leave-request-form').show();
    });
});