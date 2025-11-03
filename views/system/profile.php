<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Users.php';
include BASE_PATH . '/models/Logs.php';

$userDetails = new User();
$profileUserId = $_GET['id'] ?? $userId;
$loginUserDetails = $userDetails->getUserById($profileUserId);

$project = new Logs();
$ProjectDetails = $project->getByUserId($profileUserId);


if (!isset($userId) && empty($userId)) dd('Access Denied...!');
?>
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-4">

            <!-- Profile Card -->
            <div class="col-12">
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

                                    <!-- <dt class="col-sm-3 fw-semibold">User ID:</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($loginUserDetails['id']) ?></dd> -->

                                    <dt class="col-sm-3 fw-semibold">Email:</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($loginUserDetails['email']) ?></dd>
                                </dl>
                                <?php
                                if ((isset($_GET['id']) && $_GET['id'] == $userId) || (!isset($_GET['id']) && isset($userId))):
                                ?>
                                    <button class="btn btn-sm btn-primary d-flex align-items-center edit-user-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#edit-user-modal"
                                        data-id="<?= $loginUserDetails['id'] ?>">
                                        <i class='bx bx-cog me-2'></i> Edit My Profile
                                    </button>
                                <?php endif; ?>
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
                                    <img src="<?= !empty($loginUserDetails['photo']) ? url($loginUserDetails['photo']) : url('assets/img/illustrations/default-profile-picture.png') ?>"
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                        alt="User Photo" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <?php
            $counts = [
                'finished' => 0,
                'in_progress' => 0,
                'cancelled' => 0,
                'idle' => 0
            ];

            foreach ($ProjectDetails as $project) {
                $status = strtolower($project['status']);
                if (isset($counts[$status])) {
                    $counts[$status]++;
                }
            }
            $totalProjects = array_sum($counts);
            ?>
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="card-title text-primary fw-bold mb-3">
                            Project Summary
                        </h4>
                        <div class="row text-center g-3 justify-content-center">
                            <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded shadow-sm">
                                    <h6 class="text-muted mb-1">
                                        <i class="bi bi-kanban-fill"></i> Total Projects
                                    </h6>
                                    <h5 class="fw-bold display-6 mb-0"><?= $totalProjects ?></h5>
                                </div>
                            </div>

                            <div class="col-6 col-md">
                                <div class="p-3 bg-success bg-opacity-10 rounded shadow-sm">
                                    <h6 class="text-success mb-1">
                                        <i class="bi bi-hourglass-split"></i> In Progress
                                    </h6>
                                    <h5 class="fw-bold text-success display-6 mb-0"><?= $counts['in_progress'] ?></h5>
                                </div>
                            </div>

                            <div class="col-6 col-md">
                                <div class="p-3 bg-secondary bg-opacity-10 rounded shadow-sm">
                                    <h6 class="text-secondary mb-1">
                                        <i class="bi bi-pause-circle-fill"></i> Idle
                                    </h6>
                                    <h5 class="fw-bold text-secondary display-6 mb-0"><?= $counts['idle'] ?></h5>
                                </div>
                            </div>

                            <div class="col-6 col-md">
                                <div class="p-3 bg-info bg-opacity-10 rounded shadow-sm">
                                    <h6 class="text-info mb-1">
                                        <i class="bi bi-check-circle-fill"></i> Completed
                                    </h6>
                                    <h5 class="fw-bold text-info display-6 mb-0"><?= $counts['finished'] ?></h5>
                                </div>
                            </div>

                            <div class="col-6 col-md">
                                <div class="p-3 bg-danger bg-opacity-10 rounded shadow-sm">
                                    <h6 class="text-danger mb-1">
                                        <i class="bi bi-x-circle-fill"></i> Cancelled
                                    </h6>
                                    <h5 class="fw-bold text-danger display-6 mb-0"><?= $counts['cancelled'] ?></h5>
                                </div>
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
                    <h5 class="modal-title">Update User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" id="user_id" name="ID" />

                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label">User Name</label>
                            <input type="text" id="user_name" name="UserName" class="form-control" required placeholder="Enter Name" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" id="email" name="Email" class="form-control" required placeholder="xxxx@xxx.xx" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="Photo" id="profileImage" class="form-control" accept="image/*" />
                            <!-- Preview -->
                            <img id="profileImagePreview" src="" alt="Profile Preview" style="max-width:100px; margin-top:10px; display:none;">
                        </div>
                    </div>

                    <div class="mb-3 mt-3" id="edit-additional-fields"></div>
                    <div class="mb-3 mt-3" id="edit-alert-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="update-user">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/profile.js') ?>"></script>