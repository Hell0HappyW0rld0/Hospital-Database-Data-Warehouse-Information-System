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

$query = "SELECT DoctorName FROM Doctor where doctorID = " . $_SESSION['user_id'];
$result = $mysqli->query($query);

if ($result && $row = $result->fetch_assoc()) {
    $doctorName = $row['DoctorName'];
} else {
    $doctorName = "Doctor Not Found";
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
    <title>Employee Dashboard</title>
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
        <h2>My Patients</h2>
        <div class="data-container">
            <?php
                $patientQuery = "SELECT * FROM Patient";
                $patientResult = $mysqli->query($patientQuery);

                while ($patientRow = $patientResult->fetch_assoc()) {
                    echo '<div class="data-box">';
                    echo '<h3>' . $patientRow['PatientName'] . '</h3>';
                    echo '<p>Disease: ' . $patientRow['Disease'] . '</p>';
                    echo '<p>Admission Date: ' . $patientRow['AdmissionDate'] . '</p>';
                    echo '</div>';
                }

                $patientResult->close();
            ?>
        </div>
    </section>

    <section>
    <h2>Upcoming Appointments</h2>
    <div class="data-container">
        <?php
        $appointmentQuery = "SELECT Appointment.*, Patient.PatientName FROM Appointment JOIN Patient ON Appointment.PatientID = Patient.PatientID";
        $appointmentResult = $mysqli->query($appointmentQuery);

        while ($appointmentRow = $appointmentResult->fetch_assoc()) {
            echo '<div class="data-box">';
            echo '<h3>' . $appointmentRow['Description'] . '</h3>';
            echo '<p>Date: ' . $appointmentRow['AppointmentDate'] . '</p>';
            echo '<p>Status: ' . $appointmentRow['Status'] . '</p>';
            echo '<p>Patient: ' . $appointmentRow['PatientName'] . '</p>';
            echo '</div>';
        }

        $appointmentResult->close();
        ?>
    </div>
</section>

<section>
    <h2>Upcoming Treatments</h2>
    <div class="data-container">
        <?php
            $treatmentQuery = "SELECT Treat.*, Patient.PatientID, Patient.PatientName FROM Treat JOIN Patient ON Treat.PatientID = Patient.PatientID";
            $treatmentResult = $mysqli->query($treatmentQuery);

            while ($treatmentRow = $treatmentResult->fetch_assoc()) {
                echo '<div class="data-box">';
                echo '<h3>Treatment for ' . $treatmentRow['PatientName'] . '</h3>';
                echo '<p>Patient ID: ' . $treatmentRow['PatientID'] . '</p>';
                echo '<p>Treatment: ' . $treatmentRow['Treatment'] . '</p>';
                echo '</div>';
            }

            $treatmentResult->close();
        ?>
    </div>
</section>

</body>
</html>
