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

// Retrieve search term
$searchTerm = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';

// Query to search for consumables
$sql = "SELECT * FROM consumables WHERE 
        product_id LIKE '%$searchTerm%' OR 
        supply_usage LIKE '%$searchTerm%' OR 
        consumables_description LIKE '%$searchTerm%' OR 
        quantity_available LIKE '%$searchTerm%' OR 
        expiration_date LIKE '%$searchTerm%'";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // Output data of each row
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

$conn->close();
?>
