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
<link rel="stylesheet" href="../../assets/css/kanban-style-logs.css">

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Project Logs</h4>
            <div>

                <div class="search-container">
                    <input type="text" id="search" placeholder="Search">
                    <button id="clear-btn">&times;</button>
                </div>
                <?php if ($permission == 'admin') { ?>
                    <button type="button" class="btn btn-primary btn-sm fw-bold"
                        data-bs-toggle="modal"
                        data-bs-target="#add-project"
                        style="<?= $styleMap['imara-button-purple'] ?>">
                        <i class="bx bx-plus me-1"></i>Add Project
                    </button>
                <?php } ?>
                <button type="button" class="btn btn-primary btn-sm fw-bold"
                    id="filter-button"
                    style="<?= $styleMap['imara-button-yellow'] ?>">
                    <i class="bx bx-filter me-1"></i>Filter
                </button>
            </div>
        </div>
        <div class="mb-3" id="filter-div">
            <div class="row g-2 align-items-end">

                <div class="col-md-2">
                    <label for="assignee-filter" class="form-label">Assignee</label>
                    <select name="assignee" id="assignee-filter" class="form-select filter-input filter-select">
                        <option value="">-- None --</option>
                        <?php foreach ($user_data as $user_filter): ?>
                            <option value="<?= $user_filter['id'] ?>"><?= $user_filter['user_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="sub-assignee-filter" class="form-label">Sub Assignees</label>
                    <select name="sub-assignee[]" id="sub-assignee-filter" class="form-select filter-input filter-select" multiple>
                        <?php foreach ($user_data as $user_filter): ?>
                            <option value="<?= $user_filter['id'] ?>"><?= $user_filter['user_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="staus-filter" class="form-label">Status</label>
                    <select name="Status" id="status-filter" class="form-select filter-input">
                        <option value="">-- None --</option>
                        <option value="started">Started</option>
                        <option value="in_progress">In Progress</option>
                        <option value="finished">Finished</option>
                        <option value="idle">Idle</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="tags-filter" class="form-label">Tags</label>
                    <select name="tags[]" id="tags-filter" class="form-select filter-input filter-select" multiple>
                        <?php foreach ($all_tag as $tags_filter): ?>
                            <option value="<?= $tags_filter['id'] ?>"><?= $tags_filter['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="created_at_filter" class="form-label">Created At</label>
                    <input type="date" name="created_at" id="created_at_filter" class="form-control filter-input">
                </div>

                <div class="col-md-2">
                    <label for="updated_at_filter" class="form-label">Updated At</label>
                    <input type="date" name="updated_at" id="updated_at_filter" class="form-control filter-input">
                </div>

            </div>
        </div>
        <div class="columns-wrapper" data-permission="<?= $permission ?>" id="app">
            <?php
            $columns = [
                'started' => ['label' => 'Started', 'color' => '#22f0f0', 'count' => 0],
                'in_progress' => ['label' => 'In Progress', 'color' => '#0d6efd', 'count' => 0],
                'finished' => ['label' => 'Finished', 'color' => '#198754', 'count' => 0],
                'idle' => ['label' => 'Idle', 'color' => '#ffca2c', 'count' => 0],
                'cancelled' => ['label' => 'Cancelled', 'color' => '#dc3545', 'count' => 0]
            ];
            // // Decode filtered project IDs from POST
            // $filteredProjectIds = $_POST['projectIds'] ?? null;
            // if ($filteredProjectIds) $filteredProjectIds = json_decode($filteredProjectIds, true);

            foreach ($columns as $col_id => $col):
            ?>
                <div id="<?= $col_id ?>" class="column-container ">
                    <div class="column-header" style="background: <?= $col['color'] ?>;">
                        <?= $col['label'] ?>
                        <!-- <span class="badge rounded bg-light text-dark ms-2"> <?= $col['count'] ?></span> -->
                    </div>
                    <div class="dropzone border shadow bg-light bg- p-2" ;">
                        <!-- <div id="myDiv">
                            <?php
                            $test = $_POST['projectIds'] ?? '';
                            $decoded = json_decode($test, true);
                            ?>
                        </div> -->
                        <?php
                        foreach ($logs_data as $LD) {
                            if ($LD['status'] === $col_id) {
                                // if ($filteredProjectIds && !in_array($LD['id'], $filteredProjectIds)) continue;

                                $user_names = [];
                                foreach ($user_data as $user) {
                                    $user_names[$user['id']] = $user['user_name'];
                                }

                                $sub_assignee_id = $sub_assignee_details->getAllByProjectId($LD['id']);
                                $sub_assignee_list = [];
                                foreach ($sub_assignee_id as $sub_id) {
                                    if (isset($user_names[$sub_id])) $sub_assignee_list[] = $user_names[$sub_id];
                                }

                                $tag_ids = $project_tags->getAllTagByProjectId($LD['id']);
                                $tag_names = [];
                                foreach ($tag_ids as $tag_id) {
                                    foreach ($all_tag as $tag) {
                                        if ($tag['id'] == $tag_id) {
                                            $tag_names[] = $tag['name'];
                                            break;
                                        }
                                    }
                                }

                                $sub_assignee_list = implode(', ', $sub_assignee_list);
                                $tags_list = '';
                                foreach ($tag_names as $tag) {
                                    $tags_list .= '<span class="badge">' . htmlspecialchars($tag) . '</span>';
                                }

                                echo <<<BOX
                                <div class="box" data-id="{$LD['id']}">
                                    <div class="drag-handle">{$LD['project_name']}</div>
                                    <div class="text-dark">
                                        <div><strong>Assignee:</strong> {$user_names[$LD['user_id']]}</div>
                                        <div><strong>Sub-Assignees:</strong> {$sub_assignee_list}</div>
                                        <div><strong>Tags:</strong> {$tags_list}</div>
                                    </div>
                                </div>
                                BOX;
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
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
<script src="<?= asset('assets/forms-js/DraggableLogsTest.js') ?>"></script>
<script>
    $(document).ready(function() {
        function filterLogs() {
            var searchTerm = $('#search').val().toLowerCase();
            $('.column-container .box').each(function() {
                var projectName = $(this).find('.drag-handle').text().toLowerCase();
                var assignee = $(this).find('div strong:contains("Assignee:")').parent().text().toLowerCase();
                var subAssignees = $(this).find('div strong:contains("Sub-Assignees:")').parent().text().toLowerCase();
                var tags = $(this).find('.badge').text().toLowerCase();

                if (projectName.includes(searchTerm) || assignee.includes(searchTerm) || subAssignees.includes(searchTerm) || tags.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
        $('#search').on('keyup', function() {
            filterLogs();
            $('#clear-btn').toggle($(this).val().length > 0);
        });
        $('#clear-btn').on('click', function() {
            $('#search').val('');
            filterLogs();
            $(this).hide();
        });
    });
</script>