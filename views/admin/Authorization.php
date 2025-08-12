<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Users.php';

$userDetails = new User();
$pending = $userDetails->getUserbyStatus();

?>

<style>
.user-photo {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid #0d6efd;
  background-color: #f8f9fa;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.card-title.custom-title {
  font-size: 1.3rem;
  line-height: 1.1;
}

.dl-custom dt {
  font-weight: 600;
}

.dl-custom dd {
  margin-bottom: 0.25rem;
  font-size: 0.9rem;
}
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-lg-12 mb-4">
            <?php foreach ($pending as $LD) { ?>
                <div class="card shadow-sm border-0 mb-3" style="width: 100%;">
                    <div class="row g-3 align-items-center">
                        <div class="col-sm-8">
                            <div class="card-body py-2 px-3">
                                <h2 class="card-title text-primary fw-bold mb-1 custom-title">
                                    <?= htmlspecialchars($LD['user_name']) ?>
                                </h2>
                                <dl class="row mb-2 dl-custom">
                                    <dt class="col-sm-2">Fullname:</dt>
                                    <dd class="col-sm-10"><?= htmlspecialchars($LD['full_name']) ?></dd>

                                    <dt class="col-sm-2">Email:</dt>
                                    <dd class="col-sm-10"><?= htmlspecialchars($LD['email']) ?></dd>
                                </dl>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-success d-flex align-items-center edit-user-btn" data-bs-toggle="modal" data-bs-target="#edit-user-modal" data-id="<?= htmlspecialchars($LD['id']) ?>">
                                        <i class='bx bx-check me-2'></i>Accept
                                    </button>
                                    <button class="btn btn-sm btn-danger d-flex align-items-center edit-user-btn" data-bs-toggle="modal" data-bs-target="#edit-user-modal" data-id="<?= htmlspecialchars($LD['id']) ?>">
                                        <i class='bx bx-x me-2'></i>Decline
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 text-center">
                            <div class="card-body p-0 d-flex justify-content-center align-items-center">
                                <div class="user-photo">
                                    <img src="<?= !empty($LD['photo']) ? url('uploads/' . $LD['photo']) : url('assets/img/illustrations/mine-strappen.png') ?>"
                                         alt="User Photo"
                                         style="width: 100%; height: 100%; object-fit: cover;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/users.js') ?>"></script>
