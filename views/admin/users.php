<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Users.php';

// Get logged-in user info from session
$permission = $_SESSION['role'] ?? null;
$userId = $_SESSION['userId'] ?? null;

// Restrict access

$userModel = new User();
$table = $userModel->getTableName();
$data = $userModel->getAll();
?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"> </span>
        Employees
    </h4>
    <div class="row m-3">
        <div class="col-6">
            <div class="d-flex align-items-center m-3">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input type="text" id="searchInput" class="form-control border-0 shadow-none" placeholder="Search" aria-label="Search..." />
            </div>
        </div>
    </div>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="m-4">
            <div id="delete-alert-container"></div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table" id="usersTable">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>User Name</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
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
                                <?php if ($permission === 'admin'): ?>
                                    <span class="text-capitalize"><?= htmlspecialchars($userRole) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Restricted</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once('../layouts/footer.php'); ?>

<script src="<?= asset('assets/forms-js/users.js') ?>"></script>

<!-- Inline script for search filter -->
<script>
    document.getElementById("searchInput").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#usersTable tbody tr");

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
</script>