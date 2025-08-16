<?php
// Connect to DB
$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Total Sales
$salesResult = $conn->query("SELECT SUM(total) AS total FROM sales");
$sales = $salesResult->fetch_assoc();
$totalSales = $sales["total"] ?? 0;

// Total Categories
$categoriesResult = $conn->query("SELECT COUNT(*) AS count FROM categories");
$categories = $categoriesResult->fetch_assoc();
$totalCategories = $categories["count"];

// Total Products
$productsResult = $conn->query("SELECT COUNT(*) AS count FROM products");
$products = $productsResult->fetch_assoc();
$totalProducts = $products["count"];

// Total Customers
$customersResult = $conn->query("SELECT COUNT(*) AS count FROM customers");
$customers = $customersResult->fetch_assoc();
$totalCustomers = $customers["count"];
?>

<div class="dashboard-row">

  <div class="dashboard-box sales-box">
    <div class="dashboard-icon">💰</div>
    <h3>₱<?php echo number_format($totalSales, 2); ?></h3>
    <p>Total Sales</p>
    <a href="manage_sale.php">More info →</a>
  </div>

  <div class="dashboard-box categories-box">
    <div class="dashboard-icon">📦</div>
    <h3><?php echo number_format($totalCategories); ?></h3>
    <p>Categories</p>
    <a href="categories.php">More info →</a>
  </div>

  <div class="dashboard-box products-box">
    <div class="dashboard-icon">🛒</div>
    <h3><?php echo number_format($totalProducts); ?></h3>
    <p>Products</p>
    <a href="products.php">More info →</a>
  </div>

  <div class="dashboard-box customers-box">
    <div class="dashboard-icon">👤</div>
    <h3><?php echo number_format($totalCustomers); ?></h3>
    <p>Customers</p>
    <a href="customer.php">More info →</a>
  </div>

</div>
