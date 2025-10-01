<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Logs.php';
include BASE_PATH . '/models/Users.php';
include BASE_PATH . '/models/Sub-Assignees.php';
include BASE_PATH . '/models/Tags.php';
include BASE_PATH . '/models/ProjectTags.php';

$project_logs = new Logs();
$logs_data = $project_logs->getAllByDesc();

$user_details = new User();
$user_data = $user_details->getAllActive();

$sub_assignee_details = new SubAssignee();

$project_tags = new ProjectTags();


$tag = new Tags();
$all_tag = $tag->getAllTags();


if (!isset($permission)) {
    header('Location: views/system/Authorization.php');
    exit;
};
?>
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Project Logs</h4>
            <?php if ($permission == 'admin') { ?>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add-project">
                    <i class="bx bx-plus me-1"></i> Add Project
                </button>
            <?php } ?>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle text-center projectTable">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th>Assigned To</th>
                        <th class="text-start ps-3">Project</th>
                        <th>Tags</th>
                        <th>Sub-Assignees</th>
                        <th>Status</th>
                        <th>Photos</th>
                        <th>Last Update</th>
                        <?php if ($permission == 'admin') { ?><th>Action</th><?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user_names = [];
                    foreach ($user_data as $user) {
                        $user_names[$user['id']] = $user['user_name'];
                    }

                    $tag_names = [];
                    foreach ($all_tag as $tags) {
                        $tag_names[$tags['id']] = $tags['name'];
                    }

                    foreach ($logs_data as $LD) {
                        $sub_assignee_data = $sub_assignee_details->getAllByProjectId($LD['id']);
                        $tag_for_project   = $project_tags->getAllTagByProjectId($LD['id']);
                    ?>
                        <tr>
                            <td>
                                <span class="badge bg-info d-block mb-1"><?= htmlspecialchars($user_names[$LD['user_id']] ?? 'Unknown') ?></span>
                            </td>
                            <td class="fw-semibold text-start ps-3"><?= htmlspecialchars($LD['project_name'] ?? '') ?></td>
                            <td class="text-start">
                                <?php if (!empty($tag_for_project)): ?>
                                    <?php foreach ($tag_for_project as $tag_id): ?>
                                        <span class="badge rounded-pill bg-label-primary me-1 mb-1"><?= htmlspecialchars($tag_names[$tag_id] ?? 'Unknown') ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">None</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-start">
                                <?php if (!empty($sub_assignee_data)): ?>
                                    <?php foreach ($sub_assignee_data as $sub_id): ?>
                                        <span class="badge rounded-pill bg-secondary me-1 mb-1"><?= htmlspecialchars($user_names[$sub_id] ?? 'Unknown') ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">None</span>
                                <?php endif; ?>
                            </td>
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
                                <span class="badge <?= $statusClass ?> text-capitalize px-3 py-2"><?= htmlspecialchars(str_replace('_', ' ', $LD['status'])) ?></span>
                            </td>
                            <td>
                                <a href="../ProjectDetails.php?id=<?= (int)$LD['id'] ?>" class="btn btn-outline-info btn-sm rounded-pill" target="_blank">Show</a>
                            </td>
                            <td class="text-muted"><?= date('Y-m-d H:i', strtotime($LD['last_updated'])) ?></td>
                            <?php if ($permission == 'admin') { ?>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="text-warning edit-project-btn"
                                            data-bs-toggle="modal" data-bs-target="#edit-project-modal"
                                            data-id="<?= (int)$LD['id'] ?>">
                                            <i class="bx bx-edit-alt"></i>
                                        </a>
                                        <a class="text-success add-sub-assignee-btn"
                                            data-bs-toggle="modal" data-bs-target="#add-sub-assignee-modal"
                                            data-id="<?= (int)$LD['id'] ?>">
                                            <i class="bx bx-user-plus"></i>
                                        </a>
                                        <a class="text-danger remove-sub-assignee-btn"
                                            data-bs-toggle="modal" data-bs-target="#remove-sub-assignee-modal"
                                            data-id="<?= (int)$LD['id'] ?>">
                                            <i class="bx bx-user-minus"></i>
                                        </a>
                                    </div>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
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
                            <label class="form-label" for="project_name">Project Name</label>
                            <div class="input-group">
                                <input class="form-control" type="text" placeholder="Enter the Project Name" id="project_name" name="project_name" required />
                                <input type="hidden" name="action" value="create_project">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"> Project description</label>
                            <textarea id="description" name="description" rows="4" class="form-control" placeholder="Provide details or additional info here..." required></textarea>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="CreateUserID">Assign To</label>
                            <div class="input-group">
                                <select class="form-select" id="CreateUserID" name="user_id" required>
                                    <?php foreach ($user_data as $full_name => $user) { ?>
                                        <option value="<?= $user['id'] ?>"><?= $user['full_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <select id="addTags" name="tags[]" multiple="multiple" style="width:100%;"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sub-assignees</label>
                            <select id="createSubAssigneeSelect" name="sub_assignees[]" multiple="multiple" style="width:100%;"></select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="ProjectType">Project Type</label>
                            <select class="form-select" id="ProjectType" name="type" required>
                                <option value="coding">Coding</option>
                                <option value="automation">Automation</option>
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
                    <div class="mb-3">
                        <label class="form-label"> Project description</label>
                        <textarea id="DescriptionUpdate" name="description" rows="4" class="form-control" placeholder="Provide details or additional info here..." required></textarea>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label">Project Status</label>
                            <select class="form-select" id="ProjectStatus" name="status" required>
                                <option value="idle">Idle</option>
                                <option value="in_progress">In Progress</option>
                                <option value="finished">Finished</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Add Tags</label>
                        <select id="addTagsEdit" name="tags_add[]" multiple="multiple" style="width:100%;"></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remove Tags</label>
                        <select id="removeTagsEdit" name="tags_remove[]" multiple="multiple" style="width:100%;"></select>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label" for="ProjectTypeEdit">Project Type</label>
                        <select class="form-select" id="ProjectTypeEdit" name="project_type" required>
                            <option value="coding">Coding</option>
                            <option value="automation">Automation</option>
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

<?php require_once('../layouts/footer.php'); ?>
<script src="<?= asset('assets/forms-js/logs.js') ?>"></script>