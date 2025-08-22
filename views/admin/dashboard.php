<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Logs.php';
include BASE_PATH . '/models/Users.php';
include BASE_PATH . '/models/Sub-Assignees.php';

$projectLogs = new Logs();
$logsData = $projectLogs->getByUserId($userId);
$userDetails = new User();
$loginUserDetails = $userDetails->getById($userId);
$UserData = $userDetails->getAll();
$sub_assignee_details = new SubAssignee();
$subAssignedProjects = $sub_assignee_details->getByUserId($userId);



if (!isset($permission) || ($permission !== 'user' && $permission !== 'admin')) {
    dd('Access Denied...');
}
?>
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            My Projects
            <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#add-project">
                Add Project
            </button>
        </h4>
        <div class="card">
            <h5 class="card-header"></h5>
            <div class="table-responsive text-nowrap">
                <table class="table projectTable">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Sub-Assignee</th>
                            <th>Status</th>
                            <th>Photos</th>
                            <th>Last Update</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $user_names = [];
                        foreach ($UserData as $user) {
                            $user_names[$user['id']] = $user['full_name'];
                        }
                        foreach ($logsData as $LD):
                            $sub_assignee_data = $sub_assignee_details->getAllByProjectId($LD['id']);
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($LD['project_name'] ?? '') ?></td>
                                <td>
                                    <?php foreach ($sub_assignee_data as $sub_id): ?>
                                        <span class="badge bg-secondary d-block mb-1">
                                            <?= htmlspecialchars($user_names[$sub_id] ?? 'Unknown') ?>
                                        </span>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <?php if ($LD['status'] === 'finished'): ?>
                                        <span class="badge bg-success"><?= htmlspecialchars($LD['status']) ?></span>
                                    <?php elseif ($LD['status'] === 'in_progress'): ?>
                                        <span class="badge bg-primary">In Progress</span>
                                    <?php elseif ($LD['status'] === 'idle'): ?>
                                        <span class="badge bg-dark"><?= htmlspecialchars($LD['status']) ?></span>
                                    <?php elseif ($LD['status'] === 'cancelled'): ?>
                                        <span class="badge bg-danger"><?= htmlspecialchars($LD['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="../ProjectDetails.php?id=<?= (int) $LD['id']; ?>"
                                        class="btn rounded-pill btn-outline-primary" target="_blank">
                                        Show
                                    </a>
                                </td>
                                <td><?= date('Y-m-d H:i', strtotime($LD['last_updated'])) ?></td>
                                <td>
                                    <a class="edit-project-btn" data-bs-toggle="modal" data-bs-target="#edit-project-modal" data-id="<?= (int) $LD['id']; ?>">
                                        <i class="bx bx-edit-alt me-1"></i>
                                    </a>
                                    <a class="add-sub-assignee-btn" data-bs-toggle="modal" data-bs-target="#add-sub-assignee-modal" data-id="<?= (int) $LD['id']; ?>">
                                        <i class="bx bx-user-plus"></i>
                                    </a>
                                    <a class="remove-sub-assignee-btn" data-bs-toggle="modal" data-bs-target="#remove-sub-assignee-modal" data-id="<?= (int) $LD['id']; ?>">
                                        <i class="bx bx-user-minus"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Sub-Assigned Projects</h4>
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table projectTable">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Main Assignee</th>
                            <th>Status</th>
                            <th>Photos</th>
                            <th>Last Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($subAssignedProjects as $SAP):
                            $project = $projectLogs->getById($SAP['project_id']);
                            $sub_assignees = $sub_assignee_details->getAllByProjectId($project['id']);
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($project['project_name'] ?? '') ?></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= htmlspecialchars($user_names[$project['user_id']] ?? 'Unknown') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($project['status'] === 'finished'): ?>
                                        <span class="badge bg-success"><?= htmlspecialchars($project['status']) ?></span>
                                    <?php elseif ($project['status'] === 'in_progress'): ?>
                                        <span class="badge bg-primary">In Progress</span>
                                    <?php elseif ($project['status'] === 'idle'): ?>
                                        <span class="badge bg-dark"><?= htmlspecialchars($project['status']) ?></span>
                                    <?php elseif ($project['status'] === 'cancelled'): ?>
                                        <span class="badge bg-danger"><?= htmlspecialchars($project['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="../ProjectDetails.php?id=<?= (int)$project['id'] ?>" class="btn rounded-pill btn-outline-primary" target="_blank">
                                        Show
                                    </a>
                                </td>
                                <td><?= date('Y-m-d H:i', strtotime($project['last_updated'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- ADD PROJECT MODAL -->
<div class="modal fade" id="add-project" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="create-form" method="POST" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Project Name</label>
                        <input class="form-control" type="text" placeholder="Enter the Project Name" id="project_name" name="project_name" required />
                        <input type="hidden" name="user_id" value="<?= (int) $loginUserDetails['id'] ?>">
                        <input type="hidden" name="action" value="create_project">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Status</label>
                        <select class="form-select" id="ProjectStatusCreate" name="status" required>
                            <option value="idle">Idle</option>
                            <option value="in_progress">In Progress</option>
                            <option value="finished">Finished</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Image Title</label>
                        <input type="text" class="form-control" name="project_images_title[]" placeholder="Image title" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Images Description</label>
                        <input type="text" class="form-control" name="project_images_description[]" placeholder="Image description" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Project Images</label>
                        <input type="file" class="form-control" name="project_images[]" accept="image/*" multiple />
                    </div>

                    <div id="create-alert-container" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary ms-2" id="create-project">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT PROJECT MODAL -->
<div class="modal fade" id="edit-project-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="update-form" method="POST" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Update Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Project Name</label>
                        <input type="text" name="project_name" required class="form-control" id="ProjectName" placeholder="Project Name" />
                        <input type="hidden" name="project_id" id="ProjectId" required />
                        <input type="hidden" name="user_id" id="UserID" required />
                        <input type="hidden" name="action" value="update_project_user" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Status</label>
                        <select class="form-select" id="ProjectStatusUpdate" name="status" required>
                            <option value="idle">Idle</option>
                            <option value="in_progress">In Progress</option>
                            <option value="finished">Finished</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Image Title</label>
                        <input type="text" class="form-control" name="project_images_title[]" placeholder="Image title" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Images Description</label>
                        <input type="text" class="form-control" name="project_images_description[]" placeholder="Image description" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Images</label>
                        <input type="file" class="form-control" name="project_images[]" accept="image/*" multiple />
                    </div>

                    <div id="update-alert-container" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary ms-2" id="update-project">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD SUB-ASSIGNEE MODAL -->
<div class="modal fade" id="add-sub-assignee-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="add-sub-assignee-form" method="POST" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Sub-assignee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_sub_assignee">
                    <input type="hidden" name="project_id" id="AddSubAssigneeProjectId">

                    <div class="mb-3">
                        <label class="form-label">Project Members</label>
                        <select id="multiSelect" name="user_id[]" multiple="multiple" style="width:100%;"></select>
                    </div>

                    <div id="add-sub-alert-container" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary ms-2" id="add-sub-assignee">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- REMOVE SUB-ASSIGNEE MODAL -->
<div class="modal fade" id="remove-sub-assignee-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="remove-sub-assignee-form" method="POST" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Sub-assignee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="project_id" id="removeProjectId">
                    <input type="hidden" name="action" value="remove_sub_assignee">

                    <label class="form-label">Assigned Sub-assignees</label>
                    <select id="removeMultiSelect" name="user_id[]" multiple="multiple" style="width:100%;"></select>

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
<script src="<?= asset('assets/forms-js/dashboard.js') ?>"></script>