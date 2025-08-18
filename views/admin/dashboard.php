<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Logs.php';
include BASE_PATH . '/models/Users.php';

$projectLogs = new Logs();
$logsData = $projectLogs->getByUserId($userId);


$userDetails = new User();
$loginUserDetails = $userDetails->getById($userId);
$UserData = $userDetails->getAll();

if (!isset($permission) || ($permission !== 'user' && $permission !== 'admin')) {
    dd('Access Denied...');
}

?>
<!-- Content wrapper -->
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Project Logs -->
        <h4 class="fw-bold py-3 mb-4">
            My Projects
            <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#add-project">
                Add Project
            </button>
        </h4>

        <!-- Search -->
        <div class="row m-3">
            <div class="col-6">
                <div class="d-flex align-items-center m-3">
                    <i class="bx bx-search fs-4 lh-0"></i>
                    <input type="text" id="searchInput" class="form-control border-0 shadow-none" placeholder="Search" />
                </div>
            </div>
        </div>

        <!-- Projects Table -->
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Photos</th>
                            <th>Last Update</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logsData as $LD) { ?>
                            <tr>
                                <td><?= htmlspecialchars($LD['project_name'] ?? '') ?></td>
                                <td>
                                    <?php if ($LD['status'] == 'finished'): ?>
                                        <span class="badge bg-success"><?= $LD['status'] ?></span>
                                    <?php elseif ($LD['status'] == 'in_progress'): ?>
                                        <span class="badge bg-primary">In Progress</span>
                                    <?php elseif ($LD['status'] == 'idle'): ?>
                                        <span class="badge bg-dark"><?= $LD['status'] ?></span>
                                    <?php elseif ($LD['status'] == 'cancelled'): ?>
                                        <span class="badge bg-danger"><?= $LD['status'] ?></span>
                                    <?php endif; ?>
                                </td>
                                                            <td>
                                <a href="../ProjectDetails.php?id=<?= $LD['id']; ?>"
                                    class="btn rounded-pill btn-outline-primary"
                                    target="_blank">
                                    Show
                                </a>
                            </td>
                                <td><?= date('Y-m-d H:i', strtotime($LD['last_updated'])) ?></td>
                                <td>
                                    <a class=" edit-project-btn" data-bs-toggle="modal" data-bs-target="#edit-project-modal" data-id="<?= $LD['id']; ?>"><i class="bx bx-edit-alt me-1"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
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
                                    id="project_name"
                                    name="project_name" />
                                <input type="text" name="user_id" value="<?= $loginUserDetails['id'] ?>" hidden />
                                <input
                                    type="hidden"
                                    name="action"
                                    value="create_project">
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
                                    name="project_name"
                                    required
                                    class="form-control"
                                    id="ProjectName"
                                    placeholder="Project Name" />
                                <input
                                    type="hidden"
                                    name="project_id"
                                    required
                                    id="ProjectId" />
                                <input
                                    type="hidden"
                                    name="user_id"
                                    required
                                    id="UserID" />
                                <input
                                    type="hidden"
                                    name="action"
                                    value="update_project_user" />
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Project Status</label>
                            <select class="form-select" id="ProjectStatus" aria-label="Default select example" name="status" required>
                                <option value="idle">Idle</option>
                                <option value="in_progress">In Progress</option>
                                <option value="finished">Finished</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="projectImagesTitle" class="form-label">Project Image Title</label>
                            <input type="text" class="form-control" name="project_images_title[]" placeholder="Image title" />
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="projectImagesDescription" class="form-label">Project Images Description</label>
                                <input type="text" class="form-control" name="project_images_description[]" placeholder="Image description" />
                            </div>
                            <div class="row">
                                <div class="mb-3">
                                    <label for="projectImages" class="form-label">Project Images</label>
                                    <input type="file" class="form-control" name="project_images[]" accept="image/*" multiple />
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
<script src="<?= asset('assets/forms-js/logs.js') ?>"></script>
<script>
    $(document).ready(function() {
        $("#searchInput").on("input", function() {
            var searchTerm = $(this).val().toLowerCase();
            $("tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
            });
        });
    });
</script>