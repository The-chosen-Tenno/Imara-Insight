<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Logs.php';
include BASE_PATH . '/models/Users.php';
include BASE_PATH . '/models/Sub-Assignees.php';

$project_logs = new Logs();
$logs_data = $project_logs->getAll();
$user_details = new User();
$user_data = $user_details->getAll();
$sub_assignee_details = new SubAssignee();

if (!isset($permission)) dd('Access Denied...!');
?>
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"> </span> Project Logs
        <?php if ($permission == 'admin') { ?>
            <button type="button" class="btn btn-primary float-end add" data-bs-toggle="modal" data-bs-target="#add-project">
                Add Project
            </button>
        <?php } ?>
    </h4>
    <div class="card">
        <h5 class="card-header"></h5>
        <div class="table-responsive text-nowrap">
            <table class="table projectTable">
                <thead>
                    <tr>
                        <th>Assigned To</th>
                        <th>Project</th>
                        <th>Sub-Assignees</th>
                        <th>Status</th>
                        <th>Photos</th>
                        <th>Last Update</th>
                        <?php if ($permission == 'admin') { ?>
                            <th>Actions</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    $user_names = [];
                    foreach ($user_data as $user) {
                        $user_names[$user['id']] = $user['full_name'];
                    }
                    foreach ($logs_data as $LD) {
                        $sub_assignee_data = $sub_assignee_details->getAllByProjectId($LD['id']);
                    ?>
                        <tr>
                            <td>
                                <span class="badge text-bg-info d-block mb-1">
                                    <?= $user_names[$LD['user_id']] ?? '' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($LD['project_name'] ?? '') ?>
                            </td>
                            <td>
                                    <?php foreach ($sub_assignee_data as $sub_id): ?>
                                        <span class="badge bg-secondary d-block mb-1">
                                            <?= htmlspecialchars($user_names[$sub_id] ?? 'Unknown') ?>
                                        </span>
                                    <?php endforeach; ?>
                            </td>
                            <td>
                                <?php if ($LD['status'] == 'finished'): ?>
                                    <span class="badge bg-success"><?= $LD['status'] ?? '' ?></span>
                                <?php elseif ($LD['status'] == 'in_progress'): ?>
                                    <span class="badge bg-primary">In Progress</span>
                                <?php elseif ($LD['status'] == 'idle'): ?>
                                    <span class="badge bg-dark"><?= $LD['status'] ?? '' ?></span>
                                <?php elseif ($LD['status'] == 'cancelled'): ?>
                                    <span class="badge bg-danger"><?= $LD['status'] ?? '' ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="../ProjectDetails.php?id=<?= $LD['id']; ?>" class="btn rounded-pill btn-outline-primary" target="_blank">
                                    Show
                                </a>
                            </td>
                            <td><?= date('Y-m-d H:i', strtotime($LD['last_updated'])) ?></td>
                            <?php if ($permission == 'admin') { ?>
                                <td>
                                    <a class="edit-project-btn" data-bs-toggle="modal" data-bs-target="#edit-project-modal" data-id="<?= $LD['id']; ?>">
                                        <i class="bx bx-edit-alt me-1"></i>
                                    </a>
                                    <a class="add-sub-assignee-btn" data-bs-toggle="modal" data-bs-target="#add-sub-assignee-modal" data-id="<?= $LD['id']; ?>">
                                        <i class="bx bx-user-plus"></i>
                                    </a>
                                    <a class="remove-sub-assignee-btn" data-bs-toggle="modal" data-bs-target="#remove-sub-assignee-modal" data-id="<?= $LD['id']; ?>">
                                        <i class="bx bx-user-minus"></i>
                                    </a>
                                </td>
                            <?php } ?>
                        </tr>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-password1">Project Name</label>
                            <div class="input-group">
                                <input class="form-control" type="text" value="" placeholder="Enter the Project Name" id="project_name" name="project_name" />
                                <input type="hidden" name="action" value="create_project">
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label" for="basic-default-password2">Assign To</label>
                            <div class="input-group">
                                <select class="form-select" id="CreateUserID" aria-label="Default select example" name="user_id" required>
                                    <?php foreach ($user_data as $full_name => $user) { ?>
                                        <option value="<?= $user['id'] ?>"><?= $user['full_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="alert-container"></div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="additional-fields"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-2 mb-3">
                        <div class="col form-password-toggle">
                            <label class="form-label">Project Name</label>
                            <div class="input-group">
                                <input type="text" name="project_name" required class="form-control" id="ProjectName" placeholder="Project Name" />
                                <input type="hidden" name="project_id" required id="ProjectId" />
                                <input type="hidden" name="user_id" required id="UserID" />
                                <input type="hidden" name="action" value="update_project" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Project Status</label>
                            <select class="form-select" id="ProjectStatus" name="status" required>
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
                        <div id="additional-fields"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary ms-2" id="update-project">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="add-sub-assignee-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="add-sub-assignee-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Add Sub-assignee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Project Members</label>
                        <select id="multiSelect" name="user_id[]" multiple="multiple" style="width:100%;">
                        </select>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="alert-container"></div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="additional-fields"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary ms-2" id="add-sub-assignee">add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="remove-sub-assignee-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="remove-sub-assignee-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Sub-assignee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="project_id" id="removeProjectId">
                    <label class="form-label">Assigned Sub-assignees</label>
                    <select id="removeMultiSelect" name="user_id[]" multiple="multiple" style="width:100%;">
                        <!-- Options filled by JS -->
                    </select>
                    <input type="hidden" name="action" value="remove_sub_assignee">
                    <div id="remove-alert-container" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="remove-sub-assignee">Remove</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once('../layouts/footer.php'); ?>
<script src="<?= asset('assets/forms-js/logs.js') ?>"></script>