<?php
session_start();

// Database connection setup
$servername = "localhost";  // your database server (usually 'localhost' for XAMPP)
$username = "root";         // your database username
$password = "";             // your database password (default is empty for XAMPP)
$dbname = "pondpro_aquafarms_database"; // the database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check OTP
function checkOtp($otp) {
    global $conn;
    // Query to fetch OTP from the database (assuming column name is expiry_time)
    $query = "SELECT * FROM otp_requests WHERE otp = ? AND expiry_time > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 1; // Returns true if OTP is valid and not expired
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the OTP entered by the user
    $otp = $_POST['otp1'] . $_POST['otp2'] . $_POST['otp3'] . $_POST['otp4'] . $_POST['otp5'] . $_POST['otp6'];

    // Check if OTP is valid
    if (checkOtp($otp)) {
        // OTP is valid, allow user to reset password
        $_SESSION['otp_verified'] = true;
        header('Location: reset-password-page.php'); // Redirect to the password reset page
        exit();
    } else {
        // OTP is invalid, show error message
        $_SESSION['error_message'] = 'Invalid or expired OTP. Please try again.';
        header('Location: OTP-INPUT.php'); // Redirect back to OTP input page
        exit();
    }
}
?>
