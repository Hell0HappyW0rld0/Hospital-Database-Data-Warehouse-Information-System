<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Patient') {
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli("localhost", "root", "MyNewPass", "main_db");

$query = "SELECT PatientName FROM Patient WHERE PatientID = " . $_SESSION['user_id'];
$result = $mysqli->query($query);

if ($result && $row = $result->fetch_assoc()) {
    $patientName = $row['PatientName'];
} else {
    $patientName = "Patient Not Found";
}

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get patient ID from the session
$patient_id = $_SESSION['user_id'];

// Query to retrieve active appointments for the patient
$currentAppointmentsQuery = "SELECT * FROM Appointment WHERE PatientID = $patient_id AND Status = 'Confirmed'";
$currentAppointmentsResult = $mysqli->query($currentAppointmentsQuery);

// Query to retrieve past appointments for the patient from AppointmentDim table
$pastAppointmentsQuery = "SELECT * FROM AppointmentDim WHERE PatientID = $patient_id";
$pastAppointmentsResult = $mysqli->query($pastAppointmentsQuery);

// Calculate counts
$numCurrentAppointments = $currentAppointmentsResult->num_rows;
$numPastAppointments = $pastAppointmentsResult->num_rows;

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="view_patient.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Your Appointments</title>
</head>
<body>

<header>
    <img src="logo.jpg" alt="Hospital Logo">
    Elysian Medical Hospital
</header>

<nav>
    <div class="welcome-container">
        <div class="profile-icon"><i class="material-icons">person</i></div>
        <div class="welcome-text">Welcome,</div>
        <div class="dropdown">
            <div class="welcome-text"><?php echo $patientName; ?>!</div>
        </div>
    </div>
    <div class="nav-links">
        <a class="nav-link" href="patient_dashboard.php">Home <i class="material-icons">home</i></a>
        <a class="nav-link" href="appointments.php">Appointments <i class="material-icons">date_range</i></a>
        <a class="nav-link" href="treatments.php">Treatments <i class="material-icons">vaccines</i></a>
        <a class="nav-link" href="prescriptions.php">Prescriptions <i class="material-icons">receipt</i></a>
        <a class="nav-link" href="Billing.php">Billing <i class="material-icons">monetization_on</i></a>
        <a class="nav-link" href="logout.php">Logout <i class="material-icons">exit_to_app</i></a>
    </div>
</nav>

<section>
    <h2>Search Appointments</h2>
    <!-- Search or filter functionality -->
    <div class="search-container">
        <input type="text" id="searchBar" placeholder="Search appointments...">
    </div>

    <!-- Appointments -->
    <h2>Active Appointments</h2>
    <div class="data-container">
        <?php
        if ($numCurrentAppointments > 0) {
            while ($currentAppointmentRow = $currentAppointmentsResult->fetch_assoc()) {
                echo '<div class="data-box">';
                echo '<h4>' . $currentAppointmentRow['Description'] . '</h4>';
                echo '<p>Date: ' . $currentAppointmentRow['AppointmentDate'] . '</p>';
                echo '<p>Status: ' . $currentAppointmentRow['Status'] . '</p>';
                echo '<p>Room: ' . $currentAppointmentRow['Room'] . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No active appointments found.</p>';
        }
        ?>
    </div>

    <h2>Past Appointments</h2>
    <div class="data-container">
        <?php
        if ($numPastAppointments > 0) {
            while ($pastAppointmentRow = $pastAppointmentsResult->fetch_assoc()) {
                echo '<div class="data-box">';
                echo '<h4>' . $pastAppointmentRow['Description'] . '</h4>';
                echo '<p>Date: ' . $pastAppointmentRow['AppointmentDate'] . '</p>';
                echo '<p>Status: ' . $pastAppointmentRow['Status'] . '</p>';
                echo '<p>Room: ' . $pastAppointmentRow['Room'] . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No past appointments found.</p>';
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
