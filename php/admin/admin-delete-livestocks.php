<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate the input
    if (!isset($data['product_id']) || !is_numeric($data['product_id'])) {
        echo json_encode(["success" => false, "error" => "Invalid product ID."]);
        exit();
    }

    $product_id = intval($data['product_id']);
    $deleted_by_role = $_SESSION['role'];

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "pondpro_aquafarms_database");

    if ($conn->connect_error) {
        echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
        exit();
    }

    // Log the deletion
    $stmt = $conn->prepare("INSERT INTO delete_livestocks (product_id, deleted_by_role) VALUES (?, ?)");
    $stmt->bind_param("is", $product_id, $deleted_by_role);
    $stmt->execute();

    if ($stmt->error) {
        echo json_encode(["success" => false, "error" => "Failed to log the deletion: " . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit();
    }

    // Delete the product from the livestocks table
    $stmt = $conn->prepare("DELETE FROM livestocks WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete the livestock record."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
}
?>
