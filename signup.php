<?php
session_start();

if(isset($_SESSION['user_id'])){
    header('location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
    <?php require('includes/header.scripts.php'); ?>
</head>

<body>
<main class="main auth-main">
  <section class="section auth-section">
    <div class="card p-3 pt-4 shadow-sm">
      <div class="text-center mb-5">
        <h2 class="card-title text-primary">Register</h2>
      </div>
      <div class="card-body p-0 p-lg-3">
        <form id="registerForm">
          <div class="mb-3">
            <label for="firstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstName" name="firstName" required>
          </div>
          <div class="mb-3">
            <label for="lastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastName" name="lastName" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Phone no.</label>
            <input type="tel" class="form-control" id="phone" name="phone" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="mb-3">
            <label for="confirmpassword" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" required>
          </div>
          <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary" id="submitBtn">
              <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              Register
            </button>
          </div>
          <div class="text-center mt-4">
            Already have an account? <a href="login.php" class="link-primary">Login</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<?php require ('includes/footer.php'); ?>
</body>

</html>
