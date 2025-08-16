<?php
session_start();

// Connect to the database
$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare and execute SQL query
    $sql = "SELECT * FROM users WHERE username = ? AND is_active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and verify password
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Security measure

            // Store session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user['user'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['loggedin'] = true;

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User does not exist or is inactive.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In</title>
    <link rel="stylesheet" type="text/css" href="/frontend/css/login.css">
</head>
<body>
<div class="main">
    <div class="login">
        <form method="POST" action="">
            <label for="chk" aria-hidden="true">Log in</label>
            <img src="/frontend/image/takezo.png" alt="takezoLogo" class="takezo-logo">
            
            <!-- Username Input -->
            <input type="text" name="username" placeholder="Enter your username" required>

            <!-- Password Input -->
            <input type="password" name="password" placeholder="Enter your password" required>

            <!-- Error Message -->
            <?php if ($error): ?>
                <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <!-- Submit Button -->
            <button type="submit" name="login">Log in</button>
        </form>
    </div>
</div>
</body>
</html>
