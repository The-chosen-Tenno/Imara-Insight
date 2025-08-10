<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/books.php';

$sm = AppManager::getSM();
$Book = new Book();
$data = $Book->getAll();



// if ($permission != 'admin') dd('Access Denied...!');

?>

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span>Books
            <!-- Button trigger modal -->
            <?php if ($permission == 'admin') { ?>
                <button
                    type="button"
                    class="btn btn-primary float-end"
                    data-bs-toggle="modal"
                    data-bs-target="#createBook">
                    Add Book
                </button>
            <?php } ?>
            <?php if (!isset($permission)) { ?>
                <a
                    href="../../index.php"
                    class="btn btn-primary float-end">
                    Log in to Explore More </a>
            <?php } ?>
        </h4>

        <div class="row m-3">
            <div class="col-6">
                <div class="d-flex align-items-center m-3">
                    <i class="bx bx-search fs-4 lh-0"></i>
                    <input type="text" id="searchInput" class="form-control border-0 shadow-none" placeholder="Search " aria-label="Search..." />
                </div>
            </div>
        </div>
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">Books</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <?php if ($permission == 'admin') { ?>
                                <th>ID</th>
                            <?php } ?>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <?php if ($permission == 'admin') { ?>
                                <th>Quantity</th>
                                <th>ISBN</th>
                            <?php } ?>
                            <?php if ($permission == 'member') { ?>
                                <th>Availablity</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php
                        foreach ($data as $key => $Book) {
                        ?>
                            <tr>
                                <?php if ($permission == 'admin') { ?>
                                    <td><?= $Book['BookID'] ?? '' ?></td>
                                <?php } ?>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $Book['Title'] ?? '' ?></strong></td>
                                <td><?= $Book['Author'] ?? '' ?></td>
                                <td><?= $Book['Category'] ?? '' ?></td>
                                <?php if ($permission == 'member') { ?>
                                    <td>
                                        <?php if (($Book['Quantity']) > 0): ?>
                                            <div>
                                                <span class="badge bg-success">Available</span>
                                            </div>
                                        <?php else: ?>
                                            <div>
                                                <span class="badge bg-warning">Not Available</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php } ?>
                                <?php if ($permission == 'admin') { ?>
                                    <td><?= $Book['Quantity'] ?? '' ?></td>
                                    <td><?= $Book['ISBN'] ?? '' ?></td>
                                <?php } ?>
                                <td>
                                    <?php if ($permission == 'admin') { ?>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">

                                                <a class="dropdown-item edit-book-btn" data-bs-toggle="modal" data-bs-target="#edit-book-modal" data-id="<?= $Book['BookID']; ?>"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                                <a class="dropdown-item delete-book-btn" data-id="<?= $Book['BookID']; ?>"><i class="bx bx-trash me-1"></i> Delete</a>

                                            </div>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Basic Bootstrap Table -->

        <hr class="my-5" />


    </div>

    <!-- / Content -->

    <!-- Create Modal -->
    <div class="modal fade" id="createBook" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="create-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Add New Book</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input
                            type="hidden"
                            name="action"
                            value="create_book">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameWithTitle" class="form-label">Title</label>
                                <input
                                    type="text"
                                    required
                                    id="nameWithTitle"
                                    name="Title"
                                    class="form-control"
                                    placeholder="Book's Title" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameWithTitle" class="form-label">Author Name</label>
                                <input
                                    required
                                    type="text"
                                    name="Author"
                                    id="Author"
                                    class="form-control"
                                    placeholder="Author of the Book" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="exampleFormControlSelect1" class="form-label">Category</label>
                                <select class="form-select" id="category" aria-label="Default select example" name="category" required>
                                    <option value="fiction">Fiction</option>
                                    <option value="non-fiction">Non-Fiction</option>
                                    <option value="science">Science</option>
                                    <option value="history">History</option>
                                    <option value="biography">Biography</option>
                                    <option value="fantasy">Fantasy</option>
                                    <option value="new-category">New Category</option>
                                    <option value="philosophy">Philosophy</option>
                                </select>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col mb-3">
                                <label for="emailWithTitle" class="form-label">Quantity</label>
                                <input
                                    required
                                    type="text"
                                    name="Quantity"
                                    id="Quantity"
                                    class="form-control"
                                    placeholder="Quantity of the books">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col mb-3">
                                <label for="emailWithTitle" class="form-label">ISBN</label>
                                <input
                                    required
                                    type="text"
                                    name="ISBN"
                                    id="ISBN"
                                    class="form-control"
                                    placeholder="Enter ISBN of the book" />
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <div id="additional-fields">
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <div id="alert-container"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-primary" id="create">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Udpate Modal -->
    <div class="modal fade" id="edit-book-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="update-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Update User</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input
                            type="hidden"
                            name="action"
                            value="update_book">
                        <input
                            type="hidden"
                            required
                            id="book_id"
                            name="BookID"
                            class="form-control" />
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameWithTitle" class="form-label">Title</label>
                                <input
                                    type="text"
                                    required
                                    id="title"
                                    name="Title"
                                    class="form-control"
                                    placeholder="Book's Title" />
                            </div>
                        </div>

                        <div class="row ">
                            <div class="col mb-3">
                                <label for="emailWithTitle" class="form-label">Author</label>
                                <input
                                    required
                                    type="text"
                                    name="Author"
                                    id="author"
                                    class="form-control"
                                    placeholder="Author of the Book" />
                            </div>
                        </div>
                        <div class="row ">
                            <div class="mb-3">
                                <label for="exampleFormControlSelect1" class="form-label">Category</label>
                                <select class="form-select" id="category" aria-label="Default select example" name="category" required>
                                    <option value="fiction">Fiction</option>
                                    <option value="non-fiction">Non-Fiction</option>
                                    <option value="science">Science</option>
                                    <option value="history">History</option>
                                    <option value="biography">Biography</option>
                                    <option value="fantasy">Fantasy</option>
                                    <option value="new-category">New Category</option>
                                    <option value="philosophy">Philosophy</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-password1">Quantity</label>
                                <div class="input-group">
                                    <input
                                        required
                                        type="text"
                                        name="Quantity"
                                        id="quantity"
                                        class="form-control"
                                        placeholder="Enter available books" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-password1">ISBN</label>
                                <div class="input-group">
                                    <input
                                        required
                                        type="text"
                                        name="ISBN"
                                        id="isbn"
                                        class="form-control"
                                        placeholder="Enter available books" />
                                </div>
                            </div>
                        </div>


                        <div class="mb-3 mt-3">
                            <div id="edit-additional-fields">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <div id="edit-alert-container"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-primary" id="update-book">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <?php
    require_once('../layouts/footer.php');
    ?>
    <script src="<?= asset('assets/forms-js/books.js') ?>"></script>
    <script>
        $(document).ready(function() {
            $("#searchInput").on("input", function() {
                var searchTerm = $(this).val().toLowerCase();

                // Loop through each row in the table body
                $("tbody tr").filter(function() {
                    // Toggle the visibility based on the search term
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
                });
            });

            // Initial setup for the date picker
            $('#datePicker').val(getFormattedDate(new Date()));

            // Function to format date as YYYY-MM-DD
            function getFormattedDate(date) {
                var year = date.getFullYear();
                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                var day = date.getDate().toString().padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Function to update table rows based on the selected date
            function filterAppointmentsByDate(selectedDate) {
                console.log("selectedDate Date:", selectedDate); // Log each appointment date for debugging


                // Loop through each row in the table body
                $('tbody tr').each(function() {
                    var appointmentDate = $(this).find('.appointment_date').text().trim();
                    $(this).toggle(appointmentDate === selectedDate);
                });
            }

            // Event handler for the "Filter" button
            $('#clear').on('click', function() {
                location.reload();
            });

            // Event handler for date picker change
            $('#datePicker').on('change', function() {

                var selectedDate = $(this).val();
                alert(selectedDate);
                filterAppointmentsByDate(selectedDate);
            });

        });
    </script>