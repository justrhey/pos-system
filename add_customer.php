<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}



if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $contact = trim($_POST['contact']);
  $address = trim($_POST['address']);
  $birthday = $_POST['birthday'];

  $stmt = $conn->prepare("INSERT INTO customers (name, email, contact, address, birthday) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $name, $email, $contact, $address, $birthday);
  $stmt->execute();
  $stmt->close();

  header("Location: customer.php?success=1");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Add Customer</title>
  <link rel="stylesheet" href="/frontend/css/styles.css">
  <link rel="stylesheet" href="/frontend/css/add_customer.css">
</head>
<body>
  <div class="app-wrapper">
    <?php include 'includes/header.php'; ?>
    <div class="main-area">
      <?php include 'includes/sidebar.php'; ?>

      <main class="app-main">
        <div class="page-header">
          <h1>Add New Customer</h1>
        </div>
        <div class="page-content">
          <div class="form-box">
            <form method="POST">
              <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" required>
              </div>
              <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email">
              </div>
              <div class="form-group">
                <label>Contact:</label>
                <input type="text" name="contact">
              </div>
              <div class="form-group">
                <label>Address:</label>
                <textarea name="address"></textarea>
              </div>
              <div class="form-group">
                <label>Birthday:</label>
                <input type="date" name="birthday">
              </div>
              <div class="form-actions">
                <button type="submit" class="btn">Save</button>
                <a href="customer.php" class="btn btn-secondary">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
    <?php include 'includes/footer.php'; ?>
  </div>
  <script src="/frontend/js/toggle.js"></script>
</body>
</html>

<?php $conn->close(); ?>
