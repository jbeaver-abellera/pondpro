<?php
try {
    // Connect to the database
    $pdo = new PDO('mysql:host=localhost;dbname=pondpro_aquafarms_database', 'root', '');

    // Define users and their plain passwords
    $users = [
        [
            'email' => 'bello_admin@gmail.com',
            'password' => 'administrator_112124'
        ],
        [
            'email' => 'jc.saxophonist0629@gmail.com',
            'password' => 'manager_112124' // Manager_120624
        ],
        [
            'email' => 'cjpayodbusiness@gmail.com',
            'password' => 'Password123'
        ]
    ];

    // Loop through users and update hashed passwords
    foreach ($users as $user) {
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE user_accounts SET password = :password WHERE email = :email");
        $stmt->execute([
            'password' => $hashedPassword,
            'email' => $user['email']
        ]);

        echo "Password hashed and updated successfully for: " . $user['email'] . "<br>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

