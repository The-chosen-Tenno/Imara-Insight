<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Logs.php';
include BASE_PATH . '/models/Users.php';

$projectLogs = new Logs();
$logsData = $projectLogs->getAll();
$userDetails = new User();
$UserData = $userDetails->getAll();

if (!isset($permission)) dd('Access Denied...!');
?>

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">


    <h4 class="fw-bold py-3 mb-4" id="borrowed-history"><span class="text-muted fw-light"> </span> Project Logs
        <?php if ($permission == 'admin') { ?>
            <button
                type="button"
                class="btn btn-primary float-end add"
                data-bs-toggle="modal"
                data-bs-target="#add-project">
                Add Project
            </button>
        <?php } ?>
        <!-- <?php if ($permission == 'member') { ?>
            <button
                class="btn btn-primary float-end view-history"
                data-id="<?= $username; ?>">
                View Your Borrowed History</button>
        <?php } ?> -->
    </h4>
    <!-- <div class="row m-3">
        <div class="col-6">
            <div class="d-flex align-items-center m-3">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input type="text" id="searchInput" class="form-control border-0 shadow-none" placeholder="Search" aria-label="Search..." />
            </div>
        </div>
    </div> -->



    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header"></h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <!-- <th>Portrait</th> -->
                        <th>Assigned To</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Last Update</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    $userNames = [];
                    foreach ($UserData as $user) {
                        $userNames[$user['id']] = $user['FullName'];
                    }
                    // $userPhotos = [];
                    // foreach ($UserData as $user) {
                    //     $userPhotos[$user['id']] = $user['photo'];
                    // }
                    foreach ($logsData as $LD) {
                    ?>
                        <tr>
                            <!-- <td><img src="../../assets/uploads/<?= htmlspecialchars(!empty($userPhotos[$LD['user_id']]) ? $userPhotos[$LD['user_id']] : 'default.png') ?>" alt="User Photo" width="40" height="40" style="border-radius:50%; object-fit:cover;"></td> -->
                            <td><?= $userNames[$LD['user_id']] ?? '' ?></td>
                            <td><?= htmlspecialchars($LD['project_name'] ?? '') ?></td>
                            <td>
                                <?php if ($LD['status'] == 'finished'): ?>
                                    <span class="badge bg-success"><?= $LD['status'] ?? '' ?></span>
                                <?php elseif ($LD['status'] == 'in_progress'): ?>
                                    <span class="badge bg-primary">In Progress</span>
                                <?php elseif ($LD['status'] == 'idle'): ?>
                                    <span class="badge bg-dark"> <?= $LD['status'] ?? '' ?></span>
                                <?php elseif ($LD['status'] == 'cancelled'): ?>
                                    <span class="badge bg-danger"> <?= $LD['status'] ?? '' ?></span>
                                <?php endif; ?>

                            </td>
                            <td><?= date('Y-m-d  H:i', strtotime($LD['last_updated'])) ?></td>
                            <!-- Dropdown  -->
                            <?php if ($permission == 'admin') { ?>
                                <td>

                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item edit-book-btn" data-bs-toggle="modal" data-bs-target="#edit-book-modal" data-id="<?= $BB['BorrowedBookID']; ?>"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                        </div>
                                    </div>
                                </td>
                            <?php } ?>
                        <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->

    <hr class="my-5" />


</div>

<!-- / Content -->

<!-- Modal -->
<div class="modal fade" id="add-project" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="create-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Add Project</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-password1">Project Name</label>
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    type="text"
                                    value=""
                                    placeholder="Enter the Project Name"
                                    id="projectName"
                                    name="projectName" />
                                <input
                                    type="hidden"
                                    name="action"
                                    value="create_project">
                            </div>
                        </div>
                        <div class="col form-password-toggle">
                            <label class="form-label" for="basic-default-password2">Assign To</label>
                            <div class="input-group">
                                <select class="form-select" id="CreateUserID" aria-label="Default select example" name="UserID" required>
                                    <?php
                                    foreach ($UserData as $FullName => $User) { ?>
                                        <option value="<?= $User['id'] ?>"><?= $User['FullName'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="alert-container"></div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="additional-fields">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary ms-2" id="create-project">Create</button>
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
                    <h5 class="modal-title" id="modalCenterTitle">Manage Borrowed Book</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-2">
                        <div class="col orm-password-toggle">
                            <label class="form-label" for="basic-default-password1">Book ID</label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    name="BookID"
                                    min="1" step="1"
                                    required
                                    class="form-control"
                                    id="BookID"
                                    placeholder="Book ID" />
                                <input
                                    type="hidden"
                                    name="action"
                                    value="update_borrowed_book">
                                <input
                                    type="hidden"
                                    name="BorrowedBookID"
                                    id="BorrowedBookID">
                            </div>
                        </div>
                        <div class="col form-password-toggle">
                            <label class="form-label" for="basic-default-password2">User ID</label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    name="UserID"
                                    min="1" step="1"
                                    required
                                    class="form-control"
                                    id="UserID"
                                    placeholder="User ID" />
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Borrow Date</label>
                            <input
                                class="form-control BorrowDate"
                                type="date"
                                value="2021-06-18"
                                id="BorrowDate"
                                name="BorrowDate" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Due Date</label>
                            <input
                                class="form-control"
                                type="date"
                                value="2021-06-18"
                                id="DueDate"
                                name="DueDate" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Return Date</label>
                            <input
                                class="form-control"
                                type="date"
                                id="ReturnDate"
                                name="ReturnDate" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Fine Status</label>
                            <select class="form-select" id="finestatus" aria-label="Default select example" name="FineStatus" required>
                                <option value="No Fine">No Fine</option>
                                <option value="Not Paid">Not Paid</option>
                                <option value="Paid">Paid</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="alert-container"></div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="additional-fields">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="update-borrowed-book">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/project.js') ?>"></script>

<!-- Script for Search -->
<!-- <script>
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
</script> -->
<!-- Script for Borrowed History -->
<!-- <script>
    $(document).ready(function() {
        $('.view-history').on('click', function() {

            var username = '<?= $username ?>';

            if ($('#searchInput').val() === username) {

                $('#searchInput').val('').trigger('input');
            } else {
                $('#searchInput').val(username).trigger('input');
            }

            isValid = false;
        });
    });
</script> -->