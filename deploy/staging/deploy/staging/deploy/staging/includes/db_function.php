
<?php

    // Connection to server
    $servername = "php_docker";
    $username = "php_docker";
    $password = "password";
    $dbname = "php_docker";

    // Create connection, use global to execute db queries
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);      # Show error code
    }

    // Seperate query to reduce error redundancy

    // Function to execute SELECT query
    function executeSelectQuery($query) {
        global $conn;
        $result = $conn->query($query);
        
        if ($result === false) {
            die("Error executing query: " . $conn->error);
        }

        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    // Function to execute INSERT, UPDATE, or DELETE query
    function executeNonQuery($query) {
        global $conn;
        $result = $conn->query($query);

        if ($result === false) {
            die("Error executing query: " . $conn->error);
        }

        return $result;
    }


    // Function to get all Patients
    function getAllPatients() {
        $query = "SELECT * FROM Patient";
        return executeSelectQuery($query);
    }

    // Function to insert a new Patient
    function insertPatient($patientName, $phoneNum, $disease, $admissionDate, $dischargeDate, $totalDays) {
        $query = "INSERT INTO Patient (PatientName, PatientPhoneNum, Disease, AdmissionDate, DischargeDate, TotalDays) 
                VALUES ('$patientName', '$phoneNum', '$disease', '$admissionDate', '$dischargeDate', $totalDays)";
        return executeNonQuery($query);
    }

    // Function to update Patient information WITH SQL prevention in mind
    function updatePatient($patientID, $patientName, $phoneNum, $disease) {
        global $conn;

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE Patient SET PatientName=?, PatientPhoneNum=?, Disease=? WHERE PatientID=?");

        // Bind parameters to the prev statement
        $stmt->bind_param("sssi", $patientName, $phoneNum, $disease, $patientID);

        // Execute the update
        if ($stmt->execute()) {
            // Update successful
            $stmt->close();
            return true;
        } else {
            // Update failed, print out error code too
            die("Error updating patient: " . $stmt->error);
        }
    }

    // Function to get Patient by PatientID 
    function getPatientByID($patientID) {
        $query = "SELECT * FROM Patient WHERE PatientID = $patientID";
        return executeSelectQuery($query);

        /* WITH SQL prevention in mind
        global $conn;

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM Patient WHERE PatientID = ?");
        $stmt->bind_param("i", $patientID);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        $stmt->close();

        return $data;
        */
    }

    // Function to count total no. of Patient
    function countPatients() {
        // Construct and execute a SELECT query with COUNT...
        
    }



    // Function to get all Doctors
    function getAllDoctors() {
        $query = "SELECT * FROM Doctor";
        return executeSelectQuery($query);
    }

    // Function to get all Appointment for a Patient
    function getAppointmentsForPatient($patientID) {
        $query = "SELECT * FROM Appointment WHERE PatientID = $patientID";
        return executeSelectQuery($query);
    }

    // Function go delete Appointment based on AppointmentID
    function deleteAppointment($appointmentID) {
        $query = "DELETE FROM Appointment WHERE AppointmentID = $appointmentID";
        return executeNonQuery($query);
    }


    // Pagination: retrieve subset of records at a time, prevent large no. of record being push out at once
    // Should we sort in PHP (more option/flexible later on?) or MySQL (faster)
?>
