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


$error = '';
$success = '';

// Get the category ID from query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Invalid category ID.");
}
$id = (int) $_GET['id'];

// Fetch existing category
$result = $conn->query("SELECT * FROM categories WHERE id = $id");
if ($result->num_rows === 0) {
  die("Category not found.");
}
$category = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newCategory = trim($_POST['Category']);
  if ($newCategory === '') {
    $error = "Category name cannot be empty.";
  } else {
    $stmt = $conn->prepare("UPDATE categories SET Category = ? WHERE id = ?");
    $stmt->bind_param("si", $newCategory, $id);
    if ($stmt->execute()) {
      $success = "Category updated successfully.";
      $category['Category'] = $newCategory;
    } else {
      $error = "Error updating category.";
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Category</title>
  <link rel="stylesheet" href="frontend/css/styles.css">
  <link rel="stylesheet" href="frontend/css/categories.css">
</head>
<body>
  <div class="app-wrapper">
    <?php include 'includes/header.php'; ?>

    <div class="main-area">
      <?php include 'includes/sidebar.php'; ?>

      <main class="app-main">
        <div class="page-header">
          <h1>Edit Category</h1>
        </div>

        <div class="page-content">
          <?php if ($error): ?>
            <p style="color: red"><?= $error ?></p>
          <?php elseif ($success): ?>
            <p style="color: green"><?= $success ?></p>
          <?php endif; ?>

          <form method="POST">
            <div class="cat-form-group">
              <label for="Category">Category Name:</label>
              <input type="text" name="Category" id="Category" value="<?= htmlspecialchars($category['Category']) ?>" required>
            </div>

            <div class="cat-form-actions">
              <button type="submit" class="btn">Save Changes</button>
              <a href="categories.php" class="btn danger">Cancel</a>
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

<?php $conn->close(); ?>
