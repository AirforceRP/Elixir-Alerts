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
    <title>Login</title>
    <?php require('includes/header.scripts.php'); ?>
</head>

<body>
<main class="main auth-main">
    <section class="section auth-section">
        <div class="card p-3 pt-4 shadow-sm">
            <div class="text-center mb-5">
                <h2 class="card-title text-primary">Login</h2>
            </div>
            <div class="card-body p-0 p-lg-3">
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
<!--                    <div class="text-end">-->
<!--                        <a href="forgotPassword.php" class="link-primary">-->
<!--                            Forgot Password?-->
<!--                        </a>-->
<!--                    </div>-->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Login</button>
                    </div>
                    <div class="text-center mt-4">
                        Don't have an account? <a href="signup.php" class="link-primary">Sign Up</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php require ('includes/footer.php'); ?>

</body>

</html>
