<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Users.php';

$permission = $_SESSION['role'] ?? null;
$userId = $_SESSION['userId'] ?? null;

$userModel = new User();
$table = $userModel->getTableName();
$data = $userModel->getAll();
?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Employees</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle text-center projectTable">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th>Profile</th>
                        <th class="text-start ps-3">User Name</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>portfolio</th>
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
                            <td><strong><?= htmlspecialchars($user['UserName'] ?? $user['user_name'] ?? '') ?></strong></td>
                            <td><?= htmlspecialchars($user['FulltName'] ?? $user['full_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($user['Email'] ?? $user['email'] ?? '') ?></td>                            
                             <td>
                                <a href="../personalportfolio.php?id=<?= (int)$user['id'] ?>" class="btn btn-outline-info btn-sm rounded-pill" target="_blank">Show</a>
                            </td>
                            <td>
                                <?php if (($user['user_status'] ?? $user['status'] ?? '') === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                                <?php elseif (($user['user_status'] ?? $user['status'] ?? '') === 'inactive'): ?>
                                <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
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

<?php require_once('../layouts/footer.php'); ?>
<script src="<?= asset('assets/forms-js/users.js') ?>"></script>
