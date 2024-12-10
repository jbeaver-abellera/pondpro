<?php
session_start();

// Prevent caching of the page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Check if the user is logged in (if session variables are set)
if (!isset($_SESSION['user_id'])) {
  // If not logged in, redirect to the login page (index.php)
  header("Location: index.php");
  exit();
}

// Database connection (included directly)
$servername = "localhost"; // Database server name
$username = "root";        // Database username
$password = "";            // Database password
$dbname = "pondpro_aquafarms_database"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Initialize result variable
$result = null;

// Fetch data from consumables table if connection is successful
$sql = "SELECT * FROM consumables";
if ($conn) {
  $result = $conn->query($sql);
}


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
  echo "<div class='alert alert-danger' style='
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
  >" . $_SESSION['error_message'] . "</div>";
  // Clear the message after displaying
  unset($_SESSION['error_message']);
}
// Continue with the rest of the page code below
?>


<!DOCTYPE html>
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator - PondPro Aquafarms</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/operator/operator-inventory.css">
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
          <a href="operator-livestocks.php" class="nav-link">
            <span class="nav-icon material-symbols-rounded">analytics</span>
            <span class="nav-label">Livestocks</span>
          </a>
          <span class="nav-tooltip">Livestocks</span>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link nav-list-active">
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
    <h1>Welcome Operator!</h1>
    <p>This is the Inventory Report.</h1>
    
    <div class="inventory-searchbar">
      <input type="search" id="search" name="search" placeholder="Search...">
      <button><i class="fa fa-search searchbar"></i></button>

      <div class="inventory-filter">

          <!-- Add more categories as needed -->
        </select>
      </div>
    </div>
<div class="inventory">
  <h2>Consumables / Non-Consumables Data</h2>
  <!-- Add New Consumables button -->
  <div class="add-button-container">
    <button id="add-new-consumables" class="add-btn">Add New Data</button>
  </div>
  <table class="inventory-table">
    <thead>
      <tr>
          <th>Product ID</th>
          <th>Supply Usage</th>
          <th>Consumables / Non-Consumables Description</th>
          <th>Quantity Available</th>
          <th>Expiration Date</th>
          <th>Actions</th>
      </tr>
    </thead>
    <tbody id="inventory-body">
      <?php
        if ($result && $result->num_rows > 0) {
          // Output data of each row
          while($row = $result->fetch_assoc()) {
            echo "<td>" . $row["product_id"] . "</td>";
            echo "<td>" . $row["supply_usage"] . "</td>";
            echo "<td>" . $row["consumables_description"] . "</td>";
            echo "<td>" . $row["quantity_available"] . "</td>";
            echo "<td>" . $row["expiration_date"] . "</td>";
            
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
        $conn->close();
      ?>
    </tbody>
  </table><br>

    <!-- Modal for Adding Consumables -->
<div id="addModal" class="modal" style="display: none;">
    <div class="modal-content">
        <button class="close-btn" id="closeModal">&times;</button>
        <h2>Add New Data</h2>
        <form action="operator-save-consumables.php" id="addProductForm" method="POST">
            <label for="product-id">Product ID:</label>
            <input type="text" id="product-id" name="product-id" required>

            <label for="usage">Supply Usage</label>
            <select id="usage" name="usage" required>
                <option value="Feeds">Fish Feeds</option>
                <option value="Supplement">Supplement</option>
                <option value="Water Treatment Chemicals">Water Treatment Chemicals</option>
                <option value="Health Products">Health Products</option>
                <option value="Supplies">Supplies</option>
                <option value="Cleaning Products">Cleaning Products</option>
                <option value="Packaging Materials">Packaging Materials</option>
            </select>
                
            <label for="description">Consumables / Non-Consumables Description</label>
            <input type="text" id="description" name="description" required>

            <label for="quantity">Quantity Available</label>
            <input type="number" id="quantity" name="quantity" required>

            <label for="expiration">Expiration Date:</label>
            <input type="date" id="expiration" name="expiration">

            <button type="submit" class="modal-submit-btn">Save</button>
        </form>
</div>



<script src="../../javascript/side-navbar.js"></script>
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

    // Add validation to the modal form
    document.getElementById('addProductForm').addEventListener('submit', function(event) {
        // Validation flags
        var isValid = true;

        // Get form inputs
        var productId = document.getElementById('product-id').value.trim();
        var breed = document.getElementById('breed').value;
        var size = document.getElementById('size').value.trim();
        var weight = document.getElementById('weight').value.trim();
        var dob = document.getElementById('dob').value;
        var dor = document.getElementById('dor').value;
        var expiration = document.getElementById('expiration').value;

        // Validate Product ID
        if (!productId) {
            alert("Product ID is required.");
            isValid = false;
        }

        // Validate Breed selection
        if (!breed) {
            alert("Please select a breed.");
            isValid = false;
        }

        // Validate Size (must not be empty)
        if (!size) {
            alert("Size is required.");
            isValid = false;
        }

        // Validate Weight (must be a positive number)
        if (!weight || isNaN(weight) || weight <= 0) {
            alert("Please enter a valid weight.");
            isValid = false;
        }

        // Validate Date of Birth (must not be in the future)
        var currentDate = new Date();
        var dobDate = new Date(dob);
        if (!dob || dobDate > currentDate) {
            alert("Date of Birth must be a valid date and cannot be in the future.");
            isValid = false;
        }

        // Validate Date of Release (must be later than Date of Birth)
        var dorDate = new Date(dor);
        if (!dor || dorDate <= dobDate) {
            alert("Date of Release must be later than Date of Birth.");
            isValid = false;
        }

        // Validate Expiration Date (must be later than Date of Release)
        var expirationDate = new Date(expiration);
        if (!expiration || expirationDate <= dorDate) {
            alert("Expiration Date must be later than Date of Release.");
            isValid = false;
        }

        // If validation fails, prevent the form submission
        if (!isValid) {
            event.preventDefault(); // Prevent form submission
        }
    });
</script>
<script>
  function editRow(productId) {
  window.location.href = 'operator-edit-consumables.php?id=' + productId;
}

function deleteRow(productId) {
  if (confirm("Are you sure you want to delete Product ID: " + productId + "?")) {
    // Send request to delete endpoint
    fetch('operator-delete-consumables.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: productId })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert("Product deleted successfully.");
          location.reload(); // Refresh the page
        } else {
          alert("Error: " + data.error);
        }
      })
      .catch(err => console.error("Error:", err));
  }
}
</script>
<script>
  // Event listeners for search bar and filter
