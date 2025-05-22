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

$query = "SELECT PatientName FROM Patient WHERE PatientID = " . $_SESSION['user_id'];
$result = $mysqli->query($query);

if ($result && $row = $result->fetch_assoc()) {
    $patientName = $row['PatientName'];
} else {
    $patientName = "Patient Not Found";
}

$result->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="employee.css">
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <title>Patient Dashboard</title>
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
            <div class="welcome-text"><?php echo $patientName; ?>!</div>
        </div>
    </div>
    <div class="nav-links">
        <a class="nav-link" href="patient_dashboard.php">Home <i class="material-icons">home</i></a>
        <a class="nav-link" href="appointments.php">Appointments <i class="material-icons">date_range</i></a>
        <a class="nav-link" href="treatments.php">Treatments <i class="material-icons">vaccines</i></a>
        <a class="nav-link" href="prescriptions.php">Prescriptions <i class="material-icons">receipt</i></a>
        <a class="nav-link" href="billing.php">Billing <i class="material-icons">monetization_on</i></a>
        <a class="nav-link" href="logout.php">Logout <i class="material-icons">exit_to_app</i></a>
    </div>
</nav>

<section>
    <h2>My Appointments</h2>
    <div class="data-container">
        <?php
        $appointmentQuery = "SELECT * FROM Appointment WHERE PatientID = " . $_SESSION['user_id'];
        $appointmentResult = $mysqli->query($appointmentQuery);

        while ($appointmentRow = $appointmentResult->fetch_assoc()) {
            echo '<div class="data-box">';
            echo '<h3>' . $appointmentRow['Description'] . '</h3>';
            echo '<p>Date: ' . $appointmentRow['AppointmentDate'] . '</p>';
            echo '<p>Status: ' . $appointmentRow['Status'] . '</p>';
            echo '</div>';
        }

        $appointmentResult->close();
        ?>
    </div>
</section>

<section>
    <h2>My Treatments</h2>
    <div class="data-container">
        <?php
        $treatmentQuery = "SELECT * FROM Treat WHERE PatientID = " . $_SESSION['user_id'];
        $treatmentResult = $mysqli->query($treatmentQuery);

        while ($treatmentRow = $treatmentResult->fetch_assoc()) {
            echo '<div class="data-box">';
            echo '<h3>Treatment for Patient ' . $treatmentRow['PatientID'] . '</h3>';
            echo '<p>Treatment: ' . $treatmentRow['Treatment'] . '</p>';
            echo '</div>';
        }

        $treatmentResult->close();
        ?>
    </div>
</section>

<section>
    <h2>My Billing Information</h2>
    <div class="data-container">
        <?php
        $billingQuery = "SELECT * FROM Billing WHERE PatientID = " . $_SESSION['user_id'];
        $billingResult = $mysqli->query($billingQuery);

        while ($billingRow = $billingResult->fetch_assoc()) {
            echo '<div class="data-box">';
            echo '<h3>Billing ID: ' . $billingRow['BillingID'] . '</h3>';
            echo '<p>Amount: $' . $billingRow['Amount'] . '</p>';
            echo '<p>Due Date: ' . $billingRow['DueDate'] . '</p>';
            echo '<p>Payment Status: ' . $billingRow['PaymentStatus'] . '</p>';
            echo '</div>';
        }

        $billingResult->close();
        ?>
    </div>
</section>

<section>
    <h2>My Prescriptions</h2>
    <div class="data-container">
        <?php
        $prescriptionQuery = "SELECT * FROM Prescription WHERE PatientID = " . $_SESSION['user_id'];
        $prescriptionResult = $mysqli->query($prescriptionQuery);

        while ($prescriptionRow = $prescriptionResult->fetch_assoc()) {
            echo '<div class="data-box">';
            echo '<h3>' . $prescriptionRow['PrescriptionName'] . '</h3>';
            echo '<p>Dosage: ' . $prescriptionRow['Dosage'] . '</p>';
            echo '<p>Frequency: ' . $prescriptionRow['Frequency'] . '</p>';
            echo '</div>';
        }

        $prescriptionResult->close();
        ?>
    </div>
</section>

</body>
</html>
