<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: forgot-password.php'); // Redirect to forgot password if email is not found in session
    exit();
}

$email = $_SESSION['email']; // Retrieve email from session
$newPassword = $_POST['password'];

// Hash the password before updating in the database
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Database connection details
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "pondpro_aquafarms_database"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update password in the database
$sql = "UPDATE user_accounts SET password = ? WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashedPassword, $email);
$stmt->execute();

// Send email notification about password change
require 'vendor/autoload.php'; // Make sure PHPMailer is included

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'phpmailer572@gmail.com';
$mail->Password = 'ryuohvhdxnmaaqhn';
$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->setFrom('noreply_pondpro_aquafarms@gmail.com', 'PondPro Aquafarms');
$mail->addAddress($email);

$mail->isHTML(true);
$mail->Subject = 'Your Password Has Been Reset';
$mail->Body = "
    <html>
    <head>
        <style>
            body,p { color: black; font-family: Arial, sans-serif; }
            h2 { color: #4CAF50; }
        </style>
    </head>
    <body>
        <p>Your password has been successfully reset!</p>
        <p>You can now log in <a href='http://localhost/PondPro-Aquafarms/index.php'>here</a> with your new password</p>
        <p>If you did not make this request, please contact support immediately.</p>
        <p>© 2024 PondPro Aquafarms. All rights reserved.</p>
    </body>
    </html>
";

// Send the email
if ($mail->send()) {
    echo "<p>Password reset successful! A confirmation email has been sent.</p>";
} else {
    echo "<p>Mailer Error: " . $mail->ErrorInfo . "</p>";
}

// Close connection
$stmt->close();
$conn->close();
?>
