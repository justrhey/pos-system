<?php
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
