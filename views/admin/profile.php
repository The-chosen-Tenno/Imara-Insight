<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Users.php';

$userDetails = new User();
$loginUserDetails = $userDetails->getById($userId);

if (!isset($userId) && empty($userId)) dd('Access Denied...!');
?>
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm-8">
                        <div class="card-body">
                            <h2 class="card-title text-primary fw-bold mb-3">
                                <?= htmlspecialchars($loginUserDetails['user_name']) ?>
                            </h2>
                            <dl class="row mb-4">

                                <dt class="col-sm-3 fw-semibold">Fullname:</dt>
                                <dd class="col-sm-9"><?= htmlspecialchars($loginUserDetails['full_name']) ?></dd>

                                <dt class="col-sm-3 fw-semibold">User ID:</dt>
                                <dd class="col-sm-9"><?= htmlspecialchars($loginUserDetails['id']) ?></dd>

                                <dt class="col-sm-3 fw-semibold">Email:</dt>
                                <dd class="col-sm-9"><?= htmlspecialchars($loginUserDetails['email']) ?></dd>
                            </dl>
                            <button class="btn btn-sm btn-primary d-flex align-items-center edit-user-btn" data-bs-toggle="modal" data-bs-target="#edit-user-modal" data-id="<?= ($loginUserDetails['id']) ?>">
                                <i class='bx bx-cog me-2'></i> Edit My Profile
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-4 text-center">
                        <div class="card-body p-0">
                            <div class="mx-auto" style="
                    width: 180px;
                    height: 180px;
                    border-radius: 50%;
                    overflow: hidden;
                    border: 2px solid #0d6efd;
                    background: #f8f9fa;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">
                                <img src="<?= !empty($loginUserDetails['photo'])
                                                ? url($loginUserDetails['photo'])
                                                : url('assets/img/illustrations/default-profile-picture.png') ?>"
                                    style="
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        "
                                    alt="User Photo" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- model -->
<div class="modal fade" id="edit-user-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="update-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Update User</h5>
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
                        value="update_user">
                    <input
                        type="hidden"
                        required
                        id="user_id"
                        name="ID"
                        class="form-control" />
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">User Name</label>
                            <input
                                type="text"
                                required
                                id="user_name"
                                name="UserName"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col mb-3">
                            <label for="emailWithTitle" class="form-label">Email</label>
                            <input
                                required
                                type="text"
                                name="Email"
                                id="email"
                                class="form-control"
                                placeholder="xxxx@xxx.xx" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="profileImage" class="form-label">Profile Image</label>
                            <input
                                type="file"
                                name="Photo"
                                id="profileImage"
                                class="form-control" />
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="edit-additional-fields">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="edit-alert-container"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal"> Close</button>
                    <button type="button" class="btn btn-primary" id="update-user">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/users.js') ?>"></script>