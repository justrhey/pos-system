<?php
session_start();

// DB connection
$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST['name'] ?? '');
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $user = trim($_POST['user'] ?? '');
  $role = $_POST['role'] ?? '';

  if ($name === '' || $username === '' || $password === '' || $user === '' || $role === '') {
    $errors[] = "All fields are required.";
  } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
    $errors[] = "Full Name must contain only letters and spaces.";
  } else {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, username, password, user, role, is_active) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sssss", $name, $username, $hashedPassword, $user, $role);

    try {
      $stmt->execute();
      $success = true;
    } catch (mysqli_sql_exception $e) {
      if (str_contains($e->getMessage(), "Duplicate entry")) {
        $errors[] = "The username already exists.";
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
  <title>Add User</title>
  <link rel="stylesheet" href="frontend/css/add_user.css">
</head>
<body>
<div class="user-wrapper">
  <?php include 'includes/header.php'; ?>
  <div class="main-area">
    <?php include 'includes/sidebar.php'; ?>
    <main class="app-main">
      <div class="page-header">
        <h1>Add User</h1>
      </div>

      <div class="page-content">
        <?php if ($errors): ?>
          <div class="alert error">
            <?= implode('<br>', $errors) ?>
          </div>
        <?php elseif ($success): ?>
          <div class="alert success">User added successfully!</div>
        <?php endif; ?>

        <form method="post" class="add-user-form">
          <div class="form-group">
            <label for="name">Full Name:</label>
            <input
              type="text"
              name="name"
              id="name"
              required
              oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
              title="Only letters and spaces allowed"
              value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
            >
          </div>

          <div class="form-group">
            <label for="username">Username:</label>
            <input
              type="text"
              name="username"
              id="username"
              required
              value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
            >
          </div>

          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
          </div>

          <div class="form-group">
            <label for="user">User Label (optional label or nickname):</label>
            <input
              type="text"
              name="user"
              id="user"
              required
              value="<?= htmlspecialchars($_POST['user'] ?? '') ?>"
            >
          </div>

          <div class="form-group">
            <label for="role">Role:</label>
            <select name="role" id="role" required>
              <option value="">Select Role</option>
              <option value="Admin" <?= (($_POST['role'] ?? '') === 'Admin') ? 'selected' : '' ?>>Admin</option>
              <option value="Cashier" <?= (($_POST['role'] ?? '') === 'Cashier') ? 'selected' : '' ?>>Cashier</option>      
            </select>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn">Add User</button>
            <a href="user.php" class="cancel-button">Cancel</a>
          </div>
        </form> 

        <!-- Example delete button (place it wherever relevant) -->
        <!--
        <a href="delete_user.php?id=123" class="delete-user-btn btn btn-danger">Delete User</a>
        -->
      </div>
    </main>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>

<script src="/frontend/js/toggle.js"></script>
<script src="/frontend/js/add_user.js"></script>
</body>
</html>

<?php $conn->close(); ?>
