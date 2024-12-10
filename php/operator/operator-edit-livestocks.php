<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "pondpro_aquafarms_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$result = [];

// Get product details for editing
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $product_id = intval($_GET['id']);

        // Fetch current data
        $stmt = $conn->prepare("SELECT * FROM livestocks WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } else {
        echo "No ID provided in the URL.";
    }
}

// Save the edited data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $edited_by_role = $_SESSION['role']; // User's role
    $old_data = $_POST['old_data'];

    // Capture updated values for new_data
    $new_data = json_encode([
        'breed' => $_POST['breed'],
        'crayfish_count' => $_POST['crayfish_count'],
        'age' => $_POST['age'],
        'tank_or_pond_location' => $_POST['tank_or_pond_location'],
        'harvest_date' => $_POST['harvest_date'],
        'release_date' => $_POST['release_date'],
    ]);

    // Save the edits in the edit_livestocks table
    $stmt = $conn->prepare(
        "INSERT INTO edit_livestocks (product_id, edited_by_role, old_data, new_data) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("isss", $product_id, $edited_by_role, $old_data, $new_data);
    $stmt->execute();

    // Update the livestocks table
    $stmt = $conn->prepare(
        "UPDATE livestocks SET breed = ?, crayfish_count = ?, age = ?, tank_or_pond_location = ?, harvest_date = ?, release_date = ? WHERE product_id = ?"
    );
    $stmt->bind_param(
        "sissssi",
        $_POST['breed'],
        $_POST['crayfish_count'],
        $_POST['age'],
        $_POST['tank_or_pond_location'],
        $_POST['harvest_date'],
        $_POST['release_date'],
        $product_id
    );
    $stmt->execute();

    $stmt->close();
    $conn->close();

    // Redirect back to the livestocks page
    header("Location: operator-livestocks.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Livestock</title>
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
        <?php if (!empty($result)) : ?>
            <h1>Edit Livestock</h1>
            <form action="operator-edit-livestocks.php" method="POST">
                <!-- Hidden input to pass product_id -->
                <input type="hidden" name="product_id" value="<?php echo $result['product_id']; ?>">
                <input type="hidden" name="old_data" value="<?php echo htmlspecialchars(json_encode($result), ENT_QUOTES, 'UTF-8'); ?>">

                <!-- Breed -->
                <div class="form-group">
                    <label for="breed">Breed:</label>
                    <input type="text" id="breed" name="breed" value="<?php echo htmlspecialchars($result['breed']); ?>" required>
                </div>

                <!-- Crayfish Count -->
                <div class="form-group">
                    <label for="crayfish_count">Crayfish Count:</label>
                    <input type="number" id="crayfish_count" name="crayfish_count" value="<?php echo htmlspecialchars($result['crayfish_count']); ?>" required>
                </div>

                <!-- Age -->
                <div class="form-group">
                    <label for="age">Age:</label>
                        <select id="age" name="age" required>
                             <option value="Juvenile" <?php echo ($result['age'] == 'Juvenile') ? 'selected' : ''; ?>>Juvenile</option>
                             <option value="Sub-Adult" <?php echo ($result['age'] == 'Sub-Adult') ? 'selected' : ''; ?>>Sub-Adult</option>
                             <option value="Adult" <?php echo ($result['age'] == 'Adult') ? 'selected' : ''; ?>>Adult</option>
                         </select>
                </div>

                <!-- Tank or Pond Location -->
                <div class="form-group">
                    <label for="tank_or_pond_location">Tank or Pond Location:</label>
                    <input type="text" id="tank_or_pond_location" name="tank_or_pond_location" value="<?php echo htmlspecialchars($result['tank_or_pond_location']); ?>" required>
                </div>

                <!-- Harvest Date -->
                <div class="form-group">
                    <label for="harvest_date">Harvest Date:</label>
                    <input type="date" id="harvest_date" name="harvest_date" value="<?php echo htmlspecialchars($result['harvest_date']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="release_date">Released Date:</label>
                    <input type="date" id="release_date" name="release_date" value="<?php echo htmlspecialchars($result['release_date']); ?>">
                </div>

                <!-- Buttons -->
                <div class="form-group">
                    <button type="submit" class="save-button">Save Changes</button>
                    <a href="operator-livestocks.php">
                        <button type="button" class="cancel-button">Cancel</button>
                    </a>
                </div>
            </form>
        <?php else : ?>
            <p>No data found for the specified ID.</p>
        <?php endif; ?>
    </div>
</body>
</html>
