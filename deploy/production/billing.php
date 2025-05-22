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

// Query to retrieve active bills for the patient
$currentBillsQuery = "SELECT * FROM Billing WHERE PatientID = $patient_id AND PaymentStatus = 'Pending'";
$currentBillsResult = $mysqli->query($currentBillsQuery);

// Query to retrieve past bills for the patient
$pastBillsQuery = "SELECT * FROM Billing WHERE PatientID = $patient_id AND PaymentStatus = 'Paid'";
$pastBillsResult = $mysqli->query($pastBillsQuery);

// Calculate counts
$numCurrentBills = $currentBillsResult->num_rows;
$numPastBills = $pastBillsResult->num_rows;

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="view_patient.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Your Billing</title>
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
    <h2>Your Billing</h2>

    <!-- Search or filter functionality -->
    <div class="search-container">
        <input type="text" id="searchBar" placeholder="Search bills...">
    </div>

    <!-- Bills -->
    <h2>Active Bills</h2>
    <div class="data-container">
        <?php
        if ($numCurrentBills > 0) {
            while ($currentBillRow = $currentBillsResult->fetch_assoc()) {
                echo '<div class="data-box">';
                echo '<h4>Bill ID: ' . $currentBillRow['BillingID'] . '</h4>';
                echo '<p>Amount: $' . $currentBillRow['Amount'] . '</p>';
                echo '<p>Due Date: ' . $currentBillRow['DueDate'] . '</p>';
                echo '<p>Status: Pending</p>';
                // Add more details as needed
                echo '</div>';
            }
        } else {
            echo '<p>No active bills found.</p>';
        }
        ?>
    </div>

    <h2>Past Bills</h2>
    <div class="data-container">
        <?php
        if ($numPastBills > 0) {
            while ($pastBillRow = $pastBillsResult->fetch_assoc()) {
                echo '<div class="data-box">';
                echo '<h4>Bill ID: ' . $pastBillRow['BillingID'] . '</h4>';
                echo '<p>Amount: $' . $pastBillRow['Amount'] . '</p>';
                echo '<p>Due Date: ' . $pastBillRow['DueDate'] . '</p>';
                echo '<p>Status: Paid</p>';
                // Add more details as needed
                echo '</div>';
            }
        } else {
            echo '<p>No past bills found.</p>';
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
