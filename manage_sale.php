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


// Handle search and filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_customer = isset($_GET['customer']) ? trim($_GET['customer']) : ''; 
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : ''; 

// Build SQL
$sql = "SELECT * FROM sales WHERE 1=1";

if ($search !== '') {
  // If search is not empty, sanitize and append to SQL
  $search = $conn->real_escape_string($search);
  $sql .= " AND (customer LIKE '%$search%' OR seller LIKE '%$search%')";
}
if ($filter_customer !== '') {
  // If filtering by customer, sanitize and add to SQL
  $filter_customer = $conn->real_escape_string($filter_customer);
  $sql .= " AND customer = '$filter_customer'";
}
if ($start_date !== '' && $end_date !== '') {
  // If both dates provided, add date range filter
  $sql .= " AND sale_date BETWEEN '$start_date' AND '$end_date'";
}

$sql .= " ORDER BY sale_date DESC"; 

$result = $conn->query($sql); 
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta and page title -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sales Management</title>
  <link rel="stylesheet" href="/frontend/css/styles.css" />
  <link rel="stylesheet" href="/frontend/css/manage_sale.css" />
</head>
<body>

  <div class="app-wrapper">
    <!-- Header include -->
    <?php include 'includes/header.php'; ?>

    <div class="main-area">
      <!-- Sidebar include -->
      <?php include 'includes/sidebar.php'; ?>

      <!-- Main Content Area -->
      <main class="app-main">
        <div class="page-header">
          <h1>Sales Management</h1>
        </div>

        <div class="page-content">
          <!-- Filter/Search form -->
          <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search customer or seller..." value="<?= htmlspecialchars($search) ?>">
            <input type="text" name="customer" placeholder="Filter by customer" value="<?= htmlspecialchars($filter_customer) ?>">
            <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>"> 
            <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>"> 
            <button type="submit" class="btn">Filter</button> 
            <a href="add_sale.php" class="btn">Add New Sale</a>
          </form>

          <!-- Sales Table -->
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Seller</th>
                <th>Payment Method</th>
                <th>Total Cost</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td> 
                    <td><?= htmlspecialchars($row['seller_name']) ?></td> 
                    <td><?= htmlspecialchars($row['payment_method']) ?></td> 
                    <td>₱<?= number_format($row['total'], 2) ?></td> 
                    <td><?= date('Y-m-d H:i', strtotime($row['sale_date'])) ?></td> 
                    <td>
                      <a href="edit_sale.php?id=<?= $row['id'] ?>" class="btn">Edit</a>
                      <a href="delete_sale.php?id=<?= $row['id'] ?>" class="btn danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="7">No sales found.</td></tr> 
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
