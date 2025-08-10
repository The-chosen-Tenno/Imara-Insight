
$(document).ready(function () {

    $('#create-project').on('click', function () {
        var form = $('#create-form')[0] ?? null;
        if (!form) console.log('Something went wrong..');

        var url = $('#create-form').attr('action');
        if (form.checkValidity() && form.reportValidity()) {
            var formData = new FormData(form);
            // Perform AJAX request
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false, // Don't set content type
                processData: false, // Don't process the data
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
                    // Handle the error
                    console.error('Error submitting the form:', error);
                    showAlert('Failed to create Project!', 'danger');
                },
                complete: function (response) {
                    // This will be executed regardless of success or error
                    console.log('Request complete:', response);

                }
            });


        } else {
            showAlert('Form is not valid. Please check your inputs.', 'danger');
        }
    });

    $('.edit-book-btn').on('click', async function () {
        var BorrowedBookID = $(this).data('id');
        await getBorrowedBookById(BorrowedBookID);
    })

    $('.delete-book-btn').on('click', async function () {
        var book_id = $(this).data('id');
        var permission = $(this).data('permission');
        var is_confirm = confirm('Are you sure,Do you want to delete?');
        if (is_confirm) await deleteById(user_id, permission);
    })

    $('.add-book-btn').on('click', async function () {
        await getBooksAndMember();
    })

    $('#update-borrowed-book').on('click', function () {

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
                        $('#edit-book-modal').modal('hide');
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


async function getBorrowedBookById(id) {
    var url = $('#update-form').attr('action');
    $('#edit-additional-fields').empty();

    // Perform AJAX request
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            BorrowedBookID: id,
            action: 'get_borrowed_book'
        }, // Form data
        dataType: 'json',
        success: function (response) {
            console.log(response);

            showAlert(response.message, response.success ? 'primary' : 'danger');
            if (response.success) {
                var BorrowedBookID = response.data.BorrowedBookID;
                var BookID = response.data.BookID;
                var UserID = response.data.UserID;
                var BorrowDate = response.data.BorrowDate;
                var DueDate = response.data.DueDate;
                var ReturnDate = response.data.ReturnDate;
                var finestatus = response.data.FineStatus;
   

                $('#edit-book-modal #BorrowedBookID').val(BorrowedBookID);
                $('#edit-book-modal #BookID').val(BookID);
                $('#edit-book-modal #UserID').val(UserID);
                $('#edit-book-modal #BorrowDate').val(BorrowDate);
                $('#edit-book-modal #DueDate').val(DueDate);
                $('#edit-book-modal #ReturnDate').val(ReturnDate);
                $('#edit-book-modal #finestatus').val(finestatus);

            }
                 else {
                    $('#edit-additional-fields').empty();
                }
                $('#edit-user-modal').modal('show');
           
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

async function getBooksAndMember() {
    var url = $('#update-form').attr('action');
    $('#edit-additional-fields').empty();

    // Perform AJAX request
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            BorrowedBookID: id,
            action: 'get_borrowed_book'
        }, // Form data
        dataType: 'json',
        success: function (response) {
            console.log(response);

            showAlert(response.message, response.success ? 'primary' : 'danger');
            if (response.success) {
                var BorrowedBookID = response.data.BorrowedBookID;
                var BookID = response.data.BookID;
                var UserID = response.data.UserID;
                var BorrowDate = response.data.BorrowDate;
                var DueDate = response.data.DueDate;
                var ReturnDate = response.data.ReturnDate;
                var finestatus = response.data.FineStatus;
   

                $('#edit-book-modal #BorrowedBookID').val(BorrowedBookID);
                $('#edit-book-modal #BookID').val(BookID);
                $('#edit-book-modal #UserID').val(UserID);
                $('#edit-book-modal #BorrowDate').val(BorrowDate);
                $('#edit-book-modal #DueDate').val(DueDate);
                $('#edit-book-modal #ReturnDate').val(ReturnDate);
                $('#edit-book-modal #finestatus').val(finestatus);

            }
                 else {
                    $('#edit-additional-fields').empty();
                }
                $('#edit-user-modal').modal('show');
           
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


async function deleteById(id, permission) {
    var url = $('#update-form').attr('action');

    // Perform AJAX request
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            user_id: id,
            action: 'delete_user',
            permission: permission
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
