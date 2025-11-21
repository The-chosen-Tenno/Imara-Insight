<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Leave.php';
include BASE_PATH . '/models/Users.php';

$leaveDetails = new Leave();
$leave = $leaveDetails->getLeavebyStatus();

$userDetails = new User();
$user_data = $userDetails->getAll();
if ($permission !== 'admin') {
    die('Access Denied...!');
}
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
                <?php
                $user_photo = [];
                foreach ($user_data as $photo) {
                    $user_photo[$photo['id']] = $photo['photo'];
                }
                ?>
                <?php foreach ($leave as $pending) : ?>
                    <div class="card shadow-sm border-0 mb-3" style="width: 100%;">
                        <div class="row g-3 align-items-center">
                            <div class="col-sm-8">
                                <div class="card-body py-2 px-3">
                                    <h2 class="card-title text-primary fw-bold mb-1 custom-title">
                                    </h2>
                                    <dl class="row mb-2 dl-custom">
                                        <dt class="col-sm-3">Full Name:</dt>
                                        <dd class="col-sm-9"><?= htmlspecialchars($full_name[$pending['user_id']]) ?></dd>

                                        <dt class="col-sm-3">Leave Duration:</dt>
                                        <dd class="col-sm-9">
                                            <?php
                                            $labels = [
                                                'half'  => 'Half Day',
                                                'full'  => 'Full Day',
                                                'multi' => 'Multiple Days',
                                                'short' => 'Short Day'
                                            ];
                                            echo $labels[$pending['leave_duration']] ?? 'Unknown';
                                            ?>
                                        </dd>

                                        <dt class="col-sm-3">Date Off:</dt>
                                        <dd class="col-sm-9">
                                            <?php if ($pending['leave_duration'] === 'multi') : ?>
                                                <?= '<strong>Start:</strong> ' . $pending['start_date'] . ' &nbsp; | &nbsp; <strong>End:</strong> ' . $pending['end_date'] ?>
                                            <?php elseif ($pending['leave_duration'] === 'half'): ?>
                                                <?= '<strong>' . ($pending['half_day'] === 'first' ? 'First Half' : 'Second Half') . '</strong>' ?>
                                            <?php else : ?>
                                                <?= '<strong>' . ($pending['date_off']) . '</strong>' ?>
                                            <?php endif; ?>
                                        </dd>

                                        <dt class="col-sm-3">Reason:</dt>
                                        <dd class="col-sm-9">
                                            <?= htmlspecialchars($pending['reason_type']) ?> -
                                            <?= htmlspecialchars($pending['leave_note']) ?>
                                        </dd>

                                        <dt class="col-sm-3">Description:</dt>
                                        <dd class="col-sm-9"><?= htmlspecialchars($pending['description']) ?></dd>
                                    </dl>
                                    <div class="d-flex gap-2">
                                        <button
                                            class="btn btn-sm btn-success d-flex align-items-center approve-leave-btn"
                                            data-id="<?= htmlspecialchars($pending['id']) ?>"
                                            data-user-id="<?= htmlspecialchars($pending['user_id']) ?>">
                                            <i class='bx bx-check me-2'></i>Approve
                                        </button>
                                        <button class="btn btn-sm btn-danger d-flex align-items-center deny-leave-btn"
                                            data-id="<?= htmlspecialchars($pending['id']) ?>"
                                            <i class='bx bx-x me-2'></i>Deny
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 text-center">
                                <div class="card-body p-0 d-flex justify-content-center align-items-center">
                                    <div class="user-photo">
                                        <img src="<?= !empty($user_photo[$pending['user_id']]) ? url($user_photo[$pending['user_id']]) : url('assets/img/illustrations/default-profile-picture.png') ?>"
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
<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/users.js') ?>"></script>
<script src="<?= asset('assets/forms-js/leave-request.js') ?>"></script>