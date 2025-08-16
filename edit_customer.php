<?php
// Fetch customer data
if (!isset($_GET['id'])) {
die("Customer ID not provided.");
}
$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}


$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM customers WHERE id = $id");
if ($result->num_rows === 0) {
die("Customer not found.");
}
$customer = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
$name = $conn->real_escape_string($_POST["name"]);
$email = $conn->real_escape_string($_POST["email"]);
$phone = $conn->real_escape_string($_POST["phone"]);

$update = $conn->query("
  UPDATE customers SET 
    name = '$name', 
    email = '$email', 
    phone = '$phone' 
  WHERE id = $id
");

if ($update) {
  header("Location: customers.php");
  exit();
} else {
  echo "Error: " . $conn->error;
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Customer</title>
<link rel="stylesheet" href="css/styles.css" />
<link rel="stylesheet" href="css/edit_customer.css" />
</head>
<body>
<div class="app-wrapper">
  <?php include 'includes/header.php'; ?>
  <div class="main-area">
    <?php include 'includes/sidebar.php'; ?>

    <main class="app-main">
      <div class="page-header">
        <h1>Edit Customer</h1>
      </div>

      <div class="form-wrapper">
        <h2>Edit Customer Info</h2>
        <form method="POST">
          <label for="name">Full Name:</label>
          <input type="text" id="name" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required>

          <label for="email">Email:</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>">

          <label for="phone">Phone:</label>
          <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($customer['contact']) ?>">

          <button type="submit" class="save-btn">Update Customer</button>
          <a href="customer.php" class="cancel-btn">Cancel</a>
        </form>
      </div>
    </main>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>
<script src="js/toggle.js"></script>
</body>
</html>
