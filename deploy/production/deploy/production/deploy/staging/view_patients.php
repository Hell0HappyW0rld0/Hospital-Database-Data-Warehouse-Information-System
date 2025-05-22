<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Employee') {
    header('Location: login.php'); 
    exit();
}

$mysqli = new mysqli("localhost", "root", "MyNewPass", "main_db");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$query = "SELECT DoctorName FROM Doctor WHERE doctorID = " . $_SESSION['user_id'];
$result = $mysqli->query($query);

if ($result && $row = $result->fetch_assoc()) {
    $doctorName = $row['DoctorName'];
} else {
    $doctorName = "Doctor Not Found";
}

$result->close();

// Retrieve current patients from the Patient table
$currentPatientsQuery = "SELECT * FROM Patient";
$currentPatientsResult = $mysqli->query($currentPatientsQuery);

// Retrieve past patients from the PatientDim table
$pastPatientsQuery = "SELECT * FROM PatientDim";
$pastPatientsResult = $mysqli->query($pastPatientsQuery);

// Calculate the number of current and past patients
$numCurrentPatients = $currentPatientsResult->num_rows;
$numPastPatients = $pastPatientsResult->num_rows;

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="view_patient.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>View Patients</title>
</head>
<body>

<header>
    <img src="logo.jpg" alt="Hospital Logo">
    Elyisian Medical Hospital
</header>

<nav>
        <div class="welcome-container">
            <div class="profile-icon"><i class="material-icons">person</i></div>
            <div class="welcome-text">Welcome,</div>
            <div class="dropdown">
                <div class="welcome-text"><?php echo $doctorName; ?>!</div>
            </div>
        </div>
        <div class="nav-links">
            <a class="nav-link" href="employee_dashboard.php">Home <i class="material-icons">home</i></a>
            <a class="nav-link" href="view_patients.php">Patients <i class="material-icons">people</i></a>
            <a class="nav-link" href="view_appointments.php">Appointments <i class="material-icons">event</i></a>
            <a class="nav-link" href="view_treatments.php">Treatments <i class="material-icons">healing</i></a>
            <a class="nav-link" href="logout.php">Logout <i class="material-icons">exit_to_app</i></a>
</div>

    </nav>

<section>
    <h2>View Patients</h2>

    <!-- Search or filter functionality -->
    <div class="search-container">
        <input type="text" id="searchBar" placeholder="Search patients...">
    </div>

    <!-- Current Patients -->
    <h3>Current Patients (<?php echo $numCurrentPatients; ?>)</h3>
    <div class="data-container">
                <?php
        while ($currentPatientRow = $currentPatientsResult->fetch_assoc()) {
            echo '<div class="data-box">';
            echo '<h4>' . $currentPatientRow['PatientName'] . '</h4>';
            echo '<p>Disease: ' . $currentPatientRow['Disease'] . '</p>';
            echo '<p>Admission Date: ' . $currentPatientRow['AdmissionDate'] . '</p>';
            echo '</div>';
        }
        ?>
    </div>

    <!-- Past Patients -->
    <h3>Past Patients (<?php echo $numPastPatients; ?>)</h3>
    <div class="data-container">
        <?php
        while ($pastPatientRow = $pastPatientsResult->fetch_assoc()) {
            echo '<div class="data-box">';
            echo '<h4>' . $pastPatientRow['PatientName'] . '</h4>';
            echo '<p>Disease: ' . $pastPatientRow['Disease'] . '</p>';
            echo '<p>Admission Date: ' . $pastPatientRow['AdmissionDate'] . '</p>';
            echo '<p>Discharge Date: ' . $pastPatientRow['DischargeDate'] . '</p>';
            echo '</div>';
        }
        ?>
    </div>
</section>

<script>
    // Add JavaScript for search functionality
    document.getElementById('searchBar').addEventListener('input', function () {
        var input, filter, containers, boxes, h4, i, txtValue;
        input = document.getElementById('searchBar');
        filter = input.value.toUpperCase();
        containers = document.querySelectorAll('.data-container');

        containers.forEach(function (container) {
            boxes = container.querySelectorAll('.data-box');
            boxes.forEach(function (box) {
                h4 = box.querySelector('h4');
                txtValue = h4.textContent || h4.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    box.style.display = '';
                } else {
                    box.style.display = 'none';
                }
            });
        });
    });
</script>

</body>
</html>
