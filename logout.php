<?php
// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Get user details from the session
    $userId = $_SESSION['user_id'];

    try {
        // Connect to the database
        $pdo = new PDO('mysql:host=localhost;dbname=pondpro_aquafarms_database', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error handling

        // Update the logout time for the current user in the user_logins table
        // We're updating the logout time where the user_id matches and the logout time is still NULL
        $stmt = $pdo->prepare("UPDATE user_logins SET logout_time = NOW() WHERE user_id = :user_id AND logout_time IS NULL");
        $stmt->execute(['user_id' => $userId]);

        // Destroy the session and log the user out
        session_unset(); // Unsets all session variables
        session_destroy(); // Destroys the session

        // Redirect the user to the index page (login page)
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        // Handle errors if any occur
        echo "Error: " . $e->getMessage();
    }

} else {
    // If no user is logged in, redirect to the login page
    header("Location: index.php");
    exit();
}
?>
