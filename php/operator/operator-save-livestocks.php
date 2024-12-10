<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pondpro_aquafarms_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $breed = trim($_POST['breed']);
    $crayfishCount = trim($_POST['crayfish-count']);
    $age = trim($_POST['age-size']);
    $tankLocation = trim($_POST['tank-pond']);
    $harvestDate = trim($_POST['harvest-date']);
    $savedBy = $_SESSION['role']; // e.g., Admin or Operator

    // Validate input
    if (empty($breed) || empty($crayfishCount) || empty($age) || empty($tankLocation) || empty($harvestDate)) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: operator-livestocks.php");
        exit();
    }

    if (!is_numeric($crayfishCount) || $crayfishCount <= 0) {
        $_SESSION['error_message'] = "Crayfish count must be a positive number.";
        header("Location: operator-livestocks.php");
        exit();
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO livestocks (breed, crayfish_count, age, tank_or_pond_location, harvest_date, saved_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $breed, $crayfishCount, $age, $tankLocation, $harvestDate, $savedBy);


    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Livestock data added successfully.";
    } else {
        $_SESSION['error_message'] = "Error saving data: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: operator-livestocks.php");
    exit();
} else {
    header("Location: operator-livestocks.php");
    exit();
}
?>
