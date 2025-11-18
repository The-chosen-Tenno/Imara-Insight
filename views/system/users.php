<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Users.php';
include BASE_PATH . '/models/Logs.php';

$permission = $_SESSION['role'] ?? null;
$userId = $_SESSION['userId'] ?? null;

$userModel = new User();
$users = $userModel->getUserbyStatus();
$table = $userModel->getTableName();
$data = $userModel->getAll();

$project_details = new Logs();

?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
                <h4 class="fw-bold py-3 mb-4" id="borrowed-history"><span class="text-muted fw-light"> </span>
            <?php if ($permission == 'admin') { ?>
                <button
                    type="button"
                    class="btn btn-primary float-end mb-5"
                    data-bs-toggle="modal"
                    data-bs-target="#createUser"
                    style="<?= $styleMap['imara-button-purple'] ?>">
                   <i class="bx bx-plus me-1"></i>  Add New User
                </button>
            <?php } ?>
        </h4>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Employees</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle text-center projectTable">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th>Profile</th>
                        <!-- <th class="text-start ps-3">User Name</th> -->
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Profile</th>
                        <th>Project Status</th>
                        <th>Status</th>
                        <?php if ($permission == 'admin') { ?><th>Change Status</th><?php } ?>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">

                    <?php foreach ($data as $user): ?>
                        <?php
                        $userIdFromDB = $user['ID'] ?? $user['id'] ?? null;
                        $userRole = $user['Role'] ?? $user['role'] ?? '';
                        ?>
                        <tr>
                            <td><img src="<?= !empty($user['photo']) ? url($user['photo'])
                                                : url('assets/img/illustrations/default-profile-picture.png') ?>" alt="Profile" style="width:50px;height:50px;border-radius:50%;"></td>
                            <!-- <td><strong><?= htmlspecialchars($user['UserName'] ?? $user['user_name'] ?? '') ?></strong></td> -->
                            <td><?= htmlspecialchars($user['FulltName'] ?? $user['full_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($user['Email'] ?? $user['email'] ?? '') ?></td>
                            <td>
                                <a href="profile.php?id=<?= (int)$user['id'] ?>" class="btn btn-outline-info btn-sm rounded-pill">Show</a>
                            </td>
                            <td>
                                <?php
                                $projects = $project_details->getByAllProjectId($user['id']) ?? [];
                                $inProgressCount = $idleCount = 0;

                                foreach ($projects as $project) {
                                    $status = is_array($project) ? $project['status'] : $project->status;
                                    $status = strtolower(trim($status));
                                    if ($status === 'in_progress') $inProgressCount++;
                                    elseif ($status === 'idle') $idleCount++;
                                }

                                $badge = match (true) {
                                    $inProgressCount === 1 => '<span class="badge bg-info">In Project</span>',
                                    $inProgressCount > 1 => '<span class="badge bg-primary">Multiple Projects</span>',
                                    $idleCount >= 1 => '<span class="badge bg-dark">Idle</span>',
                                    !empty($projects) => '<span class="badge bg-success">Free</span>',
                                    default => '<span class="badge bg-secondary">Unassigned</span>',
                                };
                                echo $badge;
                                ?>
                            </td>
                            </td>
                            <td>
                                <?php
                                $userStatus = strtolower($user['user_status'] ?? $user['status'] ?? '');
                                echo match ($userStatus) {
                                    'active' => '<span class="badge bg-success">Active</span>',
                                    'inactive' => '<span class="badge bg-danger">Inactive</span>',
                                    default => ''
                                };
                                ?>
                            </td>
                            <?php if ($permission == 'admin') { ?>
                                <td>
                                    <button class="btn btn-sm btn-warning change-status-btn"
                                        data-id="<?= $user['id'] ?>"
                                        data-status="<?= $user['user_status'] ?>">Change
                                    </button>

                                </td>

                            <?php } ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-user_status-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="update-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <input type="hidden" id="UserID" name="user_id">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">User Status</label>
                            <select class="form-select" id="user_Status" name="user_status" required>
                                <option value="active">active</option>
                                <option value="inactive">inactive</option>
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
                    <button type="button" class="btn btn-primary ms-2" id="update_user_status">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- add user model -->
<div class="modal fade" id="createUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="create-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Add New User</h5>
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
                        value="admin_create_user">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input
                                type="text"
                                required
                                id="fullName"
                                name="full_name"
                                class="form-control"
                                placeholder="Enter your Full Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="userName" class="form-label">User Name</label>
                            <input
                                type="text"
                                required
                                id="userName"
                                name="user_name"
                                class="form-control"
                                placeholder="Enter A User Name" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="emailWithTitle" class="form-label">Email</label>
                            <input
                                required
                                type="text"
                                name="email"
                                id="emailWithTitle"
                                class="form-control"
                                placeholder="xxxx@xxx.xx" />
                        </div>
                    </div>
                    <div class="row gy-2">
                        <div class="col orm-password-toggle">
                            <label class="form-label" for="basic-default-password1">Password</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    required
                                    name="password"
                                    class="form-control"
                                    id="passwordInput"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="basic-default-password1" />
                                <span id="basic-default-password1" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Role</label>
                            <select class="form-select" id="permission" aria-label="Default select example" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
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

<?php require_once('../layouts/footer.php'); ?>
<script src="<?= asset('assets/forms-js/users.js') ?>"></script>