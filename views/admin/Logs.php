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
    <div class="row m-3">
        <div class="col-6">
            <div class="d-flex align-items-center m-3">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input type="text" id="searchInput" class="form-control border-0 shadow-none" placeholder="Search" aria-label="Search..." />
            </div>
        </div>
    </div>



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
                        $userNames[$user['id']] = $user['full_name'];
                    }
                    // $userPhotos = [];
                    // foreach ($UserData as $user) {
                    //     $userPhotos[$user['id']] = $user['photo'];
                    // }
                    foreach ($logsData as $LD) {
                    ?>
                        <tr>
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
                            <?php if ($permission == 'admin') { ?>
                                <td>

                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item edit-project-btn" data-bs-toggle="modal" data-bs-target="#edit-project-modal" data-id="<?= $LD['id']; ?>"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                        </div>
                                    </div>
                                </td>
                            <?php } ?>
                        <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <hr class="my-5" />


</div>
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
                                    foreach ($UserData as $full_name => $User) { ?>
                                        <option value="<?= $User['id'] ?>"><?= $User['full_name'] ?></option>
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
<div class="modal fade" id="edit-project-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="update-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Update Project</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-2 mb-3">
                        <div class="col form-password-toggle">
                            <label class="form-label">Project Name</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    name="ProjectName"
                                    required
                                    class="form-control"
                                    id="ProjectName"
                                    placeholder="Project Name" />
                                <input
                                    type="hidden"
                                    name="ProjectId"
                                    required
                                    id="ProjectId" />
                                <input
                                    type="hidden"
                                    name="UserID"
                                    required
                                    id="UserID" />
                                <input
                                    type="hidden"
                                    name="action"
                                    value="update_project" />
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Project Status</label>
                            <select class="form-select" id="ProjectStatus" aria-label="Default select example" name="ProjectStatus" required>
                                <option value="idle">Idle</option>
                                <option value="in_progress">In Progress</option>
                                <option value="finished">Finished</option>
                                <option value="cancelled">Cancelled</option>
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
                    <button type="button" class="btn btn-primary ms-2" id="update-project">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/project.js') ?>"></script>

<script>
    $(document).ready(function() {
        $("#searchInput").on("input", function() {
            var searchTerm = $(this).val().toLowerCase();
            $("tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
            });
        });
        $('#datePicker').val(getFormattedDate(new Date()));
        function getFormattedDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        function filterAppointmentsByDate(selectedDate) {
            console.log("selectedDate Date:", selectedDate);
            $('tbody tr').each(function() {
                var appointmentDate = $(this).find('.appointment_date').text().trim();
                $(this).toggle(appointmentDate === selectedDate);
            });
        }
        $('#clear').on('click', function() {
            location.reload();
        });
        $('#datePicker').on('change', function() {
            var selectedDate = $(this).val();
            alert(selectedDate);
            filterAppointmentsByDate(selectedDate);
        });
    });
</script>