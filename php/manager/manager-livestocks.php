<?php
session_start();

// Prevent caching of the page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

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

// Fetch data from livestocks table
$sqlLivestocks = "SELECT * FROM livestocks";
$result = $conn->query($sqlLivestocks);

// Fetch data from livestock_mortality table
$sqlMortality = "SELECT * FROM livestock_mortality";
$resultMortality = $conn->query($sqlMortality);


// Check if there is a success or error message and display it
if (isset($_SESSION['success_message'])) {
  echo "<div id='success-message' class='alert alert-success' style='
    background-color: green; 
    color: white; 
    padding: 10px; 
    margin-bottom: 10px; 
    border-radius: 5px; 
    position: fixed; 
    top: 50%; 
    left: 60%; 
    transform: translate(-50%, -50%); 
    z-index: 9999;
  '>" . $_SESSION['success_message'] . "</div>";
  // Clear the message after displaying
  unset($_SESSION['success_message']);
} elseif (isset($_SESSION['error_message'])) {
  echo "<div id='error-message' class='alert alert-danger' style='
    background-color: red; 
    color: white; 
    padding: 10px; 
    margin-bottom: 10px; 
    border-radius: 5px; 
    position: fixed; 
    top: 50%; 
    left: 60%; 
    transform: translate(-50%, -50%); 
    z-index: 9999;
  '>" . $_SESSION['error_message'] . "</div>";
  // Clear the message after displaying
  unset($_SESSION['error_message']);
}
// Continue with the rest of the page code below
?>


<!DOCTYPE html>
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager - PondPro Aquafarms</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/admin/admin-inventory.css">
    <link rel="icon" href="../../pics/logo-bg.png" type="image/x-icon">
</head>
<body>
  <aside class="sidebar">
    <!-- Sidebar header -->
    <header class="sidebar-header">
      <a href="#" class="header-logo">
        <img src="../../pics/logo-bg.png" alt="PondPro Aquafarms - Logo">
      </a>
      <button class="toggler sidebar-toggler">
        <span class="material-symbols-rounded">chevron_left</span>
      </button>
      <button class="toggler menu-toggler">
        <span class="material-symbols-rounded">menu</span>
      </button>
    </header>

    <nav class="sidebar-nav">
      <!-- Primary top nav -->
      <ul class="nav-list primary-nav">
        <li class="nav-item">
          <a href="manager.php" class="nav-link">
            <span class="nav-icon material-symbols-rounded">dashboard</span>
            <span class="nav-label">Dashboard</span>
          </a>
          <span class="nav-tooltip">Dashboard</span>
        </li>
        <li class="nav-item">
          <a href="manager-livestocks.php" class="nav-link nav-list-active">
            <span class="nav-icon material-symbols-rounded">analytics</span>
            <span class="nav-label">Livestocks</span>
          </a>
          <span class="nav-tooltip">Livestocks</span>
        </li>
        <li class="nav-item">
          <a href="manager-inventory.php" class="nav-link">
            <span class="nav-icon material-symbols-rounded">inventory</span>
            <span class="nav-label">Inventory</span>
          </a>
          <span class="nav-tooltip">Inventory</span>
        </li>
      </ul>

      <!-- Secondary bottom nav -->
      <ul class="nav-list secondary-nav"> 
        <li class="nav-item">
          <a href="../../logout.php" class="nav-link">
            <span class="nav-icon material-symbols-rounded">logout</span>
            <span class="nav-label">Logout</span>
          </a>
          <span class="nav-tooltip">Logout</span>
        </li>
      </ul>
    </nav>
  </aside>
  <div class="main-body">
    <h1>Welcome Manager!</h1>
    <p>This is the Livestocks Report.</h1>
    
    <div class="inventory-searchbar">
      <input type="search" id="search" name="search" placeholder="Search...">
      <button><i class="fa fa-search searchbar"></i></button>

      <div class="inventory-filter">
        <label for="category-filter">Filter by Category:</label>
        <select id="category-filter" name="category-filter">
          <option value="all">All</option>
          <option value="octopus">Octopus</option>
          <option value="crabs">Crabs</option>
          <option value="crayfish">Crayfish</option>
          <!-- Add more categories as needed -->
        </select>
      </div>
    </div>
