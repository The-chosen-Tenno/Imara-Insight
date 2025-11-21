<?php
require_once('../layouts/login_header.php');
require_once('../../config.php');
?>

<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">

      <div class="card px-sm-6 px-0">
        <div class="card-body">

          <div class="app-brand justify-content-center mb-6">
            <span class="app-brand-text demo text-heading fw-bold" style="text-transform: none;">
              <!-- <img src="<?= asset('assets/img/favicon/favicon.png') ?>" alt="icon" class="mb-2" style="width:30px; height:30px; "> -->
              Imara-Insight</span>
          </div>

          <h4 class="mb-1">Create an account</h4>
          <form id="createUserForm" class="mb-6" action="../../services/create-account-auth.php">
            <div class="mb-6">
              <div class="mb-6">
                <label for="Full Name" class="form-label">Full Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="FullName"
                  name="FullName"
                  Required
                  placeholder="Enter your Full Name"
                  autofocus />
              </div>
              <label for="User Name" class="form-label">User Name</label>
              <input
                type="text"
                class="form-control"
                id="UserName"
                name="UserName"
                placeholder="Enter a User Name"
                Required
                autofocus />
              <span id="username-error" class="text-danger small"></span>
            </div>
            <div class="mb-6">
              <label for="email" class="form-label">Email</label>
              <input type="email"
                class="form-control"
                id="email"
                Required
                name="Email"
                placeholder="Enter your email" />
              <div id="email-error" style="color:red; font-size:0.9em;"></div>
            </div>
            <div class="mb-6 form-password-toggle">
              <label class="form-label" for="Password">Password</label>
              <div class="input-group input-group-merge">
                <input
                  type="password"
                  id="password"
                  class="form-control"
                  name="Password"
                  Required
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                <div id="password-error" style="color:red; font-size:0.9em;"></div>
              </div>
            </div>
            <button id="create-user-btn" class="btn d-grid w-100"
              style="<?= $styleMap['imara-button-purple'] ?>">Sign up</button>
          </form>

          <p class="text-center">
            <span>Already have an account?</span>
            <a href="login.php">
              <span>Sign in instead</span>
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require_once('../layouts/login_footer.php');
?>
<script src="../../assets/forms-js/create.js"></script>