$(document).ready(function () {

    $('#create').on('click', function () {
        var form = $('#create-form')[0]; // Get the form element
        if (!form) {
            console.log('Something went wrong..');
            return;
        }

        var url = $('#create-form').attr('action');
        if (form.checkValidity()) { // Only submit if the form is valid
            var formData = new FormData(form); // Prepare form data

            $.ajax({
                url: url, // Target URL (ajax_functions.php)
                type: 'POST',
                data: formData,
                contentType: false, // Don't set content type
                processData: false, // Don't process the data
                dataType: 'json', // Expect JSON response
                success: function (response) {
                    showAlert(response.message, response.success ? 'primary' : 'danger');
                    if (response.success) {
                        $('#add-book').modal('hide'); // Hide the modal if successful
                        setTimeout(function () {
                            location.reload(); // Reload page after 1 second
                        }, 1000);
                    }
                },
                error: function (error) {
                    // Handle any errors in the request
                    console.error('Error submitting the form:', error);
                    showAlert('Something went wrong..!', 'danger');
                },
                complete: function (response) {
                    console.log('Request complete:', response); // Log the request completion
                }
            });
        } else {
            form.reportValidity(); // Show form validation errors if invalid
        }
    });


    $('.edit-user-btn').on('click', async function () {
        var user_id = $(this).data('id');
        await getUserById(user_id);
    })

    $('#update-user').on('click', function () {

        // Get the form element
        var form = $('#update-form')[0];
        form.reportValidity();

        // Check form validity
        if (form.checkValidity()) {
            // Serialize the form data
            var url = $('#update-form').attr('action');
            var formData = new FormData($('#update-form')[0]);

            // Perform AJAX request
            $.ajax({
                url: url,
                type: 'POST',
                data: formData, // Form data
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
                    // Handle the error
                    console.error('Error submitting the form:', error);
                },
                complete: function (response) {
                    // This will be executed regardless of success or error
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

        // Perform AJAX request
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                user_id: id,
                action: 'get_user_by_id'
            }, // Form data
            dataType: 'json',
            success: function (response) {
                console.log(response);

                showAlert(response.message, response.success ? 'primary' : 'danger');
                if (response.success) {
                    var user_id = response.data.id;
                    var username = response.data.user_name;
                    var email = response.data.email;



                    $('#edit-user-modal #user_id').val(user_id);
                    $('#edit-user-modal #user_name').val(username);
                    $('#edit-user-modal #email').val(email);
                    if (role === 'admin') {
                    } else {
                        $('#edit-additional-fields').empty();
                    }
                    $('#edit-user-modal').modal('show');
                }
            },
            error: function (error) {
                // Handle the error
                console.error('Error submitting the form:', error);
            },
            complete: function (response) {
                // This will be executed regardless of success or error
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

        // Perform AJAX request
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                user_id: user_id,
                action: 'delete_user',
            }, // Form data
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
                // Handle the error
                console.error('Error submitting the form:', error);
            },
            complete: function (response) {
                // This will be executed regardless of success or error
                console.log('Request complete:', response);
            }
        });
    }
});