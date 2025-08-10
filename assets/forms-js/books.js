$(document).ready(function () {
    $('#create').on('click', function () {
        var form = $('#create-form')[0] || null;
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
                        $('#CreateBook').modal('hide');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function (error) {
                    // Handle the error
                    console.error('Error submitting the form:', error);
                    showAlert('Something went wrong..!', 'danger');
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
        var book_id = $(this).data('id');
        await getBookById(book_id);
    })

    $('#update-book').on('click', function () {

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

    async function getBookById(id) {
        var url = $('#update-form').attr('action');
        $('#edit-additional-fields').empty();

        // Perform AJAX request
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                book_id: id,
                action: 'get_book'
            }, // Form data
            dataType: 'json',
            success: function (response) {
                console.log(response);

                showAlert(response.message, response.success ? 'primary' : 'danger');
                if (response.success) {
                    var book_id = response.data.BookID;
                    var title = response.data.Title;
                    var author = response.data.Author;
                    var category = response.data.Category;
                    var quantity = response.data.Quantity;
                    var isbn = response.data.ISBN;


                    $('#edit-book-modal #book_id').val(book_id);
                    $('#edit-book-modal #title').val(title);
                    $('#edit-book-modal #author').val(author);
                    $('#edit-book-modal #category').val(category);
                    $('#edit-book-modal #quantity').val(quantity);
                    $('#edit-book-modal #isbn').val(isbn);


                    if (permission === 'doctor') {

                        const domain = $('meta[name="domain"]').attr('content'); // Fetch domain from <meta> tag
                        const doctorPhotoPath = domain + '/assets/uploads/';

                        $('#edit-additional-fields').html(
                            ' <input type="hidden" id="doctor_id" name="doctor_id" value="' + doctorId + '"></input>' +
                            '<div class="row mt-2">' +
                            '<div class="col-12 mb-3">' +
                            '<label for="name" class="form-label">Doctor Name</label>' +
                            '<input type="text" id="name" value="' + doctorName + '" name="doctor_name" class="form-control" placeholder="Enter Name" required />' +
                            '</div>' +
                            '<div class="col-12 mb-3">' +
                            '<label for="about" class="form-label">About Doctor</label>' +
                            '<textarea id="about" name="about_doctor" class="form-control" placeholder="Enter About" required>' + doctorAbout + '</textarea>' +
                            '</div>' +
                            '<div class="col-12 mb-3">' +
                            '<label for="formFile" class="form-label">Doctor Photo</label>' +
                            '<input class="form-control" name="image" id="image" type="file" accept="image/*">' +
                            '</div>' +
                            '<div class="col-12 mb-3">' +
                            '<label for="formFile" class="form-label">Doctor Photo</label>' +
                            // Display current photo if available, or default image
                            (doctorPhoto ?
                                '<img src="' + doctorPhotoPath + doctorPhoto + '" alt="user-avatar" class="d-block rounded m-3" width="80" id="uploadedAvatar">' :
                                '<img src="assets/img/avatars/1.png" alt="user-avatar" class="d-block rounded m-3" width="80" id="uploadedAvatar">'
                            ) +
                            // File input for updating photo
                            '<input class="form-control mt-2" name="image" id="image" type="file" accept="image/*">' +
                            '</div>' +

                            '</div>'
                        );
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

    $('.delete-book-btn').on('click', async function () {
        var book_id = $(this).data('id');
        var is_confirm = confirm('Are you sure,Do you want to delete?');
        if (is_confirm) await deleteById(book_id);
    })



    async function deleteById(id) {
        var url = $('#update-form').attr('action');

        // Perform AJAX request
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                user_id: id,
                action: 'delete_book',
            }, // Form data
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Show success message when deletion is successful (no reload)
                    showAlert(response.message, 'primary');
                } else {
                    setTimeout(function () {
                        location.reload();
                    }, 1000); ;
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