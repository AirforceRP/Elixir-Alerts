<?php
session_start();

if(isset($_SESSION['user_id'])){
    header('location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <link rel="stylesheet" href="assets/css/style.css">

  <title>Forgot Password</title>
</head>

<body>
  <main class="main auth-main">
    <section class="section auth-section">
      <div class="card p-3 pt-4 shadow-sm">
        <div class="text-center mb-5">
          <h2 class="card-title text-primary">Forgot Password</h2>
        </div>
        <div class="card-body p-0 p-lg-3">
          <form>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" required>
            </div>
            <div class="text-center mt-4">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <div class="text-center mt-3">
              <a href="login.php" class="link-primary">Back to Login</a>
            </div>
          </form>
        </div>
      </div>
    </section>
  </main>


  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
    crossorigin="anonymous"></script>
  <script src="assets/js/script.js"></script>
</body>

</html>