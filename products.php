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
// Connect to DB
$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}



// Handle search and sorting
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

$valid_columns = ['description', 'category_name', 'sellingPrice', 'stock'];
if (!in_array($sort_by, $valid_columns)) {
  $sort_by = 'id';
}

// Fetch products with category name
$sql = "SELECT p.*, c.Category AS category_name
        FROM products p
        LEFT JOIN categories c ON p.idCategory = c.id";

if ($search !== '') {
  $search = $conn->real_escape_string($search);
  $sql .= " WHERE p.description LIKE '%$search%' OR c.Category LIKE '%$search%'";
}

$sql .= " ORDER BY $sort_by $order";

$result = $conn->query($sql);

function sort_link($column, $label, $current_sort_by, $current_order) {
  $new_order = ($current_sort_by === $column && $current_order === 'ASC') ? 'desc' : 'asc';
  $arrow = '';
  if ($current_sort_by === $column) {
    $arrow = $current_order === 'ASC' ? ' <span style="color:#00f">▲</span>' : ' <span style="color:#00f">▼</span>';
  }
  return "<a href=\"?sort_by=$column&order=$new_order\">$label$arrow</a>";


}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Product Management</title>
  <link rel="stylesheet" href="/frontend/css/styles.css" />
</head>
<body>

  <div class="app-wrapper">

    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="main-area">
      <!-- Sidebar -->
      <?php include 'includes/sidebar.php'; ?>

      <!-- Main content -->
      <main class="app-main">
        <div class="page-header">
          <h1>Product Management</h1>
        </div>

        <div class="page-content">
          <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="add_product.php" class="btn">Add New Product</a>
            <form method="GET" style="margin: 0;">
              <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>" />
              <button type="submit" class="btn small">Search</button>
            </form>
          </div>

          <table class="product-table">
            <thead>
              <tr>
                <th>Product ID</th>
                <th><?= sort_link('description', 'Name', $sort_by, $order) ?></th>
                <th><?= sort_link('category_name', 'Category', $sort_by, $order) ?></th>
                <th><?= sort_link('sellingPrice', 'Price', $sort_by, $order) ?></th>
                <th><?= sort_link('stock', 'Stock', $sort_by, $order) ?></th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                    <td>₱<?= number_format($row['sellingPrice'], 2) ?></td>
                    <td><?= htmlspecialchars($row['stock']) ?></td>
                    <td>
                      <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn small">Edit</a>
                      <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn small danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="6">No products found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

  </div>

<script src="/frontend/js/toggle.js"></script>

</body>
</html>

<?php $conn->close(); ?>
