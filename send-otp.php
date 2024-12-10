<?php
session_start(); // Start the session

// Database connection
$servername = "localhost"; // Your database server
$username = "root"; // Your database username (default is root)
$password = ""; // Your database password (default is empty)
$dbname = "pondpro_aquafarms_database"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the email from the form
$email = $_POST['email'];

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format";
    exit();
}

// Store email in session for later use
$_SESSION['email'] = $email;

// Check if the email exists in the user_accounts table
$sql = "SELECT role FROM user_accounts WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email); // "s" means the parameter is a string
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Email exists, get the role
    $user = $result->fetch_assoc();
    $role = $user['role']; // Assuming role is stored as 'manager', 'operator', or 'admin'

    // Generate OTP
    $otp = rand(100000, 999999);

    // Set timezone to Asia/Manila (adjust for the correct timezone)
    $timezone = new DateTimeZone('Asia/Manila');
    $current_time = new DateTime('now', $timezone); // Get the current time in Asia/Manila timezone
    $expiry_time = $current_time->modify('+5 minutes')->format('Y-m-d H:i:s'); // Add 5 minutes

    // Store OTP and expiry time in the database
    $sql = "INSERT INTO otp_requests (email, otp, expiry_time) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $email, $otp, $expiry_time); // "sis" means string, integer, string
    $stmt->execute();

    // Store OTP in session (you can also store it in the database with an expiry time)
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = $expiry_time; // Store expiry time in session for later comparison

    // Use PHPMailer to send the OTP email
    require 'vendor/autoload.php'; // Make sure PHPMailer is included

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'phpmailer572@gmail.com'; // Your Gmail address
    $mail->Password = 'ryuohvhdxnmaaqhn'; // Your Gmail password
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('noreply_pondpro_aquafarms@gmail.com', 'PondPro Aquafarms');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Reset Your Password';
    $mail->Body = "
        <html>
        <head>
            <style>
                body,p { color: black; font-family: Arial, sans-serif; }
                h2 { color: #4CAF50; }
            </style>
        </head>
        <body>
            <p>Hello, $role!</p>
            <p>We received a request to reset your password.</p>
            <p>Use the following OTP:</p>
            <h2>$otp</h2>
            <p>This OTP will expire at 5 minutes.</p>
            <p>If you didn’t request this, please ignore this email.</p>
            <p>© 2024 PondPro Aquafarms. All rights reserved.</p>
        </body>
        </html>
    ";

    // Send the email
    if ($mail->send()) {
        echo "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                }
                .success-message {
                    color: green;
                    font-size: 16px;
                }
            </style>
        </head>
        <body>
            <p class='success-message'>OTP sent successfully to your email.</p>
            <p> Click <a href='OTP-INPUT.php'>here</a> to reset your password.</p>
        </body>
        </html>";
    } else {
        echo "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                }
                .error-message {
                    color: red;
                    font-size: 16px;
                }
            </style>
        </head>
        <body>
            <p class='error-message'>Mailer Error: " . $mail->ErrorInfo . "</p>
        </body>
        </html>";
    }
} else {
    // Email doesn't exist, set error message in session and redirect back
    $_SESSION['error_message'] = "The email address is not registered.";
    header('Location: forgot-password.php');
}

// Close the database connection
$stmt->close();
$conn->close();
?>
