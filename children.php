<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

include 'php/db_connect.php';

$parent_id = $_SESSION['user_id'];

// Fetch children data from the database
$sql = "SELECT id, first_name, last_name, photo FROM children WHERE parent_id = ?";
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
    <title>Children</title>
    <?php require ('includes/header.scripts.php'); ?>
</head>

<body>
<main class="main dashboard-main">
    <?php require ('includes/user_header.php'); ?>
    <div class="main-content">
        <div class="container">
            <section class="section-flex-col">
                <div class="d-flex align-items-center justify-content-between">
                    <h1 class="text-primary">Children</h1>
                    <a href="addNewChild.php" class="btn btn-primary">Add Child</a>
                </div>
                <?php if (empty($children)): ?>
                    <div class="no-data-banner">No Children Added yet!</div>
                <?php else: ?>
                    <ul class="custom-grid-list">
                        <?php foreach ($children as $child): ?>
                            <li class="custom-card">
                                <div class="custom-card-body">
                                    <div class="profile-image-container mx-auto">
                                        <img class="profile-image"
                                             src="<?= !empty($child['photo']) ? '../uploads/photos/' . $child['photo'] : './assets/placeholder.png' ?>" />
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between gap-3">
                                        <a class="custom-card-link" href="childView.php?id=<?= $child['id'] ?>">
                                            <p class="custom-card-name"><?= htmlspecialchars($child['first_name'] . ' ' . $child['last_name']) ?></p>
                                            <img src="./assets/Arrow-1.svg" />
                                        </a>
                                        <div class="d-flex gap-2">
                                            <a class="btn btn-rounded btn-card" href="childEdit.php?id=<?= $child['id'] ?>">
                                                <img src="./assets/edit-icon.svg" />
                                            </a>
                                            <button class="btn btn-rounded btn-card" data-bs-toggle="modal"
                                                    data-bs-target="#deleteChildProfileModal"
                                                    data-child-id="<?= $child['id'] ?>">
                                                <img src="./assets/trash-can-icon.svg" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <footer class="footer">
        <div>
            <p class="mb-0">&copy; 2024 <span class="text-primary">Medication.</span> All rights reserved.</p>
        </div>
    </footer>
</main>

<!-- deleting Child profile modal -->
<div class="modal fade" id="deleteChildProfileModal" tabindex="-1" aria-labelledby="deleteChildProfileModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteChildProfileModalLabel">Delete Child!</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this Child profile?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<?php require ('includes/footer.php'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteChildProfileModal = document.getElementById('deleteChildProfileModal');
        deleteChildProfileModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const childId = button.getAttribute('data-child-id');
            const confirmDeleteButton = document.getElementById('confirmDeleteButton');

            confirmDeleteButton.onclick = function () {
                fetch(`php/delete_child.php?id=${childId}`, {
                    method: 'GET',
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete child: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            };
        });
    });
</script>

</body>

</html>
