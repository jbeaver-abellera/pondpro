<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pondpro_aquafarms_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the search term
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';

// SQL query to fetch consumables data
$sql = "SELECT * FROM consumables";
if (!empty($searchTerm)) {
    $sql .= " WHERE product_id LIKE ? 
              OR supply_usage LIKE ? 
              OR consumables_description LIKE ? 
              OR quantity_available LIKE ? 
              OR expiration_date LIKE ?";
}

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

if (!empty($searchTerm)) {
    $searchWildcard = "%" . $conn->real_escape_string($searchTerm) . "%";
    $stmt->bind_param("sssss", $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard);
}

// Execute and fetch results
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["product_id"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["supply_usage"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["consumables_description"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["quantity_available"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["expiration_date"]) . "</td>";
        echo "<td>";
        echo "<button class='edit-btn' onclick='editRow(\"" . htmlspecialchars($row["product_id"]) . "\")'>Edit</button>";
        echo "<button class='delete-btn' onclick='deleteRow(\"" . htmlspecialchars($row["product_id"]) . "\")'>Delete</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No records found</td></tr>";
}

// Close connections
$stmt->close();
$conn->close();
?>
