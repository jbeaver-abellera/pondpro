<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
  echo json_encode(["success" => false, "error" => "Unauthorized"]);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Parse the JSON data from the POST request
  $data = json_decode(file_get_contents("php://input"), true);
  $product_id = intval($data['product_id']);
  $deleted_by_role = $_SESSION['role']; // Use the user role instead of the user ID

  // Connect to the database
  $conn = new mysqli("localhost", "root", "", "pondpro_aquafarms_database");

  // Check connection
  if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
    exit();
  }

  // Log the deletion
  $stmt = $conn->prepare(
    "INSERT INTO delete_consumables (product_id, deleted_by_role) VALUES (?, ?)"
  );
  $stmt->bind_param("is", $product_id, $deleted_by_role);
  $stmt->execute();

  // Delete the product from the consumables table
  $stmt = $conn->prepare("DELETE FROM consumables WHERE product_id = ?");
  $stmt->bind_param("i", $product_id);

  if ($stmt->execute()) {
    echo json_encode(["success" => true]);
  } else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
  }

  // Close the statement and connection
  $stmt->close();
  $conn->close();
}
?>
