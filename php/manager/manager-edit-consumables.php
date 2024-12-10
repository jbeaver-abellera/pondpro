<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
  header("Location: index.php");
  exit();
}

// Get product details for editing
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
  $product_id = intval($_GET['id']);

  // Fetch current data
  $conn = new mysqli("localhost", "root", "", "pondpro_aquafarms_database");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $stmt = $conn->prepare("SELECT * FROM consumables WHERE product_id = ?");
  $stmt->bind_param("i", $product_id);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  $conn->close();
}

// Save the edited data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $conn = new mysqli("localhost", "root", "", "pondpro_aquafarms_database");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $product_id = $_POST['product_id'];
  $edited_by_role = $_SESSION['role']; // Use the user's role instead of their ID
  $old_data = json_encode([
    'usage' => $_POST['usage'],
    'description' => $_POST['description'],
    'quantity' => $_POST['quantity'],
    'expiration' => $_POST['expiration']
  ]);

  $new_data = json_encode([
    'usage' => $_POST['usage'],
    'description' => $_POST['description'],
    'quantity' => $_POST['quantity'],
    'expiration' => $_POST['expiration']
  ]);

  // Save the edits in the edit_consumables table
  $stmt = $conn->prepare(
    "INSERT INTO edit_consumables (product_id, edited_by_role, old_data, new_data) VALUES (?, ?, ?, ?)"
  );
  $stmt->bind_param("isss", $product_id, $edited_by_role, $old_data, $new_data);
  $stmt->execute();

    // Update the consumables table
  $stmt = $conn->prepare(
    "UPDATE consumables SET supply_usage = ?, consumables_description = ?, quantity_available = ?, expiration_date = ? WHERE product_id = ?"
  );
  $stmt->bind_param(
    "ssssi",
    $_POST['usage'],
    $_POST['description'],
    $_POST['quantity'],
    $_POST['expiration'],
    $product_id
  );
  $stmt->execute();

  $stmt->close();
  $conn->close();

  // Redirect back to the inventory page
  header("Location: manager-inventory.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Consumables</title>
  <style>
    /* General Styles */
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      margin: 0;
      padding: 0;
    }

    .form-container {
      width: 50%;
      margin: 50px auto;
      background-color: #ffffff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      padding: 20px;
      color: #333;
    }

    .form-container h1 {
      text-align: center;
      font-size: 24px;
      color: #007bff;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
      color: #555;
    }

    .form-group input[type="text"],
    .form-group input[type="date"],
    .form-group input[type="number"] {
      width: 97%;
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .form-group input[type="text"]:focus,
    .form-group input[type="date"]:focus {
      border-color: #007bff;
      outline: none;
    }

    .form-group button {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      background-color: #1fb703;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .form-group button:hover {
      background-color: #189800;
    }

    .form-group .cancel-button {
      background-color: #dc3545;
      margin-top: 10px;
    }

    .form-group .cancel-button:hover {
      background-color: #b02a37;
    }

    .form-group select {
    width: 100%;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    }

    .form-group select:focus {
      border-color: #007bff;
      outline: none;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .form-container {
        width: 90%;
      }
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h1>Edit Consumables</h1>
    <form action="manager-edit-consumables.php" method="POST">
  <input type="hidden" name="product_id" value="<?php echo $result['product_id']; ?>">
  <input type="hidden" name="old_data" value='<?php echo json_encode($result); ?>'>

  <div class="form-group">
    <label for="usage">Supply Usage</label>
    <select id="usage" name="usage" required>
      <option value="Feeds" <?php echo ($result['supply_usage'] == 'Feeds') ? 'selected' : ''; ?>>Fish Feeds and Supplement</option>
      <option value="Water Treatment" <?php echo ($result['supply_usage'] == 'Water Treatment') ? 'selected' : ''; ?>>Water Treatment Chemicals</option>
      <option value="Health Products" <?php echo ($result['supply_usage'] == 'Health Products') ? 'selected' : ''; ?>>Health Products</option>
      <option value="Breeding" <?php echo ($result['supply_usage'] == 'Breeding') ? 'selected' : ''; ?>>Spawning and Breeding Supplies</option>
      <option value="Testing" <?php echo ($result['supply_usage'] == 'Testing') ? 'selected' : ''; ?>>Monitoring and Testing Supplies</option>
      <option value="Cleaning" <?php echo ($result['supply_usage'] == 'Cleaning') ? 'selected' : ''; ?>>Cleaning Products</option>
      <option value="Energy" <?php echo ($result['supply_usage'] == 'Energy') ? 'selected' : ''; ?>>Fuel and Energy</option>
      <option value="Packaging" <?php echo ($result['supply_usage'] == 'Packaging') ? 'selected' : ''; ?>>Packaging Materials</option>
    </select>
  </div>

  <div class="form-group">
    <label for="description">Consumables Description</label>
    <input type="text" id="description" name="description" value="<?php echo $result['consumables_description']; ?>" required>
  </div>

  <div class="form-group">
    <label for="quantity">Quantity Available</label>
    <input type="number" id="quantity" name="quantity" value="<?php echo $result['quantity_available']; ?>" required>
  </div>

  <div class="form-group">
    <label for="expiration">Expiration Date</label>
    <input type="date" id="expiration" name="expiration" value="<?php echo $result['expiration_date']; ?>" required>
  </div>

  <div class="form-group">
    <button type="submit">Save Changes</button>
    <button type="button" class="cancel-button" onclick="window.location.href='manager-inventory.php';">Cancel</button>
  </div>
</form>

  </div>
</body>
</html>

