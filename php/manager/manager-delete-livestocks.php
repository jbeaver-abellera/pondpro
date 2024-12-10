<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pondpro_aquafarms_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if product_id is set in POST request
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // SQL query to delete the record from the 'livestocks' table
    $sql_delete = "DELETE FROM livestocks WHERE product_id = ?";
    
    // Prepare and bind the delete statement
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $product_id);
        if ($stmt_delete->execute()) {

            // Now log the deletion in the 'delete_livestocks' table
            $deleted_by_role = $_SESSION['user_role']; // Assuming the role is stored in session (admin, manager, operator)
            $sql_insert = "INSERT INTO delete_livestocks (product_id, deleted_by_role, delete_time) VALUES (?, ?, NOW())";
            
            // Prepare and bind the insert statement
            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param("is", $product_id, $deleted_by_role); // 's' for string (role)
                if ($stmt_insert->execute()) {
                    echo "success"; // Deletion and logging were successful
                } else {
                    echo "error_logging"; // Error logging the deletion
                }
                $stmt_insert->close();
            } else {
                echo "error_prepare_logging"; // Error preparing insert query
            }

        } else {
            echo "error_deletion"; // Error in deletion
        }
        $stmt_delete->close();
    } else {
        echo "error_prepare_deletion"; // Error preparing delete query
    }
}

$conn->close();
?>
