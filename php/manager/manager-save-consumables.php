<?php
// Start session
session_start();

// Check if the user is logged in and is an manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../php/index.php");
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pondpro_aquafarms_database";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST data
    $product_id = $_POST['product-id'];
    $supply_usage = $_POST['usage'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $expiration_date = $_POST['expiration'];  // Ensure the name matches the form field
    
    // Get the role of the logged-in user
    $created_by_role = $_SESSION['role'];  // This should be 'manager' if the user is an manager

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO consumables (product_id, supply_usage, consumables_description, quantity_available, expiration_date, created_by_role) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("ssssss", $product_id, $supply_usage, $description, $quantity, $expiration_date, $created_by_role);

        if ($stmt->execute()) {
            // Set success message in session
            $_SESSION['success_message'] = "Record added successfully!";
        } else {
            error_log("Error: " . $stmt->error); // Log error
            $_SESSION['error_message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        error_log("Error: " . $conn->error); // Log error
        $_SESSION['error_message'] = "Error: " . $conn->error;
    }
}

$conn->close();

// Redirect to the Manager inventory page
header("Location: manager-inventory.php");
exit();
?>
