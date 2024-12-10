<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=pondpro_aquafarms_database', 'root', ''); // Adjust credentials if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT * FROM user_accounts WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Valid credentials, set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Log login time with user_id and role
        $stmt = $pdo->prepare("INSERT INTO user_logins (user_id, role, login_time) VALUES (:user_id, :role, NOW())");
        $stmt->execute([
            'user_id' => $user['id'],
            'role' => $user['role']
        ]);

        // Redirect based on role
        switch ($user['role']) {
            case 'admin':
                header("Location: ../../PondPro-Aquafarms/php/admin/admin.php");
                break;
            case 'manager':
                header("Location: ../../PondPro-Aquafarms/php/manager/manager.php");
                break;
            case 'operator':
                header("Location: ../../PondPro-Aquafarms/php/operator/operator.php");
                break;
        }
        exit();
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid email or password.'); window.location.href = 'index.php';</script>";
    }
}
?>