<div class="inventory">
  <h2>Livestocks Data</h2>
  <!-- Add New Livestocks button -->
  <div class="add-button-container">
    <button id="add-new-consumables" class="add-btn">Add New Livestocks</button>
  </div>
  <table class="inventory-table">
    <thead>
      <tr>
          <th>Breed</th>
          <th>Crayfish Count</th>
          <th>Age</th>
          <th>Tank or Pond Location</th>
          <th>Harvest Date</th>
          <th>Actions</th>
      </tr>
    </thead>
    <tbody id="inventory-body">
      <?php
        if ($result && $result->num_rows > 0) {
          // Output data of each row
          while($row = $result->fetch_assoc()) {
            echo "<tr>"; 
            echo "<td>" . $row["breed"] . "</td>";
            echo "<td>" . $row["crayfish_count"] . "</td>";
            echo "<td>" . $row["age"] . "</td>";
            echo "<td>" . $row["tank_or_pond_location"] . "</td>";
            echo "<td>" . $row["harvest_date"] . "</td>";
            
            // Add Edit and Delete buttons in the last cell
            echo "<td>";
            echo "<button class='edit-btn' onclick='editRow(\"" . $row["product_id"] . "\")'>Edit</button>";
            echo "<button class='delete-btn' onclick='deleteRow(\"" . $row["product_id"] . "\")'>Delete</button>";
            echo "</td>";
            
            echo "</tr>";
          }
        } else {
          // If no data, display "No records found"
          echo "<tr><td colspan='6'>No records found</td></tr>"; 
        }
      ?>
    </tbody>
  </table><br>

  <h2>Mortality Rate per Breed</h2>
  <!-- Motality button -->
  <div class="add-button-container">
    <button id="add-new-consumables" class="add-btn">Add New Data</button>
  </div>
  <table class="inventory-table">
    <thead>
      <tr>
          <th>Breed</th>
          <th>Total Population</th>
          <th>Dead Count</th>
          <th>Mortality Rate (%)</th>
          <th>Actions</th>
      </tr>
    </thead>
    <tbody id="inventory-body">
      <?php
      if ($result && $result->num_rows > 0) {
        while ($row = $resultMortality->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row["breed"] . "</td>";
          echo "<td>" . $row["total_population"] . "</td>";
          echo "<td>" . $row["dead_count"] . "</td>";
          echo "<td>";
          echo "<button class='edit-btn' onclick='editRow(\"" . $row["id"] . "\")'>Edit</button>";
          echo "<button class='delete-btn' onclick='deleteRow(\"" . $row["breed"] . "\")'>Delete</button>";
          echo "</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='6'>No records found</td></tr>";
      }
      ?>
    </tbody>
  </table><br>      
  


    <!-- Modal for Adding Livestocks -->
<div id="addModal" class="modal" style="display: none;">
    <div class="modal-content">
        <button class="close-btn" id="closeModal">&times;</button>
        <h2>Add New Livestocks</h2>
        <form action="manager-save-livestocks.php" id="addProductForm" method="POST">
            <label for="breed">Breed</label>
            <select id="breed" name="breed" required>
                <option value="Electric blue crayfish">Electric blue crayfish (Procambarus alleni)</option>
                <option value="Marbled crayfish">Marbled crayfish (Procambarus virginalis)</option>
                <option value="Australian redclaw crayfish">Australian redclaw crayfish (Cherax quadricarinatus)</option>
                <option value="Red swamp crayfish">Red swamp crayfish (Procambarus clarkii)</option>
                <option value="Common yabby crayfish">Common yabby crayfish (Cherax destructor) </option>
            </select>
            
            <label for="crayfish-count">Crayfish Count:</label>
            <input type="number" id="crayfish-count" name="crayfish-count" required>

            <label for="age-size">Age</label>
            <select id="age-size" name="age-size" required>
                <option value="Juvenile">Juvenile</option>
                <option value="Sub-Adult">Sub-Adult</option>
                <option value="Adult">Adult</option>
            </select>

            <label for="tank-pond">Tank or Pond Location</label>
            <input type="text" id="tank-pond" name="tank-pond" required>

            <label for="harvest-date">Harvest Date</label>
            <input type="date" id="harvest-date" name="harvest-date" required>

            <button type="submit" class="modal-submit-btn">Save</button>
        </form>
</div>



<script src="../../javascript/side-navbar.js"></script>

<!-- Add New Livestocks Modal and Validation -->
<script>
    // JavaScript to handle modal visibility
    document.getElementById('add-new-consumables').addEventListener('click', function() {
        document.getElementById('addModal').style.display = 'flex';
    });

    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('addModal').style.display = 'none';
    });

    // Close the modal when clicking outside of it
    window.addEventListener('click', function(event) {
        var modal = document.getElementById('addModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

  // Function to validate the Add Livestock form
  function validateLivestockForm() {
    var breed = document.getElementById("breed").value;
    var crayfishCount = document.getElementById("crayfish-count").value;
    var ageSize = document.getElementById("age-size").value;
    var tankPond = document.getElementById("tank-pond").value;
    var harvestDate = document.getElementById("harvest-date").value;

    // Validate that all fields are filled out
    if (breed === "" || crayfishCount === "" || ageSize === "" || tankPond === "" || harvestDate === "") {
      alert("All fields must be filled out.");
      return false; // Prevent form submission
    }

    // Validate Crayfish Count (ensure it's a number)
    if (isNaN(crayfishCount) || crayfishCount <= 0) {
      alert("Crayfish count must be a valid number greater than 0.");
      return false; // Prevent form submission
    }

    // If all validations pass, allow form submission
    return true;
  }

  // Attach the validation function to the form's submit event
  document.getElementById('addProductForm').addEventListener('submit', function(event) {
    if (!validateLivestockForm()) {
      event.preventDefault(); // Prevent form submission if validation fails
    }
  });
</script>

<!-- EDIT AND DELETE LIVESTOCKS FUNCTIONALITY -->
<script>
  function editRow(productId) {
  window.location.href = 'manager-edit-livestocks.php?id=' + productId;
}

function deleteRow(breed) {
  const data = { product_id: productId };

// Send the delete request using fetch
fetch('manager-delete-livestocks.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(data),
})
  .then(response => response.text()) // Get the response as text
  .then(responseText => {
      if (responseText === "success") {
          alert("Livestock deleted successfully!");
          // Optionally, remove the deleted item from the UI
      } else if (responseText === "error_deletion") {
          alert("There was an error deleting the livestock.");
      } else if (responseText === "error_logging") {
          alert("There was an error logging the deletion.");
      } else {
          alert("An unexpected error occurred.");
      }
  })
  .catch(error => {
      console.error("Error:", error);
      alert("There was an error with the request.");
  });
  }

</script>
<script>
    // Check if the success message exists
    window.onload = function() {
    // Check if the success message exists
    var successMessage = document.getElementById('success-message');
    if (successMessage) {
        // Set a timeout to fade out the message after 2 seconds
        setTimeout(function() {
            successMessage.style.transition = "opacity 1s"; // Transition effect
            successMessage.style.opacity = "0"; // Fade out
            // Remove the message after fading out
            setTimeout(function() {
                successMessage.style.display = "none"; // Hide the message completely after fade
            }, 1000); // After 1 second
        }, 2000); // Wait for 2 seconds before starting fade-out
    }

    // Check if the error message exists
    var errorMessage = document.getElementById('error-message');
    if (errorMessage) {
        // Set a timeout to fade out the message after 2 seconds
        setTimeout(function() {
            errorMessage.style.transition = "opacity 1s"; // Transition effect
            errorMessage.style.opacity = "0"; // Fade out
            // Remove the message after fading out
            setTimeout(function() {
                errorMessage.style.display = "none"; // Hide the message completely after fade
            }, 1000); // After 1 second
        }, 2000); // Wait for 2 seconds before starting fade-out
    }
};

</script>
</body>
</html>

<?php
// Close the database connection after all operations
$conn->close();
?>