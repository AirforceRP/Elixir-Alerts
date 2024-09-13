<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header('location: login.php');
    exit;
}

// Fetch children data from the database
include 'php/db_connect.php';
$parent_id = $_SESSION['user_id'];
$sql = "SELECT id, first_name, last_name FROM children WHERE parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

$children = [];
while ($row = $result->fetch_assoc()) {
    $children[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedules</title>
    <?php require('includes/header.scripts.php'); ?>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.print.min.css' rel='stylesheet'>
    <style>
        .fc-event {
            cursor: pointer;
        }
        .fc-event:hover {
            background: #ccc !important;
        }
    </style>
</head>

<body>
<main class="main dashboard-main">
    <?php require('includes/user_header.php'); ?>
    <div class="main-content">
        <div class="container">
            <section class="section-flex-col">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h1 class="text-primary">Schedules</h1>
                    <select id="childDropdown" class="form-select w-auto">
                        <option value="all">All Children</option>
                        <?php foreach ($children as $child): ?>
                            <option value="<?= $child['id'] ?>"><?= $child['first_name'] . ' ' . $child['last_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="custom-card">
                    <div id="calendar"></div>
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

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require('includes/footer.php'); ?>

<script defer src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src="assets/js/schedulesCalender.js"></script>
<script>

</script>
</body>
</html>
