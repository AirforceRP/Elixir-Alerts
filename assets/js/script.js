$(document).ready(function() {
    $('select').select2();
    const phoneElement = document.querySelector("#phone");
    let phoneInput;
    if(phoneElement) {
        phoneInput = intlTelInput(document.querySelector("#phone"), {
            initialCountry: "auto",
            geoIpLookup: function (success, failure) {
                $.get("https://ipinfo.io", function () {
                }, "jsonp").always(function (resp) {
                    const countryCode = (resp && resp.country) ? resp.country : "us";
                    success(countryCode);
                });
            },
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });
    }
    $('#registerForm').on('submit', function(event) {
        event.preventDefault();

        const data = {
            firstName: $('#firstName').val(),
            lastName: $('#lastName').val(),
            email: $('#email').val(),
            phone: phoneInput.getNumber(),
            password: $('#password').val(),
            confirmpassword: $('#confirmpassword').val()
        };

        // Phone number validation
        if (!phoneInput.isValidNumber()) {
            toastr.error('Invalid phone number format.');
            return;
        }

        // Password validation (minimum 8 characters, at least one special character)
        const passwordPattern = /^(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/;
        if (!passwordPattern.test(data.password)) {
            toastr.error('Password must be at least 8 characters long and contain at least one special character.');
            return;
        }

        if (data.password !== data.confirmpassword) {
            toastr.error('Passwords do not match.');
            return;
        }

        ajaxRequest('POST', 'php/register.php', data, function(response) {
            response = JSON.parse(response);
            if (response.success) {
                toastr.success('Registration successful');
                // Redirect or other actions
                window.location = 'index.php';
            } else {
                toastr.error('Registration failed: ' + (response.message || 'Unknown error occurred'));
            }
        });
    });

    $('#loginForm').on('submit', function(event) {
            event.preventDefault(); // Prevent form from submitting the traditional way

        const data = {
            email: $('#email').val(),
            password: $('#password').val(),
        };
        ajaxRequest('POST', 'php/login.php', data, function(response) {
            response = JSON.parse(response);
            if (response.success) {
                toastr.success('Login successful redirecting...');
                // Redirect or other actions
                window.location = 'index.php';
            } else {
                toastr.error('Registration failed: ' + (response.message || 'Unknown error occurred'));
            }
        });
    });
    // Profile update form submission
    $('#profileForm').on('submit', function(event) {
        event.preventDefault(); // Prevent form from submitting the traditional way

        const formData = new FormData(this);

        // Debugging: Check if FormData contains data
        if (!formData.entries().next().value) {
            console.error('FormData is empty');
        } else {
            console.log('FormData entries:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
        }

        if (phoneInput) {
            formData.set('phone', phoneInput.getNumber()); // Update phone number
        }

        // Debugging: Check if FormData contains data after setting phone number
        if (!formData.entries().next().value) {
            console.error('FormData is still empty after adding phone number');
        } else {
            console.log('FormData entries after setting phone number:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
        }

        ajaxRequest('POST', 'php/update_profile.php', formData, function(response) {
            response = JSON.parse(response);
            if (response.success) {
                toastr.success('Profile updated successfully');
                // location.reload();
            } else {
                toastr.error('Profile update failed: ' + (response.message || 'Unknown error occurred'));
            }
        }, true);
    });

    $('#addChildForm').on('submit', function(event) {
        event.preventDefault(); // Prevent form from submitting the traditional way

        const formData = new FormData(this);
        ajaxRequest('POST', 'php/add_child.php', formData, function(response) {
            response = JSON.parse(response);
            if (response.success) {
                toastr.success('Child added successfully');
                window.location.href = 'children.php';
            } else {
                toastr.error('Failed to add child: ' + (response.message || 'Unknown error occurred'));
            }
        }, true);
    });

    $('#editChildForm').on('submit', function(event) {
        event.preventDefault(); // Prevent form from submitting the traditional way

        const formData = new FormData(this);
        ajaxRequest('POST', 'php/update_child.php', formData, function(response) {
            response = JSON.parse(response);
            if (response.success) {
                toastr.success('Child updated successfully');
                window.location.href = 'children.php';
            } else {
                toastr.error('Failed to add child: ' + (response.message || 'Unknown error occurred'));
            }
        }, true);
    });

    $('#saveMedicationButton').on('click', function () {
        const childId = $('#childId').val();  // Fetch the childId from a hidden input or a data attribute
        const name = $('#medicationName').val();
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();

        const timeSlots = [];
        $('#timeSlotContainer input[type="time"]').each(function () {
            timeSlots.push($(this).val());
        });

        if (!name || !startDate || !endDate || timeSlots.length === 0) {
            toastr.error('All fields are required.');
            return;
        }

        const data = {
            child_id: childId,
            name: name,
            start_date: startDate,
            end_date: endDate,
            time_slots: timeSlots
        };

        ajaxRequest('POST', 'php/save_medicine.php', data, function (response) {
            response = JSON.parse(response);
            if (response.success) {
                toastr.success('Medication saved successfully');
                // Close modal and reload medications
                const newMedModal = bootstrap.Modal.getInstance(document.getElementById('newMedModal'));
                newMedModal.hide();
                loadMedications();
            } else {
                toastr.error('Failed to save medication: ' + (response.message || 'Unknown error occurred'));
            }
        });
    });

    $('#saveEditMedicationButton').on('click', function () {
        const medicationId = $('#editMedicationId').val();  // Assuming there's a hidden input with the medication ID
        const name = $('#editMedicationName').val();
        const startDate = $('#editStartDate').val();
        const endDate = $('#editEndDate').val();

        const timeSlots = [];
        $('#timeSlotContainerUpdate input[type="time"]').each(function () {
            timeSlots.push($(this).val());
        });

        // Validate form fields
        if (!name) {
            toastr.error('Medication name is required.');
            return;
        }
        if (!startDate) {
            toastr.error('Start date is required.');
            return;
        }
        if (!endDate) {
            toastr.error('End date is required.');
            return;
        }
        if (timeSlots.length === 0) {
            toastr.error('At least one time slot is required.');
            return;
        }

        // Check if end date is before start date
        if (new Date(startDate) > new Date(endDate)) {
            toastr.error('End date cannot be before start date.');
            return;
        }

        const data = {
            medication_id: medicationId,
            name: name,
            start_date: startDate,
            end_date: endDate,
            time_slots: timeSlots
        };

        ajaxRequest('POST', 'php/save_edit_medication.php', data, function (response) {
            response = JSON.parse(response);
            if (response.success) {
                toastr.success('Medication updated successfully');
                // Close modal and reload medications
                const editMedModal = bootstrap.Modal.getInstance(document.getElementById('editMedModal'));
                editMedModal.hide();
                loadMedications();
            } else {
                toastr.error('Failed to update medication: ' + (response.message || 'Unknown error occurred'));
            }
        });
    });


});

function formatTimeToAMPM(time) {
    const [hour, minute] = time.split(':');
    let hours = parseInt(hour);
    const minutes = parseInt(minute);
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
    return `${hours}:${formattedMinutes} ${ampm}`;
}