document.getElementById('search').addEventListener('input', filterTable);
document.getElementById('category-filter').addEventListener('change', filterTable);

// Function to filter table rows based on search input and category filter
function filterTable() {
  var searchInput = document.getElementById('search').value.toLowerCase();
  var categoryFilter = document.getElementById('category-filter').value.toLowerCase();
  var rows = document.querySelectorAll('#inventory-body tr');

  rows.forEach(function(row) {
    var productId = row.cells[0].textContent.toLowerCase();
    var breed = row.cells[1].textContent.toLowerCase();
    var size = row.cells[2].textContent.toLowerCase();
    var weight = row.cells[3].textContent.toLowerCase();
    var dob = row.cells[4].textContent.toLowerCase();
    var dor = row.cells[5].textContent.toLowerCase();
    var expiration = row.cells[6].textContent.toLowerCase();

    var matchesSearch = productId.includes(searchInput) || breed.includes(searchInput) || size.includes(searchInput) || weight.includes(searchInput) || dob.includes(searchInput) || dor.includes(searchInput) || expiration.includes(searchInput);
    var matchesCategory = categoryFilter === 'all' || breed === categoryFilter;

    if (matchesSearch && matchesCategory) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

</script>
<script>
        // JavaScript to handle modal visibility
        document.getElementById('add-new-equipment').addEventListener('click', function() {
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

        // Add validation to the modal form
        document.getElementById('addEquipmentForm').addEventListener('submit', function(event) {
            var isValid = true;

            // Get form inputs
            var equipmentId = document.getElementById('equipment-id').value.trim();
            var equipmentName = document.getElementById('equipment-name').value.trim();
            var equipmentType = document.getElementById('equipment-type').value.trim();
            var equipmentDescription = document.getElementById('equipment-description').value.trim();
            var equipmentQuantity = document.getElementById('equipment-quantity').value.trim();
            var equipmentPrice = document.getElementById('equipment-price').value.trim();

            // Validate Equipment ID
            if (!equipmentId) {
                alert("Equipment ID is required.");
                isValid = false;
            }

            // Validate Equipment Name
            if (!equipmentName) {
                alert("Equipment Name is required.");
                isValid = false;
            }

            // Validate Equipment Type
            if (!equipmentType) {
                alert("Equipment Type is required.");
                isValid = false;
            }

            // Validate Equipment Description
            if (!equipmentDescription) {
                alert("Equipment Description is required.");
                isValid = false;
            }

            // Validate Quantity (must be a positive number)
            if (!equipmentQuantity || isNaN(equipmentQuantity) || equipmentQuantity <= 0) {
                alert("Please enter a valid quantity.");
                isValid = false;
            }

            // Validate Price (must be a positive number)
            if (!equipmentPrice || isNaN(equipmentPrice) || equipmentPrice <= 0) {
                alert("Please enter a valid price.");
                isValid = false;
            }

            // If validation fails, prevent the form submission
            if (!isValid) {
                event.preventDefault();
            }
            
        });
    </script>
<script>
    // Check if the success message exists
    window.onload = function() {
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
    }

// Add an event listener for the search input field to trigger the search on input
document.getElementById('search').addEventListener('input', function() {
  var searchTerm = this.value.trim();
  
  if (searchTerm.length === 0) {
    // If the search field is empty, show all items by fetching the full data set from the server
    showAllConsumables();
  } else {
    // Call the function to search the consumables with the search term
    searchConsumables(searchTerm);
  }
});

function searchConsumables(searchTerm) {
  fetch('operator-search-consumables.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'search=' + encodeURIComponent(searchTerm)
  })
  .then(response => response.text())
  .then(data => {
    document.getElementById("inventory-body").innerHTML = data;
  })
  .catch(error => {
    console.error("Error:", error);
    alert("There was an error with the search request.");
  });
}

// Function to fetch and show all consumables when the search field is cleared
function showAllConsumables() {
  fetch('operator-search-consumables.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'search=' // Send an empty search term to get all results
  })
  .then(response => response.text())
  .then(data => {
    document.getElementById("inventory-body").innerHTML = data;
  })
  .catch(error => {
    console.error("Error:", error);
    alert("There was an error with the request to show all items.");
  });
}


</script>
</body>
</html>
