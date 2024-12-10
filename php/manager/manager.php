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

// Database connection
$host = 'localhost';
$db = 'pondpro_aquafarms_database';
$user = 'root';
$pass = ''; // Use your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch data from consumables table
$stmt = $pdo->query("SELECT * FROM consumables ORDER BY created_at DESC");
$consumables = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch data from livestocks table
$stmt = $pdo->query("SELECT * FROM livestocks ORDER BY created_at DESC");
$livestocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total stock counts for pie chart data
$consumableCounts = [];
foreach ($consumables as $consumable) {
    $description = $consumable['consumables_description'];
    if (!isset($consumableCounts[$description])) {
        $consumableCounts[$description] = 0;
    }
    $consumableCounts[$description] += $consumable['quantity_available'];
}

$livestockCounts = [];
foreach ($livestocks as $livestock) {
    $breed = $livestock['breed'];
    if (!isset($livestockCounts[$breed])) {
        $livestockCounts[$breed] = 0;
    }
    $livestockCounts[$breed] += $livestock['crayfish_count'];
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager - PondPro Aquafarms</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../../css/admin/admin.css">
  <link rel="icon" href="../../pics/logo-bg.jpg" type="image/x-icon">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script> <!-- Chart.js Data Labels Plugin -->
</head>
<body>
  <aside class="sidebar">
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
      <ul class="nav-list primary-nav">
        <li class="nav-item">
          <a href="#" class="nav-link nav-list-active">
            <span class="nav-icon material-symbols-rounded">dashboard</span>
            <span class="nav-label">Dashboard</span>
          </a>
          <span class="nav-tooltip">Dashboard</span>
        </li>

      </ul>

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
    <center>
      <h1>Welcome Manager!</h1>
      <p>This is today's report</p>
    </center>

    <!-- Consumables Chart -->
    <h2>Inventory Data</h2>
    <div style="width: 50%; margin: auto;">
      <canvas id="consumablesChart"></canvas>
    </div>

    <!-- Consumables Data Table -->
    <center>
      <table>
        <thead>
          <tr>
            <th>Product ID</th>
            <th>Supply Usage</th>
            <th>Consumables Description</th>
            <th>Quantity Available</th>
            <th>Expiration Date</th>
            <th>Saved At</th>
            <th>Saved By (Role)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($consumables as $consumable): ?>
          <tr>
            <td><?php echo htmlspecialchars($consumable['product_id']); ?></td>
            <td><?php echo htmlspecialchars($consumable['supply_usage']); ?></td>
            <td><?php echo htmlspecialchars($consumable['consumables_description']); ?></td>
            <td><?php echo htmlspecialchars($consumable['quantity_available']); ?></td>
            <td><?php echo htmlspecialchars($consumable['expiration_date']); ?></td>
            <td><?php echo htmlspecialchars($consumable['created_at']); ?></td>
            <td><?php echo htmlspecialchars($consumable['created_by_role']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </center>

    <!-- Livestocks Chart -->
    <h2>Livestocks Data</h2>
    <div style="width: 50%; margin: auto;">
      <canvas id="livestocksChart"></canvas>
    </div>

    <!-- Livestocks Data Table -->
    <center>
      <table>
        <thead>
          <tr>
            <th>Breed</th>
            <th>Crayfish Count</th>
            <th>Age</th>
            <th>Tank or Pond Location</th>
            <th>Harvest Date</th>
            <th>Saved By</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($livestocks as $livestock): ?>
          <tr>
            <td><?php echo htmlspecialchars($livestock['breed']); ?></td>
            <td><?php echo htmlspecialchars($livestock['crayfish_count']); ?></td>
            <td><?php echo htmlspecialchars($livestock['age']); ?></td>
            <td><?php echo htmlspecialchars($livestock['tank_or_pond_location']); ?></td>
            <td><?php echo htmlspecialchars($livestock['harvest_date']); ?></td>
            <td><?php echo htmlspecialchars($livestock['saved_by']); ?></td>
            <td><?php echo htmlspecialchars($livestock['created_at']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </center>

    <script>
      // Consumables Chart
      const consumablesCtx = document.getElementById('consumablesChart').getContext('2d');
      const consumablesChart = new Chart(consumablesCtx, {
        type: 'pie',
        data: {
          labels: <?php echo json_encode(array_keys($consumableCounts)); ?>,
          datasets: [{
            data: <?php echo json_encode(array_values($consumableCounts)); ?>,
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FFC107', '#E91E63'],
            borderColor: '#fff',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: true,
              position: 'top'
            },
            datalabels: {
              display: true,
              formatter: function(value, context) {
                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                let percentage = ((value / total) * 100).toFixed(1) + '%';
                return percentage;
              },
              color: '#000',
              font: {
                weight: 'bold'
              }
            },
            tooltip: {
              callbacks: {
                label: function(tooltipItem) {
                  return tooltipItem.label + ': ' + tooltipItem.raw;
                }
              }
            }
          }
        }
      });

      // Livestocks Chart
      const livestocksCtx = document.getElementById('livestocksChart').getContext('2d');
      const livestocksChart = new Chart(livestocksCtx, {
        type: 'pie',
        data: {
          labels: <?php echo json_encode(array_keys($livestockCounts)); ?>,
          datasets: [{
            data: <?php echo json_encode(array_values($livestockCounts)); ?>,
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FFC107', '#E91E63'],
            borderColor: '#fff',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: true,
              position: 'top'
            },
            datalabels: {
              display: true,
              formatter: function(value, context) {
                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                let percentage = ((value / total) * 100).toFixed(1) + '%';
                return percentage;
              },
              color: '#000',
              font: {
                weight: 'bold'
              }
            },
            tooltip: {
              callbacks: {
                label: function(tooltipItem) {
                  return tooltipItem.label + ': ' + tooltipItem.raw;
                }
              }
            }
          }
        }
      });
    </script>

    <script src="../../javascript/side-navbar.js"></script>
  </body>
</html>
