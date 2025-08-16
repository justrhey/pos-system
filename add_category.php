<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// OPTIONAL: Restrict to Admins only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: add_sale.php");
    exit;
}

// Connect to the MySQL database using MySQLi
$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error); // Stop and show error if connection fails
}

// Initialize variables for displaying messages
$message = '';     // Stores feedback to show user (success or error)
$success = false;  // Indicates if the insert operation was successful

// Check if the form was submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Get the submitted category name and remove extra spaces
  $category = trim($_POST['category']);

  // Make sure the input is not empty
  if (!empty($category)) {

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO categories (Category) VALUES (?)");
    $stmt->bind_param("s", $category); // Bind the category as a string

    // Execute the statement and check if successful
    if ($stmt->execute()) {
      $message = "Category added successfully."; // Success message
      $success = true; // Set flag for success
    } else {
      $message = "Failed to add category."; // Show error if insert failed
    }

    // Close the prepared statement
    $stmt->close();

  } else {
    $message = "Category name is required."; // Error if input is empty
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Category</title>
  <link rel="stylesheet" href="/frontend/css/styles.css">
  <link rel="stylesheet" href="/frontend/css/add_category.css"> <!-- Unique CSS -->
</head>
<body>
  <div class="app-wrapper">
    <?php include 'includes/header.php'; ?>
    <div class="main-area">
      <?php include 'includes/sidebar.php'; ?>

      <main class="app-main">
        <div class="page-header">
          <h1>Add New Category</h1>
        </div>
        <div class="page-content cat-page">

          <?php if (!empty($message)): ?>
            <p class="cat-message <?= $success ? 'success' : 'error' ?>"><?= $message ?></p>
          <?php endif; ?>

          <form method="POST">
            <div class="cat-form-group">
              <label>Category Name:</label>
              <input type="text" name="category" required>
            </div>

            <div class="cat-form-group">
              <button type="submit" class="cat-btn">Save</button>
              <a href="categories.php" class="cat-btn danger">Cancel</a>
            </div>
          </form>

        </div>
      </main>
    </div>
    <?php include 'includes/footer.php'; ?>
  </div>
  <script src="/frontend/js/toggle.js"></script>
</body>
</html>

<?php include 'includes/security.php' ?>
