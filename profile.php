<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header('location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <?php require ('includes/header.scripts.php'); ?>
</head>

<body>
<main class="main dashboard-main">
    <?php require ('includes/user_header.php'); ?>
    <div class="main-content">
        <div class="container">
            <section class="section-flex-col">
                <div class="d-flex align-items-center justify-content-between">
                    <h1 class="text-primary">Profile</h1>
                </div>
                <div class="custom-card">
                    <ul id="tabNav" class="nav nav-underline mb-5">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#profileDetails" data-tab="profileDetails">Profile
                                Details</a>
                        </li>
<!--                        <li class="nav-item">-->
<!--                            <a class="nav-link" href="#changePassword" data-tab="changePassword">Change Password</a>-->
<!--                        </li>-->
                    </ul>
                    <div id="profileDetails" class="tab-content active">
                        <form id="profileForm" enctype="multipart/form-data" method="post">
                            <div class="file-upload-container mb-3">
                                <div class="file-upload">
                                    <input type="file" id="fileInput" name="photo" />
                                    <img src="./assets/gallery-add.svg" />
                                    <p>Upload photo</p>
                                </div>
                                <div class="file-uploaded">
                                    <img id="uploadedImage" alt="profile picture" />
                                    <div class="upload-btn-container">
                                        <button class="btn btn-ghost btn-upload" id="updateImage" type="button">
                                            <img src="./assets/gallery-add.svg" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" required value="<?php echo $_SESSION['user_data']['firstName']; ?>">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" required value="<?php echo $_SESSION['user_data']['lastName']; ?>">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required value="<?php echo $_SESSION['user_data']['email']; ?>">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 mb-3">
                                    <label for="phone" class="form-label">Phone no.</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required value="<?php echo $_SESSION['user_data']['phone']; ?>">
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    Update
                                </button>
                            </div>
                        </form>

                    </div>
                    <div id="changePassword" class="tab-content">
                        <form id="passwordForm">
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4 mb-3">
                                    <label for="oldPassword" class="form-label">Old Password</label>
                                    <input type="password" class="form-control" id="oldPassword" name="oldPassword" required>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary" id="submitBtnPassword">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <footer class="footer">
        <div>
            <p class="mb-0">&copy; 2024 <span class="text-primary">Medication.</span> All rights reserved.</p>
        </div>
    </footer>
</main>

<?php require('includes/footer.php')?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('#tabNav .nav-link');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function (event) {
                event.preventDefault();
                const target = this.getAttribute('data-tab');

                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                contents.forEach(content => {
                    content.classList.remove('active');
                    if (content.id === target) {
                        content.classList.add('active');
                    }
                });
            });
        });

        document.getElementById('fileInput').addEventListener('change', function (event) {
            handleFileUpload(event.target);
        });

        document.getElementById('updateImage').addEventListener('click', function () {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function (event) {
            handleFileUpload(event.target);
        });

        function handleFileUpload(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const uploadedImage = document.getElementById('uploadedImage');
                    uploadedImage.src = e.target.result;

                    // Hide file-upload and show file-uploaded
                    document.querySelector('.file-upload').style.display = 'none';
                    document.querySelector('.file-uploaded').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
    });
    $(document).ready(function(e){
        $("#phone").focus();
    })

</script>
</body>

</html>
