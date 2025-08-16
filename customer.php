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



// Handle search
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM customers";
if (!empty($search)) {
  $safe = $conn->real_escape_string($search);
  $sql .= " WHERE name LIKE '%$safe%' OR email LIKE '%$safe%' OR contact LIKE '%$safe%'";
}
$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Customers</title>
  <link rel="stylesheet" href="/frontend/css/styles.css">
  <link rel="stylesheet" href="/frontend/css/customer.css">
</head>
<body>
  <div class="app-wrapper">
    <?php include 'includes/header.php'; ?>
    <div class="main-area">
      <?php include 'includes/sidebar.php'; ?>

      <main class="app-main">
        <div class="page-header">
          <h1>Customers</h1>
        </div>

        <div class="page-content">
          <?php if (isset($_GET['success'])): ?>
            <p style="color: green;">Customer added successfully.</p>
          <?php endif; ?>

          <div class="top-bar">
            <form method="GET" class="search-bar">
              <input type="text" name="search" placeholder="Search customer..." value="<?= htmlspecialchars($search) ?>">
              <button type="submit" class="btn">Search</button>
            </form>
            <a href="add_customer.php" class="btn">+ Add Customer</a>
          </div>

          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Birthday</th>
                <th>Total Purchases</th>
                <th>Last Purchase</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['contact']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= $row['birthday'] ?></td>
                    <td>₱<?= number_format($row['total_purchases'] ?? 0, 2) ?></td>
                    <td><?= $row['last_purchase'] ?? '—' ?></td>
                    <td class="actions">
                      <a href="edit_customer.php?id=<?= $row['id'] ?>">Edit</a>
                      <a href="delete_customer.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="10">No customers found.</td></tr>
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
