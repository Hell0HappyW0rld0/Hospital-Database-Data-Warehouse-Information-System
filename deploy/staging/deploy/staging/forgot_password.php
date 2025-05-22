<?php
session_start();

$hostname = "localhost";  
$username = "root";  
$password = "MyNewPass";  
$database_name = "login_db"; 

$db = mysqli_connect($hostname, $username, $password, $database_name);

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $id = $_POST['id'];
    $user_type = $_POST['user_type'];
    $new_password = $_POST['new_password'];

    $query = "UPDATE users SET password = '$new_password' WHERE username = '$username' AND id = '$id' AND user_type = '$user_type'";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_affected_rows($db) > 0) {
        $message = "Password updated successfully.";
    } else {
        $message = "Invalid username, id, or user type.";
    }    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f7f7f7;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .container {
      max-width: 400px;
      padding: 60px;
      padding-top: 0px;
      padding-bottom: 0px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
    }

    .message {
      text-align: center; 
      color: #333; 
      margin-bottom: 20px; 
    }

    h1 {
      text-align: center;
      color: #333;
    }

    h2 {
      text-align: center;
      color: #555;
      margin-bottom: 40px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
      color: #555;
    }

    input[type="text"], input[type="password"], select {
      width: 100%;
      padding: 15px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
      background-color: #f9f9f9;
      transition: border-color 0.3s ease;
      box-sizing: border-box;
    }

    input[type="text"]:focus, input[type="password"]:focus, select:focus {
      border-color: #007bff;
    }

    select {
      height: 50px;
    }

    input[type="submit"] {
      width: 100%;
      background-color: #007bff;
      color: #fff;
      padding: 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      box-sizing: border-box;
    }

    input[type="submit"]:hover {
      background-color: #0056b3;
    }

    .form-links {
      text-align: center;
      margin-top: 10px;
      margin-bottom: 10px;
      color: #777;
    }

    .form-links a {
      margin: 0 10px;
      color: #007bff;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .form-links a:hover {
      color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="container">
  <div class="message"><?php echo $message; ?></div>
    <h1>Elysian Medical Hospital</h1>
    <h2>Reset Password</h2>
    <form method="post" action="forgot_password.php">
      <label for="username">Username:</label>
      <input type="text" name="username" required /><br />

      <label for="id">ID:</label>
      <input type="text" name="id" required /><br />

      <label for="user_type">User Type:</label>
      <select name="user_type">
        <option value="Employee">Employee</option>
        <option value="Patient">Patient</option>
      </select><br />

      <label for="new_password">New Password:</label>
      <input type="password" name="new_password" required /><br />

      <input type="submit" value="Reset Password" />
    </form>
    <div class="form-links">
  <a href="index.php">Back to Login</a>
</div>
  </div>
</body>
</html>
