<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Connect to DB
$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


// Get product ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
  die("Invalid product ID.");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $description   = $_POST['description'] ?? '';
  $idCategory    = $_POST['idCategory'] ?? 0;
  $buyingPrice   = $_POST['buyingPrice'] ?? 0;
  $sellingPrice  = $_POST['sellingPrice'] ?? 0;
  $stock         = $_POST['stock'] ?? 0;

  $stmt = $conn->prepare("UPDATE products SET description=?, idCategory=?, buyingPrice=?, sellingPrice=?, stock=? WHERE id=?");
  $stmt->bind_param("siddii", $description, $idCategory, $buyingPrice, $sellingPrice, $stock, $id);

  if ($stmt->execute()) {
    header("Location: products.php");
    exit();
  } else {
    $error = "Failed to update product.";
  }
}

// Fetch product
$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
if (!$product) {
  die("Product not found.");
}

// Fetch categories
$categories = $conn->query("SELECT id, Category FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Product</title>
  <link rel="stylesheet" href="/frontend/css/styles.css" />
    <link rel="stylesheet" href="/frontend/css/edit_product.css" />
</head>
<body>
  <div class="app-wrapper">

    <?php include 'includes/header.php'; ?>

    <div class="main-area">
      <?php include 'includes/sidebar.php'; ?>

      <main class="app-main">
        <div class="page-header">
          <h1>Edit Product</h1>
        </div>

        <div class="page-content">
          <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
          <?php endif; ?>

          <form method="POST" class="form">
            <label>
              Name:
              <input type="text" name="description" value="<?= htmlspecialchars($product['description'] ?? '') ?>" required />
            </label>

            <label>
              Category:
              <select name="idCategory" required>
                <option value="">Select category</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                  <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['idCategory'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['Category']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </label>

            <label>
              Buying Price:
              <input type="number" step="0.01" name="buyingPrice" value="<?= htmlspecialchars($product['buyingPrice'] ?? '0.00') ?>" required />
            </label>

            <label>
              Selling Price:
              <input type="number" step="0.01" name="sellingPrice" value="<?= htmlspecialchars($product['sellingPrice'] ?? '0.00') ?>" required />
            </label>

            <label>
              Stock:
              <input type="number" name="stock" value="<?= htmlspecialchars($product['stock'] ?? 0) ?>" required />
            </label>

            <button type="submit" class="save-btn">Update Product</button>
            <a href="products.php" class="cancel-btn">Cancel</a>
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
