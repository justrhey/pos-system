<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$success = false;

// Get user ID from query
$id = $_GET['id'] ?? null;
if (!$id) {
    die("User ID not provided.");
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $user_label = trim($_POST['user'] ?? '');
    $role = $_POST['role'] ?? '';

    if ($name === '' || $username === '' || $user_label === '' || $role === '') {
        $errors[] = "All fields are required.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors[] = "Full Name must contain only letters and spaces.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, username = ?, user = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $username, $user_label, $role, $id);

        try {
            $stmt->execute();
            $success = true;
            // Refresh user data for form display
            $user = ['name' => $name, 'username' => $username, 'user' => $user_label, 'role' => $role];
        } catch (mysqli_sql_exception $e) {
            if (str_contains($e->getMessage(), "Duplicate entry")) {
                $errors[] = "That username is already taken.";
            } else {
                $errors[] = "Error: " . $e->getMessage();
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="frontend/css/add_user.css">
</head>
<body>
<div class="user-wrapper">
    <?php include 'includes/header.php'; ?>
    <div class="main-area">
        <?php include 'includes/sidebar.php'; ?>
        <main class="app-main">
            <div class="page-header">
                <h1>Edit User</h1>
            </div>

            <div class="page-content">
                <?php if ($errors): ?>
                    <div class="alert error"><?= implode('<br>', $errors) ?></div>
                <?php elseif ($success): ?>
                    <div class="alert success">User updated successfully!</div>
                <?php endif; ?>

                <form method="post" class="add-user-form">
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="<?= htmlspecialchars($user['name']) ?>"
                            required
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            title="Only letters and spaces allowed"
                        >
                    </div>

                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input
                            type="text"
                            name="username"
                            id="username"
                            value="<?= htmlspecialchars($user['username']) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="user">User Label (optional display name):</label>
                        <input
                            type="text"
                            name="user"
                            id="user"
                            value="<?= htmlspecialchars($user['user']) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select name="role" id="role" required>
                            <option value="">Select Role</option>
                            <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="Cashier" <?= $user['role'] === 'Cashier' ? 'selected' : '' ?>>Cashier</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">Update User</button>
                        <a href="user.php" class="cancel-button">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <?php include 'includes/footer.php'; ?>
</div>
<script src="/frontend/js/toggle.js"></script>
<script src="/frontend/js/edit_user.js"></script>
</body>
</html>

<?php $conn->close(); ?>
