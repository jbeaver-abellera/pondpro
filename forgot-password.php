<?php
session_start(); // Start the session

// Check if there's an error message in the session
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
// Clear the error message after displaying it
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="icon" type="image/jpg" href="pics/logo-bg.jpg">
  <link rel="stylesheet" href="css/forgot-password.css">
  <!-- Add Font Awesome CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

  <!-- Display error message if it exists -->
  <?php if ($error_message): ?>
    <div class="error-message">
      <?php echo $error_message; ?>
    </div>
  <?php endif; ?>

  <div class="container">
    <div class="form-container">
      <h1 class="title">PondPro Aquafarms Forgot Password</h1><br>
      <p class="message">Enter your email to reset your password.</p><br><br>
      <form action="send-otp.php" method="POST">
        <div class="input-group">
          <input type="email" id="email" name="email" required 
                 pattern="^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com)$" 
                 title="Please enter a valid Gmail or Yahoo email address (e.g., user@gmail.com or user@yahoo.com)">
          <label for="email">Email</label>
        </div>
        <button type="submit" class="btn">Send OTP</button>
        <a href="index.php" class="forgot-password">Go Back</a>
      </form>
    </div>
  </div>

  <script>
    window.onload = function() {
      const errorMessage = document.querySelector('.error-message');
      if (errorMessage) {
        // Wait 2 seconds before starting the fade-out effect
        setTimeout(function() {
          errorMessage.classList.add('fade-out');
        }, 2000); // Adjust this time if needed
      }
    };
  </script>
  
</body>
</html>
