<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Patient') {
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli("localhost", "root", "MyNewPass", "main_db");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get patient ID from the session
$patient_id = $_SESSION['user_id'];

// Query to retrieve active prescriptions for the patient
$currentPrescriptionsQuery = "SELECT * FROM Prescription WHERE PatientID = $patient_id";
$currentPrescriptionsResult = $mysqli->query($currentPrescriptionsQuery);

// Query to retrieve past prescriptions for the patient
$pastPrescriptionsQuery = "SELECT * FROM PrescriptionDim WHERE PatientID = $patient_id";
$pastPrescriptionsResult = $mysqli->query($pastPrescriptionsQuery);

// Calculate counts
$numCurrentPrescriptions = $currentPrescriptionsResult->num_rows;
$numPastPrescriptions = $pastPrescriptionsResult->num_rows;

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="view_patient.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Your Prescriptions</title>
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
            <!-- Display patient name or any other relevant information -->
            <div class="welcome-text"><?php echo "Patient Name"; ?>!</div>
        </div>
    </div>
    <div class="nav-links">
        <!-- Add any relevant links for patient navigation -->
        <a class="nav-link" href="patient_dashboard.php">Home <i class="material-icons">home</i></a>
        <a class="nav-link" href="appointments.php">Appointments <i class="material-icons">date_range</i></a>
        <a class="nav-link" href="treatments.php">Treatments <i class="material-icons">vaccines</i></a>
        <a class="nav-link" href="prescriptions.php">Prescriptions <i class="material-icons">receipt</i></a>
        <a class="nav-link" href="billing.php">Billing <i class="material-icons">monetization_on</i></a>
        <a class="nav-link" href="logout.php">Logout <i class="material-icons">exit_to_app</i></a>
    </div>
</nav>

<section>
    <h2>Your Prescriptions</h2>

    <!-- Search or filter functionality -->
    <div class="search-container">
        <input type="text" id="searchBar" placeholder="Search prescriptions...">
    </div>

    <!-- Prescriptions -->
    <h2>Active Prescriptions</h2>
    <div class="data-container">
        <?php
        if ($numCurrentPrescriptions > 0) {
            while ($currentPrescriptionRow = $currentPrescriptionsResult->fetch_assoc()) {
                echo '<div class="data-box">';
                echo '<h4>' . $currentPrescriptionRow['PrescriptionName'] . '</h4>';
                echo '<p>Dosage: ' . $currentPrescriptionRow['Dosage'] . '</p>';
                echo '<p>Frequency: ' . $currentPrescriptionRow['Frequency'] . '</p>';
                echo '<p>Status: Active</p>';
                // Add more details as needed
                echo '</div>';
            }
        } else {
            echo '<p>No active prescriptions found.</p>';
        }
        ?>
    </div>

    <h2>Past Prescriptions</h2>
    <div class="data-container">
        <?php
        if ($numPastPrescriptions > 0) {
            while ($pastPrescriptionRow = $pastPrescriptionsResult->fetch_assoc()) {
                echo '<div class="data-box">';
                echo '<h4>' . $pastPrescriptionRow['PrescriptionName'] . '</h4>';
                echo '<p>Dosage: ' . $pastPrescriptionRow['Dosage'] . '</p>';
                echo '<p>Frequency: ' . $pastPrescriptionRow['Frequency'] . '</p>';
                echo '<p>Status: Completed</p>';
                // Add more details as needed
                echo '</div>';
            }
        } else {
            echo '<p>No past prescriptions found.</p>';
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
