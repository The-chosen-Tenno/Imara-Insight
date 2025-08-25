<?php
require_once('../layouts/login_header.php');
require_once('../../config.php');
?>

<!-- Content -->
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">

            <!-- Login Card -->
            <div class="card" id="login-card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center">
                        <a href="index.html" class="app-brand-link gap-2">
                            <span class="app-brand-text demo text-body fw-bolder">Imara Insight</span>
                        </a>
                    </div>
                    <!-- /Logo -->

                    <h4 class="mb-2">Welcome to Imara-Insight</h4>
                    <p class="mb-4">Please sign-in to your account</p>

                    <!-- Login Form -->
                    <form id="formAuthentication" class="mb-3" action="<?= ('../../services/auth.php') ?>" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="text"
                                class="form-control"
                                id="email"
                                name="Email"
                                placeholder="Enter your email"
                                autofocus />
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Password</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input
                                    type="password"
                                    id="password"
                                    class="form-control"
                                    name="Password"
                                    placeholder="••••••••••••"
                                    aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            <div class="mb-3" id="password-error"></div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit" id="login">Sign in</button>
                        </div>
                    </form>

                    <p class="text-center">
                        <span>New on our platform?</span>
                        <a href="../auth/create-account.php">Create</a>
                    </p>
                </div>
            </div>
            <!-- /Login Card -->

            <!-- Pending Card -->
            <div class="card" id="pending-card" style="display:none;">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mb-2"></i>
                    <h4 class="mb-2 text-danger">Your Account is Pending</h4>
                    <p class="mb-4">Your account is not approved yet. Please contact the administrator.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" id="back-home" class="btn btn-primary btn-sm">Back To Login</button>
                    </div>
                </div>
            </div>
            <!-- /Pending Card -->

        </div>
    </div>
</div>
<!-- /Content -->

<?php
require_once('../layouts/login_footer.php');
?>

<script src="../../assets/forms-js/login.js"></script>