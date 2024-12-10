<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pondpro_aquafarms_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for POST request and search term
$data = json_decode(file_get_contents('php://input'), true);
$searchTerm = $conn->real_escape_string($data['search']);

if ($searchTerm) {
    $sqlLivestocks = "SELECT * FROM livestocks WHERE breed LIKE '%$searchTerm%' OR age LIKE '%$searchTerm%' OR tank_or_pond_location LIKE '%$searchTerm%'";
    $result = $conn->query($sqlLivestocks);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["breed"] . "</td>";
            echo "<td>" . $row["crayfish_count"] . "</td>";
            echo "<td>" . $row["age"] . "</td>";
            echo "<td>" . $row["tank_or_pond_location"] . "</td>";
            echo "<td>" . $row["harvest_date"] . "</td>";
            echo "<td>";
            echo "<button class='edit-btn' onclick='editRow(\"" . $row["product_id"] . "\")'>Edit</button>";
            echo "<button class='delete-btn' onclick='deleteRow(\"" . $row["product_id"] . "\")'>Delete</button>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No matching records found</td></tr>";
    }
} else {
    echo "<tr><td colspan='6'>Invalid search query</td></tr>";
}

$conn->close();
?>
