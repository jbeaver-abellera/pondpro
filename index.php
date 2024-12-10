<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="icon" type="image/jpg" href="pics/logo-bg.jpg">
  <link rel="stylesheet" href="css/index.css">
  <!-- Add Font Awesome CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="form-container">
      <h1 class="title">PondPro Aquafarms Login</h1>
      <form action="login.php" method="POST">
        <div class="input-group">
          <input type="email" id="email" name="email" required 
                 pattern="^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com)$" 
                 title="Please enter a valid Gmail or Yahoo email address (e.g., user@gmail.com or user@yahoo.com)">
          <label for="email">Email</label>
        </div>
        <div class="input-group password-field">
          <input type="password" id="password" name="password" required>
          <label for="password">Password</label>
          <!-- Font Awesome eye icon inside the input field -->
          <i class="fas fa-eye-slash toggle-password" id="togglePassword"></i>
        </div>
        <button type="submit" class="btn">Login</button>
        <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
      </form>
    </div>
  </div>

  <script>
    // Password visibility toggle
    const passwordInput = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");

    togglePassword.addEventListener("click", () => {
      // Toggle the type attribute
      const type = passwordInput.type === "password" ? "text" : "password";
      passwordInput.type = type;

      // Toggle the icon
      togglePassword.classList.toggle("fa-eye");
      togglePassword.classList.toggle("fa-eye-slash");
    });

    // Optional: Add a simple loading animation on form submit
    document.querySelector("form").addEventListener("submit", (e) => {
      const button = document.querySelector(".btn");
      button.textContent = "Logging in...";
      button.disabled = true;
      setTimeout(() => {
        button.textContent = "Login";
        button.disabled = false;
      }, 2000);
    });
  </script>
</body>
</html>
