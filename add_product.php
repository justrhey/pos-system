<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// OPTIONAL: Restrict to Admins only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: add_sale.php");
    exit;
}
// Connect to database
$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}



// Handle form submission
$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $description = trim($_POST["description"]);
  $idCategory = (int)$_POST["idCategory"];
  $buyingPrice = (float)$_POST["buyingPrice"];
  $sellingPrice = (float)$_POST["sellingPrice"];
  $stock = (int)$_POST["stock"];

  // Basic validation
  if ($description === "") $errors[] = "Description is required.";
  if ($sellingPrice <= 0) $errors[] = "Selling price must be greater than 0.";
  if ($stock < 0) $errors[] = "Stock cannot be negative.";

  if (empty($errors)) {
    $stmt = $conn->prepare("INSERT INTO products (description, idCategory, buyingPrice, sellingPrice, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siddi", $description, $idCategory, $buyingPrice, $sellingPrice, $stock);
    if ($stmt->execute()) {
      header("Location: products.php");
      exit;
    } else {
      $errors[] = "Failed to add product: " . $stmt->error;
    }
  }
}

// Fetch categories
$categories = [];
$res = $conn->query("SELECT id, Category FROM categories ORDER BY Category ASC");
if ($res && $res->num_rows > 0) {
  while ($row = $res->fetch_assoc()) {
    $categories[] = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Product</title>
  <link rel="stylesheet" href="/frontend/css/styles.css" />
</head>
<body>

<div class="app-wrapper">
  <?php include 'includes/header.php'; ?>
  <div class="main-area">
    <?php include 'includes/sidebar.php'; ?>

    <main class="app-main">
      <div class="page-header">
        <h1>Add New Product</h1>
      </div>

      <div class="page-content">
        <?php if (!empty($errors)): ?>
          <div class="error-box">
            <ul>
              <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" class="add-product-form">
          <label>
            Product Name:
            <input type="text" name="description" required />
          </label>

          <label>
            Category:
            <select name="idCategory" required>
              <option value="">Select category</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['Category']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>

          <label>
            Buying Price:
            <input type="number" name="buyingPrice" step="0.01" />
          </label>

          <label>
            Selling Price:
            <input type="number" name="sellingPrice" step="0.01" required />
          </label>

          <label>
            Stock:
            <input type="number" name="stock" min="0" required />
          </label>

          <button type="submit" class="btn">Save Product</button>
          <a href="products.php" class="cancel-button">Cancel</a>
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
