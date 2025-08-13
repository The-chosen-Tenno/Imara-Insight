<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Users.php';

$userModel = new User();
$table = $userModel->getTableName();
$data = $userModel->getAll();


if ($permission != 'admin') dd('Access Denied...!');
?>

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Users
        <!-- Button trigger modal -->
        <button
            type="button"
            class="btn btn-primary float-end"
            data-bs-toggle="modal"
            data-bs-target="#createUser">
            Add New User
        </button>
    </h4>
    <!-- Search Bar -->
    <div class="card mb-5">
        <div class="row m-3">
            <div class="col-6">
                <div class="d-flex align-items-center m-3">
                    <i class="bx bx-search fs-4 lh-0"></i>
                    <input type="text" id="searchInput" class="form-control border-0 shadow-none" placeholder="Search" aria-label="Search..." />
                </div>
            </div>
        </div>
    </div>
    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">Users</h5>
        <div class="m-4">
            <div id="delete-alert-container"></div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Role</th>


                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    foreach ($data as $key => $user) {
                    ?>
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $user['UserName'] ?? '' ?></strong></td>
                            <td><?= $user['FirstName'] ?? '' ?></td>
                            <td><?= $user['LastName'] ?? '' ?></td>
                            <td><?= $user['Email'] ?? '' ?></td>
                            <td>
                                <span class="text-capitalize"> <?= $user['Role'] ?? '' ?></span>
                            </td>
                            <td>
                            <?php if ($user['ID'] != $userId && $user['Role'] != 'admin') { ?>

                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">

                                            <a class="dropdown-item edit-user-btn" data-bs-toggle="modal" data-bs-target="#edit-user-modal" data-id="<?= $user['ID']; ?>"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                            <a class="dropdown-item delete-user-btn" data-id="<?= $user['ID']; ?>"><i class="bx bx-trash me-1"></i> Delete</a>

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

<!-- Udpate Modal -->
<div class="modal fade" id="edit-user-modal" tabindex="-1" aria-hidden="true">
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
                        value="update_user">
                    <input
                        type="hidden"
                        required
                        id="user_id"
                        name="ID"
                        class="form-control" />
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">User Name</label>
                            <input
                                type="text"
                                required
                                id="user_name"
                                name="UserName"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col mb-3">
                            <label for="emailWithTitle" class="form-label">Email</label>
                            <input
                                required
                                type="text"
                                name="Email"
                                id="email"
                                class="form-control"
                                placeholder="xxxx@xxx.xx" />
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
                    <button type="button" class="btn btn-primary" id="update-user">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/users.js') ?>"></script>
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