<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Leave.php';
include BASE_PATH . '/models/Users.php';

$leaveDetails = new Leave();
$leave = $leaveDetails->getLeavebyStatus();

$userDetails = new User();
$user_data = $userDetails->getAll();
?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-lg-12 mb-4">
            <?php if (!empty($leave)) : ?>
                <?php
                $full_name = [];
                foreach ($user_data as $user) {
                    $full_name[$user['id']] = $user['full_name'];
                }
                ?>
                <?php foreach ($leave as $pending) : ?>
                    <div class="card shadow-sm border-0 mb-3" style="width: 100%;">
                        <div class="row g-3 align-items-center">
                            <div class="col-sm-8">
                                <div class="card-body py-2 px-3">
                                    <h2 class="card-title text-primary fw-bold mb-1 custom-title">
                                       <?= htmlspecialchars($full_name[$pending['user_id']]) ?>
                                    </h2>
                                    <dl class="row mb-2 dl-custom">
                                        <dt class="col-sm-2">Fullname:</dt>
                                        <dd class="col-sm-10"><?= htmlspecialchars($pending['user_id']) ?></dd>

                                        <dt class="col-sm-2">Date Off:</dt>
                                        <dd class="col-sm-10"><?= htmlspecialchars($pending['date_off']) ?></dd>

                                        <dt class="col-sm-2">Reason:</dt>
                                        <dd class="col-sm-10"><?= htmlspecialchars($pending['reason_type']) ?></dd>

                                        <dt class="col-sm-2">Description:</dt>
                                        <dd class="col-sm-10"><?= htmlspecialchars($pending['description']) ?></dd>
                                    </dl>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-success d-flex align-items-center accept-user-btn" data-id="<?= htmlspecialchars($pending['id']) ?>">
                                            <i class='bx bx-check me-2'></i>Accept
                                        </button>
                                        <button class="btn btn-sm btn-danger d-flex align-items-center decline-user-btn" data-id="<?= htmlspecialchars($pending['id']) ?>">
                                            <i class='bx bx-x me-2'></i>Decline
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 text-center">
                                <div class="card-body p-0 d-flex justify-content-center align-items-center">
                                    <div class="user-photo">
                                        <img src="<?= !empty($pending['photo']) ? url($pending['photo']) : url('assets/img/illustrations/mine-strappen.png') ?>"
                                            alt="User Photo"
                                            style="width: 100%; height: 100%; object-fit: cover;" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach  ?>
                
            <?php else : ?>
                <div class="alert alert-success d-flex flex-column align-items-center justify-content-center text-center mx-auto"
                    style="width: 320px; height: 150px; border-radius: 8px; padding: 1.5rem;">
                    <div style="font-weight: 600; font-size: 1.2rem; margin-bottom: 0.5rem;">You’re all caught up.</div>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        width="48" height="48" fill="none" stroke="#198754" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-check-circle" aria-hidden="true" focusable="false">
                        <circle cx="24" cy="24" r="22" />
                        <path d="M16 24l6 6 12-12" />
                    </svg>
                </div>
            <?php endif; ?>
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
                        value="create_user">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">User Name</label>
                            <input
                                type="text"
                                required
                                id="nameWithTitle"
                                name="UserName"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">First Name</label>
                            <input
                                type="text"
                                required
                                id="nameWithTitle"
                                name="FirstName"
                                class="form-control"
                                placeholder="First Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Last Name</label>
                            <input
                                type="text"
                                required
                                id="nameWithTitle"
                                name="LastName"
                                class="form-control"
                                placeholder="last Name" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="emailWithTitle" class="form-label">Email</label>
                            <input
                                required
                                type="text"
                                name="Email"
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
                                    name="Password"
                                    class="form-control"
                                    id="passwordInput"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="basic-default-password1" />
                                <span id="basic-default-password1" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                        <div class="col form-password-toggle">
                            <label class="form-label" for="basic-default-password2">Confirm Password</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    required
                                    name="confirm_password"
                                    class="form-control"
                                    id="confirmPasswordInput"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="basic-default-password2" />
                                <span id="basic-default-password2" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Role</label>
                            <select class="form-select" id="permission" aria-label="Default select example" name="Role" required>
                                <option value="member">Member</option>
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
<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/users.js') ?>"></script>
<script src="<?= asset('assets/forms-js/auth.js') ?>"></script>