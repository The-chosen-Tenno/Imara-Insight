<?php

require_once('../layouts/header.php');
include BASE_PATH . '/models/Users.php';

$userDetails = new User();
$users = $userDetails->getUserbyStatus();
if ($permission !== 'admin') {
    die('Access Denied...!');
}

?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-lg-12 mb-4">
            <?php if (!empty($users)) : ?>
                <?php foreach ($users as $pending) : ?>
                    <div class="card shadow-sm border-0 mb-3" style="width: 100%;">
                        <div class="row g-3 align-items-center">
                            <div class="col-sm-8">
                                <div class="card-body py-2 px-3">
                                    <h2 class="card-title text-primary fw-bold mb-1 custom-title">
                                        <?= htmlspecialchars($pending['user_name']) ?>
                                    </h2>
                                    <dl class="row mb-2 dl-custom">
                                        <dt class="col-sm-2">Fullname:</dt>
                                        <dd class="col-sm-10"><?= htmlspecialchars($pending['full_name']) ?></dd>

                                        <dt class="col-sm-2">Email:</dt>
                                        <dd class="col-sm-10"><?= htmlspecialchars($pending['email']) ?></dd>
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
                                        <img src="<?= !empty($pending['photo']) ? url($pending['photo']) : url('assets/img/illustrations/default-profile-picture.png') ?>"
                                            alt="User Photo"
                                            style="width: 100%; height: 100%; object-fit: cover;" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/auth.js') ?>"></script>
<script src="<?= asset('assets/forms-js/users.js') ?>"></script>
