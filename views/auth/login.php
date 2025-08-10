<?php
require_once('../layouts/login_header.php');
require_once('../../config.php');
?>

<!-- Content -->
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <!-- Register -->
            <div class="card">
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
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            <div class="mb-3" id="password-error">
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit" id="create">Sign in</button>
                        </div>
                    </form>

                    <p class="text-center">
                        <span>New on our platform?</span>
                        <a href="../admin/books.php">
                            <span>Explore as Guest</span>
                        </a>
                    </p>
                    <p class="text-center">
                        <span>Disable after creating first account</span>
                        <a href="../auth/create-account.php">CREATE</a>
                        <span></span>
                    </p>
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>
</div>
<!-- / Content -->

<?php
require_once('../layouts/login_footer.php');
?>
<script src="../../assets/forms-js/login.js"></script>

<script>
    $(document).ready(function() {
        $("#formAuthentication").on("submit", function(event) {
            // Prevent form from submitting
            event.preventDefault();

            // Clear previous error messages
            $(".error-message").remove();

            // Get form values
            const email = $("#email").val().trim();
            const password = $("#password").val().trim();
            let isValid = true;

            // Email validation
            if (email === "") {
                $("#email").after('<span class="error-message" style="color: red;">Type Email!</span>');
                isValid = false;
            } else {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    $("#email").after('<span class="error-message" style="color: red;">Enter a valid Email!</span>');
                    isValid = false;
                }
            }

            // Password validation
            if (password === "") {
                $("#password-error").after('<span class="error-message" style="color: red;">Enter Password!</span>');
                isValid = false;
            }

            // If all validations pass, submit the form
            if (isValid) {
                // Now, submit the form
                this.submit();
            }
        });
    });
</script>