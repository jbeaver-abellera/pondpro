<?php
session_start();

// Check if there's an error message set in the session
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear the error message after displaying it
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OTP Verification</title>
  <link rel="icon" type="image/jpg" href="pics/logo-bg.jpg">
  <link rel="stylesheet" href="css/OTP-INPUT.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="form-container">
      <h1 class="title">Enter OTP to Reset Password</h1>

      <!-- Display error message if it exists -->
      <?php if ($error_message): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <form action="verify-otp.php" method="POST">
        <div class="otp-container">
          <input type="text" id="otp1" name="otp1" maxlength="1" required class="otp-input" autofocus>
          <input type="text" id="otp2" name="otp2" maxlength="1" required class="otp-input">
          <input type="text" id="otp3" name="otp3" maxlength="1" required class="otp-input">
          <input type="text" id="otp4" name="otp4" maxlength="1" required class="otp-input">
          <input type="text" id="otp5" name="otp5" maxlength="1" required class="otp-input">
          <input type="text" id="otp6" name="otp6" maxlength="1" required class="otp-input">
        </div>
        <button type="submit" class="btn">Verify OTP</button>
        <a href="forgot-password.php" class="forgot-password">Back to Forgot Password</a>
      </form>
    </div>
  </div>

  <script>
    // JavaScript to handle input focusing
    const otpInputs = document.querySelectorAll('.otp-input');

    otpInputs.forEach((input, index) => {
      input.addEventListener('input', (e) => {
        if (e.target.value.length === 1 && index < otpInputs.length - 1) {
          otpInputs[index + 1].focus(); // Move to next input
        }
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && index > 0 && e.target.value.length === 0) {
          otpInputs[index - 1].focus(); // Move to previous input on backspace
        }
      });
    });
  </script>
</body>
</html>
