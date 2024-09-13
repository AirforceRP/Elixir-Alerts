<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

include 'php/db_connect.php';

$child_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($child_id === 0) {
    echo "Invalid Child ID.";
    exit();
}

$parent_id = $_SESSION['user_id'];

// Fetch child data from the database
$sql = "SELECT first_name, last_name, date_of_birth, gender, photo, timezone FROM children WHERE id = ? AND parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $child_id, $parent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Child not found or you do not have permission to view this child.";
    exit();
}

$child = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Child</title>
    <?php require ('includes/header.scripts.php'); ?>
</head>

<body>
<main class="main dashboard-main">
    <?php require ('includes/user_header.php'); ?>
    <div class="main-content">
        <div class="container">
            <section class="section-flex-col mb-5">
                <div class="profile-container">
                    <div class="profile-image-container">
                        <img class="profile-image" src="<?= !empty($child['photo']) ? '../uploads/photos/' . $child['photo'] : './assets/placeholder.png' ?>" />
                    </div>
                    <h1 class="text-primary"><?= htmlspecialchars($child['first_name'] . ' ' . $child['last_name']) ?></h1>
                </div>
                <div class="custom-card">
                    <div class="child-details">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-3">
                                <h5 class="text-primary">Birthdate:</h5>
                                <p><?= htmlspecialchars(date('d/m/Y', strtotime($child['date_of_birth']))) ?></p>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <h5 class="text-primary">Gender:</h5>
                                <p><?= htmlspecialchars(ucfirst($child['gender'])) ?></p>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <h5 class="text-primary">TimeZone:</h5>
                                <p><?= htmlspecialchars(ucfirst($child['timezone'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="section-flex-col">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="text-primary">Medication</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMedModal">Add Med</button>
                </div>
                <!-- conditional render -->
                <!-- <div class="no-data-banner">No Medication Added yet!</div> -->
                <!-- conditional render -->
                <div class="custom-card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Medication</th>
                                <th scope="col">Start date</th>
                                <th scope="col">End Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="medicationTableBody">
                            <!-- Medication data will be populated here by JavaScript -->
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

<!-- add new Modal -->
<div class="modal fade" id="newMedModal" tabindex="-1" aria-labelledby="newMedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="newMedModalLabel">Add Medication</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <input type="hidden" id="childId" value="<?= $child_id ?>">

            <div class="modal-body">
                <div class="mb-3">
                    <label for="medicationName" class="form-label">Medication Name</label>
                    <input type="text" class="form-control" id="medicationName" required>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" required>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" required>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="text-primary btn-sm">Add time slot</h5>
                    <button class="btn btn-primary" id="addButton">Add</button>
                </div>
                <div class="row" id="timeSlotContainer">
                    <div class="col-12 mb-3 d-flex align-items-end gap-3" id="timeSlot1">
                        <div class="flex-1 w-100">
                            <label for="time1" class="form-label">Time 1</label>
                            <input type="time" class="form-control" id="time1" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <input type="hidden" id="childId" value="<?= $child_id ?>">
                <input type="hidden" id="editMedicationId"> <!-- Add this hidden input for medication ID -->

                <button type="button" class="btn btn-primary" id="saveMedicationButton">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- View Medication Modal -->
<div class="modal fade" id="viewMedModal" tabindex="-1" aria-labelledby="viewMedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="viewMedModalLabel">View Medication</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="text-primary">Medication:</h5>
                    <p class="mb-0" id="viewMedicationName">Ponstan</p>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="text-primary">Start Date:</h5>
                    <p class="mb-0" id="viewStartDate">01/07/2024</p>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="text-primary">End Date:</h5>
                    <p class="mb-0" id="viewEndDate">30/07/2024</p>
                </div>
                <div class="d-flex align-items-start justify-content-between">
                    <h5 class="text-primary">Timing:</h5>
                    <div class="d-flex flex-column align-items-end" id="viewTimings">
                        <!-- Timings will be populated here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Med Modal -->
<div class="modal fade" id="editMedModal" tabindex="-1" aria-labelledby="editMedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editMedModalLabel">Edit Medication</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editMedicationName" class="form-label">Medication Name</label>
                    <input type="text" class="form-control" id="editMedicationName" required>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="editStartDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="editStartDate" required>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="editEndDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="editEndDate" required>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="text-primary btn-sm">Add time slot</h5>
                    <button class="btn btn-primary" id="addButtonUpdate">Add</button>
                </div>
                <div class="row" id="timeSlotContainerUpdate">
                    <!-- Time slots will be populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEditMedicationButton">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- deleting Med modal -->
<div class="modal fade" id="deleteMedModal" tabindex="-1" aria-labelledby="deleteMedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteMedModalLabel">Delete Medication</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this Medication?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal">
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
<script src="assets/js/ajax.js"></script>
<script>

        const addButtonUpdate = document.getElementById('addButtonUpdate');
        const timeSlotContainerUpdate = document.getElementById('timeSlotContainerUpdate');
        let timeSlotCounterUpdate = 2; // Start from 2 since there's already a "Time 1"

        addButtonUpdate.addEventListener('click', function () {
            addTimeSlot(timeSlotContainerUpdate, timeSlotCounterUpdate++);
        });

        document.getElementById('addButton').addEventListener('click', function () {
             addTimeSlot(document.getElementById('timeSlotContainer'), timeSlotCounterUpdate++);
        });

        function loadMedications() {
            const childId = <?= $child_id ?>;
            ajaxRequest('GET', 'php/get_medications.php?child_id=' + childId, null, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    const medications = response.data;
                    const medicationTableBody = document.getElementById('medicationTableBody');
                    medicationTableBody.innerHTML = '';

                    medications.forEach((medication, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${medication.name}</td>
                    <td>${medication.start_date}</td>
                    <td>${medication.end_date}</td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <button class="btn btn-rounded" data-bs-toggle="modal" data-bs-target="#viewMedModal" onclick="viewMedication(${medication.id})"><img src="./assets/view-icon.svg" /></button>
                            <button class="btn btn-rounded" data-bs-toggle="modal" data-bs-target="#editMedModal" onclick="editMedication(${medication.id})"><img src="./assets/edit-icon.svg" /></button>
                            <button class="btn btn-rounded" data-bs-toggle="modal" data-bs-target="#deleteMedModal" onclick="deleteMedication(${medication.id})"><img src="./assets/trash-can-icon.svg" /></button>
                        </div>
                    </td>
                `;
                        medicationTableBody.appendChild(row);
                    });
                } else {
                    toastr.error(response.message || 'Failed to load medications');
                }
            });
        }

        function viewMedication(medicationId) {
            ajaxRequest('GET', 'php/get_medication.php?id=' + medicationId, null, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    const medication = response.data;
                    document.getElementById('viewMedicationName').innerText = medication.name;
                    document.getElementById('viewStartDate').innerText = medication.start_date;
                    document.getElementById('viewEndDate').innerText = medication.end_date;

                    const timingsContainer = document.getElementById('viewTimings');
                    timingsContainer.innerHTML = '';
                    medication.time_slots.forEach(time => {
                        const timing = document.createElement('p');
                        timing.classList.add('mb-0');
                        timing.innerText = formatTimeToAMPM(time);
                        timingsContainer.appendChild(timing);
                    });
                } else {
                    toastr.error(response.message || 'Failed to load medication details');
                }
            });
        }

        function editMedication(medicationId) {
            ajaxRequest('GET', 'php/get_medication.php?id=' + medicationId, null, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    const medication = response.data;
                    document.getElementById('editMedicationName').value = medication.name;
                    document.getElementById('editStartDate').value = medication.start_date;
                    document.getElementById('editEndDate').value = medication.end_date;
                    $("#editMedicationId").val(medicationId);

                    const container = document.getElementById('timeSlotContainerUpdate');
                    container.innerHTML = '';
                    medication.time_slots.forEach((time, index) => {
                        addTimeSlot(container, index + 1, time);
                    });
                } else {
                    toastr.error(response.message || 'Failed to load medication details');
                }
            });
        }

        function deleteMedication(medicationId) {
            document.getElementById('confirmDeleteButton').onclick = function() {
                ajaxRequest('POST', 'php/delete_medication.php', { id: medicationId }, function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        toastr.success('Medication deleted successfully');
                        loadMedications();
                    } else {
                        toastr.error(response.message || 'Failed to delete medication');
                    }
                    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteMedModal'));
                    deleteModal.hide();
                });
            };
        }

        function addTimeSlot(container, counter, time = '') {
             const newTimeSlot = document.createElement('div');
            newTimeSlot.classList.add('col-12', 'mb-3', 'd-flex', 'align-items-end', 'gap-3');
            newTimeSlot.id = `timeSlot${counter}`;

            const newInputContainer = document.createElement('div');
            newInputContainer.classList.add('flex-1', 'w-100');

            const newLabel = document.createElement('label');
            newLabel.classList.add('form-label');
            newLabel.setAttribute('for', `time${counter}`);
            newLabel.textContent = `Time ${counter}`;

            const newInput = document.createElement('input');
            newInput.type = 'time';
            newInput.classList.add('form-control');
            newInput.id = `time${counter}`;
            newInput.value = time;
            newInput.required = true;

            const newDeleteButton = document.createElement('button');
            newDeleteButton.classList.add('btn', 'btn-danger');
            newDeleteButton.innerHTML = '<img src="assets/transh-can-white-icon.svg" />';
            newDeleteButton.addEventListener('click', function () {
                removeTimeSlot(newTimeSlot.id, container);
            });

            newInputContainer.appendChild(newLabel);
            newInputContainer.appendChild(newInput);
            newTimeSlot.appendChild(newInputContainer);
            newTimeSlot.appendChild(newDeleteButton);

            container.appendChild(newTimeSlot);
        }

        function removeTimeSlot(id, container) {
            const timeSlot = document.getElementById(id);
            if (container.childElementCount > 1) {
                timeSlot.remove();
            }
        }

        loadMedications()
</script>
