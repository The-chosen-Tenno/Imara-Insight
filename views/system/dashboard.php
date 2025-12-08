<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Logs.php';
include BASE_PATH . '/models/Users.php';
include BASE_PATH . '/models/Sub-Assignees.php';
include BASE_PATH . '/models/Tags.php';
include BASE_PATH . '/models/ProjectTags.php';
include BASE_PATH . '/models/Leave.php';

// Fetch data
$projectLogs = new Logs();
$logs_data = $projectLogs->getByUserId($userId);

$userDetails = new User();
$loginUserDetails = $userDetails->getById($userId);
$user_data = $userDetails->getAll();

$sub_assignee_details = new SubAssignee();
$subAssignedProjects = $sub_assignee_details->getByUserId($userId);

$leave_details = new Leave();
$AllLeave = $leave_details->getAllLeaveByUserId($userId);

// print_r($AllLeave);

$project_tags = new ProjectTags();
$tag = new Tags();
$all_tag = $tag->getAllTags();

// Permission check
if (!isset($permission) || ($permission !== 'user' && $permission !== 'admin')) {
    dd('Access Denied...');
}

// Prepare lookup arrays
$user_names = [];
foreach ($user_data as $user) {
    $user_names[$user['id']] = $user['user_name'];
}

$tag_names = [];
foreach ($all_tag as $tags) {
    $tag_names[$tags['id']] = $tags['name'];
}
?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">My Projects</h4>
            <div>
                <button type="button" class="btn  btn-sm me-2" data-bs-toggle="modal" data-bs-target="#add-project"
                    style="<?= $styleMap['imara-button-purple'] ?>">
                    <i class="bx bx-plus me-1"></i> Add Project
                </button>
                <div class="btn-group">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#add-short-leave">
                        <i class="bx bx-hourglass me-1"></i>Short Leave
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle text-center projectTable">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th class="text-start ps-3">Project</th>
                        <th>Tags</th>
                        <th>Sub-Assignees</th>
                        <th>Status</th>
                        <th>Photos</th>
                        <th>Last Update</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs_data as $LD):
                        $sub_assignee_data = $sub_assignee_details->getAllByProjectId($LD['id']);
                        $tag_for_project = $project_tags->getAllTagByProjectId($LD['id']);
                    ?>
                        <tr>
                            <td class="fw-semibold text-start ps-3"><?= htmlspecialchars($LD['project_name'] ?? '') ?></td>

                            <!-- Tags -->
                            <td class="text-start">
                                <?php if (!empty($tag_for_project)): ?>
                                    <?php foreach ($tag_for_project as $tag_id): ?>
                                        <span class="badge rounded-pill bg-label-primary me-1 mb-1">
                                            <?= htmlspecialchars($tag_names[$tag_id] ?? 'Unknown') ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">None</span>
                                <?php endif; ?>
                            </td>

                            <!-- Sub-Assignees -->
                            <td class="text-start">
                                <?php if (!empty($sub_assignee_data)):
                                    foreach ($sub_assignee_data as $sub_id): ?>
                                        <span class="badge rounded-pill bg-secondary me-1 mb-1">
                                            <?= htmlspecialchars($user_names[$sub_id] ?? 'Unknown') ?>
                                        </span>
                                    <?php endforeach;
                                else: ?>
                                    <span class="text-muted">None</span>
                                <?php endif; ?>
                            </td>

                            <!-- Status -->
                            <td>
                                <?php
                                $statusClass = match ($LD['status']) {
                                    'finished' => 'bg-success',
                                    'in_progress' => 'bg-primary',
                                    'idle' => 'bg-dark text-white',
                                    'cancelled' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $statusClass ?> text-capitalize px-3 py-2">
                                    <?= htmlspecialchars(str_replace('_', ' ', $LD['status'])) ?>
                                </span>
                            </td>

                            <!-- Photos -->
                            <td>
                                <a href="../ProjectDetails.php?id=<?= (int)$LD['id'] ?>" class="btn btn-outline-info btn-sm rounded-pill" target="_blank">Show</a>
                            </td>

                            <!-- Last Update -->
                            <td class="text-muted"><?= date('Y-m-d H:i', strtotime($LD['last_updated'])) ?></td>

                            <!-- Action -->
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="text-warning me-1 edit-project-btn" data-bs-toggle="modal" data-bs-target="#edit-project-modal" data-id="<?= (int)$LD['id'] ?>"><i class="bx bx-edit-alt"></i></a>
                                    <a class="text-success me-1 add-sub-assignee-btn" data-bs-toggle="modal" data-bs-target="#add-sub-assignee-modal" data-id="<?= (int)$LD['id'] ?>"><i class="bx bx-user-plus"></i></a>
                                    <a class="text-danger remove-sub-assignee-btn" data-bs-toggle="modal" data-bs-target="#remove-sub-assignee-modal" data-id="<?= (int)$LD['id'] ?>"><i class="bx bx-user-minus"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sub-Assigned Projects -->
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Sub-Assigned Projects</h4>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle text-center projectTable">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th class="text-start ps-3">Project</th>
                        <th>Tags</th>
                        <th>Other Sub-Assignees</th>
                        <th>Main Assignee</th>
                        <th>Status</th>
                        <th>Photos</th>
                        <th>Last Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subAssignedProjects as $SAP):
                        $project = $projectLogs->getById($SAP['project_id']);
                        $sub_assignees = $sub_assignee_details->getAllByProjectId($project['id']);
                        $tag_for_project = $project_tags->getAllTagByProjectId($project['id']);
                    ?>
                        <tr>
                            <td class="fw-semibold text-start ps-3"><?= htmlspecialchars($project['project_name'] ?? '') ?></td>

                            <!-- Tags -->
                            <td class="text-start">
                                <?php if (!empty($tag_for_project)): ?>
                                    <?php foreach ($tag_for_project as $tag_id): ?>
                                        <span class="badge rounded-pill bg-label-primary me-1 mb-1">
                                            <?= htmlspecialchars($tag_names[$tag_id] ?? 'Unknown') ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">None</span>
                                <?php endif; ?>
                            </td>

                            <!-- Other Sub-Assignees -->
                            <td class="text-start">
                                <?php
                                $otherSubs = array_filter($sub_assignees, fn($id) => $id != $project['user_id']);
                                if (!empty($otherSubs)):
                                    foreach ($otherSubs as $sub_id): ?>
                                        <span class="badge rounded-pill bg-secondary me-1 mb-1">
                                            <?= htmlspecialchars($user_names[$sub_id] ?? 'Unknown') ?>
                                        </span>
                                    <?php endforeach;
                                else: ?>
                                    <span class="text-muted">None</span>
                                <?php endif; ?>
                            </td>

                            <!-- Main Assignee -->
                            <td>
                                <span class="badge bg-info d-block mb-1">
                                    <?= htmlspecialchars($user_names[$project['user_id']] ?? 'Unknown') ?>
                                </span>
                            </td>

                            <!-- Status -->
                            <td>
                                <?php
                                $statusClass = match ($project['status']) {
                                    'finished' => 'bg-success',
                                    'in_progress' => 'bg-primary',
                                    'idle' => 'bg-dark text-white',
                                    'cancelled' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $statusClass ?> text-capitalize px-3 py-2">
                                    <?= htmlspecialchars(str_replace('_', ' ', $project['status'])) ?>
                                </span>
                            </td>

                            <!-- Photos -->
                            <td>
                                <a href="../ProjectDetails.php?id=<?= (int)$project['id'] ?>" class="btn btn-outline-info btn-sm rounded-pill" target="_blank">Show</a>
                            </td>

                            <!-- Last Update -->
                            <td class="text-muted"><?= date('Y-m-d H:i', strtotime($project['last_updated'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Leave Requests  -->
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">My Leave Requests</h4>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle text-center projectTable">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th class="text-start ps-3">Date</th>
                        <th>Leave Type</th>
                        <th>Duration</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Approval</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($AllLeave as $total_leave):
                    ?>
                        <tr>
                            <td class="ps-4 fw-medium text-primary">
                                <?= htmlspecialchars(date('Y-m-d', strtotime($total_leave['uploaded_at'] ?? ''))) ?>
                            </td>
                            <td class="text-start">
                                <div class="text-capitalize d-inline-flex align-items-center px-3 py-1 rounded-3 badge rounded-pill bg-label-dark text-dark fw-medium">
                                    <?= htmlspecialchars($total_leave['reason_type']) ?>
                                </div>
                            </td>

                            <td class="text-start">
                                <div class="text-capitalize d-inline-flex align-items-center px-3 py-1 rounded-3 badge rounded-pill bg-label-secondary text-secondary fw-medium">
                                    <?= htmlspecialchars($total_leave['leave_duration']) ?> Day
                                </div>
                            </td>
                            <td class="ps-4 fw-medium text-primary">
                                <?= htmlspecialchars(date('Y-m-d', strtotime($total_leave['date_off'] ?: $total_leave['start_date'] ?? ''))) ?>

                            </td>
                            <td class="ps-4 fw-medium text-primary">
                                <?= !empty($total_leave['end_date']) ? htmlspecialchars(date('Y-m-d', strtotime($total_leave['end_date']))) : '' ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = match ($total_leave['status']) {
                                    'approved' => 'bg-success',
                                    'pending' => 'bg-dark text-white',
                                    'denied' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $statusClass ?> text-capitalize px-3 py-2">
                                    <?= htmlspecialchars(str_replace('_', ' ', $total_leave['status'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ADD PROJECT MODAL -->
<div class="modal fade" id="add-project" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="create-form" method="POST" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Project Name</label>
                        <input class="form-control" type="text" name="project_name" required placeholder="Enter the Project Name" />
                        <input type="hidden" name="user_id" value="<?= (int)$loginUserDetails['id'] ?>">
                        <input type="hidden" name="action" value="create_project">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Description</label>
                        <textarea class="form-control" name="description" rows="4" required placeholder="Provide details or additional info here..."></textarea>
                    </div>

                    <!-- Tags -->
                    <div class="mb-3">
                        <label class="form-label">Tags</label>
                        <select id="addTags" name="tags[]" multiple="multiple" style="width:100%;">
                            <?php foreach ($all_tag as $tagItem): ?>
                                <option value="<?= $tagItem['id'] ?>"><?= htmlspecialchars($tagItem['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sub-Assignees -->
                    <div class="mb-3">
                        <label class="form-label">Sub-assignees</label>
                        <select id="createSubAssigneeSelect" name="sub_assignees[]" multiple="multiple" style="width:100%;"></select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Type</label>
                        <select class="form-select" name="type" required>
                            <option value="coding">Coding</option>
                            <option value="automation">Automation</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Status</label>
                        <select class="form-select" name="status" required>
                            <option value="idle">Idle</option>
                            <option value="in_progress">In Progress</option>
                            <option value="finished">Finished</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- IMAGE UPLOAD -->
                    <div class="mb-3">
                        <label class="form-label">Upload Project Images</label>
                        <input type="file" class="form-control" name="project_images[]" accept="image/*" multiple id="project-images" />
                    </div>
                    <div id="image-descriptions-container" class="mb-3"></div>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="update-form" method="POST" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Update Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Project Name</label>
                        <input type="text" name="project_name" id="ProjectName" class="form-control" required placeholder="Project Name" />
                        <input type="hidden" name="project_id" id="ProjectId" required />
                        <input type="hidden" name="user_id" id="UserID" required />
                        <input type="hidden" name="action" value="update_project_user">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Description</label>
                        <textarea class="form-control" name="description" id="DescriptionUpdate" rows="4" required placeholder="Provide details or additional info here..."></textarea>
                    </div>

                    <!-- Tags Add/Remove -->
                    <div class="mb-3">
                        <label class="form-label">Add Tags</label>
                        <select id="addTagsEdit" name="tags_add[]" multiple="multiple" style="width:100%;">
                            <?php foreach ($all_tag as $tagItem): ?>
                                <option value="<?= $tagItem['id'] ?>"><?= htmlspecialchars($tagItem['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remove Tags</label>
                        <select id="removeTagsEdit" name="tags_remove[]" multiple="multiple" style="width:100%;">
                            <?php foreach ($all_tag as $tagItem): ?>
                                <option value="<?= $tagItem['id'] ?>"><?= htmlspecialchars($tagItem['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project Status</label>
                        <select class="form-select" name="status" id="ProjectStatusUpdate" required>
                            <option value="idle">Idle</option>
                            <option value="in_progress">In Progress</option>
                            <option value="finished">Finished</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- IMAGE UPLOAD -->
                    <div class="mb-3">
                        <label class="form-label">Upload Project Images</label>
                        <input type="file" class="form-control" name="project_images[]" accept="image/*" multiple id="edit-project-images" />
                    </div>
                    <div id="edit-image-descriptions-container" class="mb-3"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary ms-2" id="update-project">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD SUB ASSIGNEE MODAL -->
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
                        <select id="multiSelect" name="user_id[]" multiple="multiple" style="width:100%;"></select>
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

<!-- REMOVE SUB ASSIGNEE MODAL -->
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
                    <select id="removeMultiSelect" name="user_id[]" multiple="multiple" style="width:100%;"></select>
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

<!-- ADD SHORT LEAVE MODAL -->
<div class="modal fade" id="add-short-leave" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="short-leave-form" action="<?= url('services/ajax_functions.php') ?>" method="POST">
                <input type="hidden" name="user_id" value=<?= $userId ?>>
                <input type="hidden" name="action" value="short_leave">

                <div class="modal-header">
                    <h5 class="modal-title">Confirm Short Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p id="leave-time"><br></p>

                    <div class="mb-3">
                        <label for="leave_duration" class="form-label">Duration (hours)</label>
                        <input type="number" class="form-control" id="leave_duration" name="duration" min="1" max="2" required>
                    </div>

                    <div class="mb-3">
                        <label for="leave_reason" class="form-label">Reason (optional)</label>
                        <textarea class="form-control" id="reason" name="reason" rows="2" placeholder="Optional reason"></textarea>
                    </div>
                    <div class="mb-3 mt-3" id="short-alert-container"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning ms-2" id="confirmed-short-leave">Confirm Leave</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php require_once('../layouts/footer.php'); ?>
<script src="<?= asset('assets/forms-js/dashboard.js') ?>"></script>