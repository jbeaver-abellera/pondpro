<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PondPro Aquafarms Reset Password</title>
  <link rel="icon" type="image/jpg" href="pics/logo-bg.jpg">
  <link rel="stylesheet" href="css/index.css">
  <!-- Add Font Awesome CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="form-container">
      <h1 class="title">PondPro Aquafarms Reset Password</h1><br>
      <form action="reset-password.php" method="POST" id="resetForm">
        <div class="input-group">
          <input type="text" id="password" name="password" required>
          <label for="password">New Password:</label>
        </div>
        <div class="input-group password-field">
          <input type="password" id="password-confirm" name="password-confirm" required>
          <label for="password">Repeat Password:</label>
          <!-- Font Awesome eye icon inside the input field -->
          <i class="fas fa-eye-slash toggle-password" id="togglePassword"></i>
        </div>
        <button type="submit" class="btn">Reset Password</button>
      </form>
    </div>
  </div>

  <script>
    // Password visibility toggle
    const passwordInput = document.getElementById("password-confirm");
    const togglePassword = document.getElementById("togglePassword");

    togglePassword.addEventListener("click", () => {
      // Toggle the type attribute
      const type = passwordInput.type === "password" ? "text" : "password";
      passwordInput.type = type;

      // Toggle the icon
      togglePassword.classList.toggle("fa-eye");
      togglePassword.classList.toggle("fa-eye-slash");
    });

    // Form validation
    document.getElementById("resetForm").addEventListener("submit", function(event) {
      var password = document.getElementById("password").value;
      var passwordConfirm = document.getElementById("password-confirm").value;

      // Check if passwords match
      if (password !== passwordConfirm) {
        alert("Passwords do not match!");
        event.preventDefault();
        return false;
      }

      // Check if password length is between 8 and 15 characters
      if (password.length < 8 || password.length > 15) {
        alert("Password must be between 8 to 15 characters long.");
        event.preventDefault();
        return false;
      }

      // Check if password contains at least one number
      if (!/\d/.test(password)) {
        alert("Password must contain at least one number.");
        event.preventDefault();
        return false;
      }

      // Check if password contains at least one uppercase letter
      if (!/[A-Z]/.test(password)) {
        alert("Password must contain at least one uppercase letter.");
        event.preventDefault();
        return false;
      }

      // Check if password contains at least one lowercase letter
      if (!/[a-z]/.test(password)) {
        alert("Password must contain at least one lowercase letter.");
        event.preventDefault();
        return false;
      }

      // Check if password contains any invalid special characters (allowing only underscores)
      if (/[^A-Za-z0-9_]/.test(password)) {
        alert("Password must not contain any special characters except for underscores (_).");
        event.preventDefault();
        return false;
      }

      return true;
    });
  </script>
</body>
</html>
