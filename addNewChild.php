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
    <title>Add Child</title>
    <?php require ('includes/header.scripts.php'); ?>
</head>

<body>
<main class="main dashboard-main">
    <?php require ('includes/user_header.php'); ?>
    <div class="main-content">
        <div class="container">
            <section class="section-flex-col">
                <div class="d-flex align-items-center justify-content-between">
                    <h1 class="text-primary">Add Child</h1>
                </div>
                <form id="addChildForm" enctype="multipart/form-data" method="post">
                    <div class="custom-card">
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
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" required >
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" required >
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="mb-3">
                                    <label for="dateOfBirth" class="form-label">Date of birth</label>
                                    <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth" required >
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select id="gender" class="form-select" name="gender" required>
                                        <option value="male" >Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="mb-3">
                                    <label for="timezone" class="form-label">Select Time Zone:</label>
                                    <select id="timezone" class="form-select" name="timezone" required></select>
                                    <span class="text-info mt-2">SMS will be sent according to Timezone</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">Add Child</button>
                            <a href="children.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>

            </section>
        </div>
    </div>
    <footer class="footer">
        <div>
            <p class="mb-0">&copy; 2024 <span class="text-primary">Medication.</span> All rights reserved.</p>
        </div>
    </footer>
</main>
<?php require ('includes/footer.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>

<script>

    document.addEventListener('DOMContentLoaded', function () {
        const timeZoneSelect = document.getElementById('timezone');
        const timeZones = moment.tz.names();


        timeZones.forEach(timeZone => {
            const option = document.createElement('option');
            option.value = timeZone;
            option.textContent = `(GMT${moment.tz(timeZone).format('Z')}) ${timeZone.replace('_', ' ')}`;
            timeZoneSelect.appendChild(option);
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



</script>
</body>

</html>