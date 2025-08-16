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

$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}



// Handle delete
if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  $conn->query("DELETE FROM categories WHERE id = $id");
  header("Location: categories.php");
  exit;
}

// Handle search
$search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
$sort = isset($_GET['sort']) && in_array($_GET['sort'], ['id', 'Category', 'Date']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

// Query
$query = "SELECT * FROM categories";
if (!empty($search)) {
  $query .= " WHERE Category LIKE '%$search%'";
}
$query .= " ORDER BY $sort $order";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Categories</title>
  <link rel="stylesheet" href="/frontend/css/styles.css">
  <link rel="stylesheet" href="/frontend/css/category.css">
</head>
<body>
  <div class="app-wrapper">
    <?php include 'includes/header.php'; ?>

    <div class="main-area">
      <?php include 'includes/sidebar.php'; ?>

      <main class="app-main">
        <div class="page-header">
          <h1>Manage Categories</h1>
        </div>

        <div class="page-content cat-page">
          <div class="cat-toolbar">
            <form method="GET" class="cat-search-form">
              <input type="text" name="search" placeholder="Search category..." value="<?= htmlspecialchars($search) ?>" />
              <button type="submit" class="cat-btn">Search</button>
              <a href="categories.php" class="cat-btn reset">Reset</a>
            </form>
            <a href="add_category.php" class="cat-btn add">+ Add Category</a>
          </div>

          <table class="cat-table">
            <thead>
              <tr>
              <th>
                <a href="?sort=id&order=<?= ($sort == 'id' && $order == 'ASC') ? 'desc' : 'asc' ?>">
                  #
                  <?php if ($sort === 'id'): ?>
                    <?= $order === 'ASC' ? '▲' : '▼' ?>
                  <?php endif; ?>
                </a>
              </th>
              <th>
                <a href="?sort=Category&order=<?= ($sort == 'Category' && $order == 'ASC') ? 'desc' : 'asc' ?>">
                  Category
                  <?php if ($sort === 'Category'): ?>
                    <?= $order === 'ASC' ? '▲' : '▼' ?>
                  <?php endif; ?>
                </a>
              </th>
              <th>
                <a href="?sort=Date&order=<?= ($sort == 'Date' && $order == 'ASC') ? 'desc' : 'asc' ?>">
                  Date
                  <?php if ($sort === 'Date'): ?>
                    <?= $order === 'ASC' ? '▲' : '▼' ?>
                  <?php endif; ?>
                </a>
              </th>

                <th>Actions</th>  
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['Category']) ?></td>
                    <td><?= $row['Date'] ?></td>
                    <td>
                      <a href="edit_category.php?id=<?= $row['id'] ?>" class="cat-btn">Edit</a>
                      <a href="?delete=<?= $row['id'] ?>" class="cat-btn danger" onclick="return confirm('Delete this category?')">Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="4">No categories found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>

    <?php include 'includes/footer.php'; ?>
  </div>

  <script src="/frontend/js/toggle.js"></script>
</body>
</html>
<?php $conn->close(); ?>
