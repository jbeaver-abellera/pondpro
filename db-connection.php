<?php
// Replace with your actual database connection details
$host = 'localhost';
$dbname = 'pondpro_aquafarms_database';
$username = 'root'; // or your database username
$password = ''; // or your database password

try {
    // PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
