<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header('location: login.php');
    exit;
}

// Fetch data from the database
include 'php/db_connect.php';

$parent_id = $_SESSION['user_id'];

// Fetch total children
$sql = "SELECT COUNT(*) as total_children FROM children WHERE parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();
$total_children = $result->fetch_assoc()['total_children'];
$stmt->close();

// Fetch total medication
$sql = "
    SELECT COUNT(*) as total_medication
    FROM medicine m
    JOIN children c ON m.child_id = c.id
    WHERE c.parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();
$medication_data = $result->fetch_assoc();
$stmt->close();

// Fetch children medication data
$sql = "
    SELECT c.first_name, c.last_name, m.name as medication_name, m.start_date, m.end_date
    FROM medicine m
    JOIN children c ON m.child_id = c.id
    WHERE c.parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();
$children_medications = [];
while ($row = $result->fetch_assoc()) {
    $children_medications[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php require('includes/header.scripts.php'); ?>
</head>

<body>
<main class="main dashboard-main">
    <?php require('includes/user_header.php'); ?>
    <div class="main-content">
        <div class="container">
            <section class="section-flex-col">
                <div class="d-flex align-items-center justify-content-between">
                    <h1 class="text-primary">Dashboard</h1>
                </div>
                <div>
                    <ul class="custom-grid-list">
                        <li class="custom-card">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex flex-column gap-2">
                                    <span class="text-primary fw-bold">Total Children</span>
                                    <span class="text-primary fw-bold fs-2"><?php echo $total_children; ?></span>
                                </div>
                                <img src="./assets/users-icon.svg" />
                            </div>
                        </li>
                        <li class="custom-card">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex flex-column gap-2">
                                    <span class="text-primary fw-bold">Total Medication</span>
                                    <span class="text-primary fw-bold fs-2"><?php echo $medication_data['total_medication']; ?></span>
                                </div>
                                <img src="./assets/pills-icon.svg" />
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
            <section class="section-flex-col mt-5">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="text-primary">Children</h3>
                    <a href="addNewChild.php" class="btn btn-primary">Add Child</a>
                </div>
                <div class="custom-card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Child</th>
                                <th scope="col">Medication</th>
                                <th scope="col">Start date</th>
                                <th scope="col">End Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($children_medications as $index => $medication): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $medication['first_name'] . ' ' . $medication['last_name']; ?></td>
                                    <td><?php echo $medication['medication_name']; ?></td>
                                    <td><?php echo $medication['start_date']; ?></td>
                                    <td><?php echo $medication['end_date']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
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
<?php require ('includes/footer.php'); ?>
</body>

</html>
