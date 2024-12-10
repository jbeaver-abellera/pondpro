<?php
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

$id = $_GET['id']; // Assuming you pass the ID of the livestock to delete

// Retrieve the livestock to be deleted
$sql = "SELECT * FROM livestocks WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Insert the data into deleted_livestocks
    $sql_insert = "INSERT INTO deleted_livestocks (breed, crayfish_count, age, tank_or_pond_location, harvest_date, release_date)
                   VALUES ('{$row['breed']}', {$row['crayfish_count']}, {$row['age']}, '{$row['tank_or_pond_location']}', '{$row['harvest_date']}', '{$row['release_date']}')";
    $conn->query($sql_insert);

    // Delete the livestock from the original table
    $sql_delete = "DELETE FROM livestocks WHERE id = $id";
    if ($conn->query($sql_delete) === TRUE) {
        echo "Livestock deleted successfully and archived.";
    } else {
        echo "Error deleting livestock: " . $conn->error;
    }
} else {
    echo "No livestock found with ID: $id";
}

$conn->close();
?>
