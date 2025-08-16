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

// Fetch sales over time (daily totals for the last 30 days)
$sales_data = [];
$res = $conn->query("
  SELECT DATE(sale_date) as date, SUM(total) as total 
  FROM sales 
  GROUP BY DATE(sale_date) 
  ORDER BY DATE(sale_date) ASC
");
while ($row = $res->fetch_assoc()) {
  $sales_data[] = $row;
}

// Fetch best-selling products
$best_sellers = [];
$res = $conn->query("
  SELECT product_name, SUM(quantity) as total_quantity 
  FROM sale_items 
  GROUP BY product_name 
  ORDER BY total_quantity DESC 
  LIMIT 10
");
while ($row = $res->fetch_assoc()) {
  $best_sellers[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Report</title>
  <link rel="stylesheet" href="/frontend/css/styles.css">
  <link rel="stylesheet" href="/frontend/css/report.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>  
<div class="app-wrapper">
  <?php include 'includes/header.php'; ?>
  <div class="main-area">
    <?php include 'includes/sidebar.php'; ?>

    <main class="app-main">
      <div class="page-header">
        <h1>Sales Report</h1>
      </div>
        <div class="date-range-filter-report">
            <label>From: <input type="date" id="fromDate"></label>
            <label>To: <input type="date" id="toDate"></label>
            <button id="filterBtn">Filter</button>
        </div>
      <div class=       "page-content">
        <div class="report-section">
          <h3>Total Sales Over Time</h3>
          <canvas id="salesChart" height="100"></canvas>
        </div>

        <div class="report-sectionn">
          <h3>Top Selling Products</h3>
          <canvas id="bestSellerChart" width="200" height="200"></canvas>
            <div class="bestseller-summary">
                    <h3>Best Sellers Breakdown</h3>
                 <ul id="bestsellerList" class="bestseller-list"></ul>
            </div>
        </div>
      </div>
    </main>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>
<?php
$sales_labels = [];
$sales_totals = [];
foreach ($sales_data as $entry) {
  $sales_labels[] = $entry['date'];
  $sales_totals[] = (float)$entry['total'];
}

$bestseller_labels = [];
$bestseller_counts = [];
foreach ($best_sellers as $entry) {
  $bestseller_labels[] = $entry['product_name'];
  $bestseller_counts[] = (int)$entry['total_quantity'];
}
?>
<script>
  window.reportData = {
    salesLabels: <?= json_encode($sales_labels) ?>,
    salesTotals: <?= json_encode($sales_totals) ?>,
    bestSellerLabels: <?= json_encode($bestseller_labels) ?>,
    bestSellerData: <?= json_encode($bestseller_counts) ?>
  };
</script>
<script src="/frontend/js/toggle.js"></script>
<script src="/frontend/js/chart.js"></script>
</body>
</html>
<?php $conn->close(); ?>
