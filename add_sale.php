
<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}




// Fetch products
$products_result = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY description");

// Fetch customers and sellers
$customers_result = $conn->query("SELECT name FROM customers ORDER BY name");
$sellers_result = $conn->query("SELECT name FROM users WHERE role = 'Cashier' OR role = 'Admin' ORDER BY name");

// Handle form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $customer = trim($_POST['customer']);
  $seller = trim($_POST['seller']);
  $payment = trim($_POST['payment_method']);
  $product_ids = $_POST['product_id'];
  $quantities = $_POST['quantity'];

  $total_cost = 0;
  $items = [];

  foreach ($product_ids as $i => $pid) {
    $pid = (int)$pid;
    $qty = (int)$quantities[$i];

    $res = $conn->query("SELECT description, sellingPrice, stock FROM products WHERE id = $pid");
    if ($row = $res->fetch_assoc()) {
      if ($qty > 0 && $qty <= $row['stock']) {
        $price = $row['sellingPrice'];
        $product_name = $row['description'];
        $subtotal = $qty * $price;

        $items[] = [
          'product_name' => $product_name,
          'quantity' => $qty,
          'price' => $price,
        ];
        $total_cost += $subtotal;
      }
    }
  }

  if (!empty($items)) {
    // Insert sale
    $stmt = $conn->prepare("INSERT INTO sales (customer_name, seller_name, payment_method, total, sale_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssd", $customer, $seller, $payment, $total_cost);
    $stmt->execute();
    $sale_id = $stmt->insert_id;
    $stmt->close();

    // Insert sale_items and update product stock
    foreach ($items as $item) {
      $stmt = $conn->prepare("INSERT INTO sale_items (sale_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("isid", $sale_id, $item['product_name'], $item['quantity'], $item['price']);
      $stmt->execute();
      $stmt->close();

      $conn->query("UPDATE products SET stock = stock - {$item['quantity']} WHERE description = '{$conn->real_escape_string($item['product_name'])}'");
    }

    // Update customer info
    $customer_safe = $conn->real_escape_string($customer);
    $res = $conn->query("SELECT id, total_purchases FROM customers WHERE name = '$customer_safe'");
    if ($cust = $res->fetch_assoc()) {
      $new_total = $cust['total_purchases'] + $total_cost;
      $conn->query("UPDATE customers SET last_purchase = NOW(), total_purchases = $new_total WHERE id = {$cust['id']}");
    }

    header("Location: manage_sale.php?success=1");
    exit;
  } else {
    $error = "Invalid product selection or quantity.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Add Sale</title>
  <link rel="stylesheet" href="/frontend/css/styles.css">
</head>
<body>
  <div class="app-wrapper">
    <?php include 'includes/header.php'; ?>
    <div class="main-area">
      <?php include 'includes/sidebar.php'; ?>

      <main class="app-main">
        <div class="page-header">
          <h1>Add New Sale</h1>
        </div>
        <div class="page-content">
          <?php if (!empty($error)): ?>
            <p style="color: red"><?= $error ?></p>
          <?php endif; ?>

          <form method="POST" onsubmit="return validateForm()" class="add-sale-form">
            <label>Customer:</label>
            <select name="customer" required>
              <option value="">Select Customer</option>
              <?php
              $customers_result->data_seek(0);
              while ($c = $customers_result->fetch_assoc()):
              ?>
                <option value="<?= htmlspecialchars($c['name']) ?>"><?= htmlspecialchars($c['name']) ?></option>
              <?php endwhile; ?>
            </select>

            <label>Seller:</label>  
            <select name="seller" required>
              <option value="">Select Seller</option>
              <?php
              $sellers_result->data_seek(0);
              while ($s = $sellers_result->fetch_assoc()):
              ?>
                <option value="<?= htmlspecialchars($s['name']) ?>"><?= htmlspecialchars($s['name']) ?></option>
              <?php endwhile; ?>
            </select>

            <label>Payment Method:</label>
            <select name="payment_method" required>
              <option value="">Select</option>
              <option value="Cash">Cash</option>
              <option value="Card">Card</option>
              <option value="Mobile Money">Mobile Money</option>
            </select>

            <h3>Products</h3>
            <div id="product-list">
              <div class="product-row">
                <select name="product_id[]" required>
                  <option value="">Select Product</option>
                  <?php
                  $products_result->data_seek(0);
                  while ($p = $products_result->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>" data-price="<?= $p['sellingPrice'] ?>">
                      <?= htmlspecialchars($p['description']) ?> (₱<?= number_format($p['sellingPrice'], 2) ?>)
                    </option>
                  <?php endwhile; ?>
                </select>
                <input type="number" name="quantity[]" min="1" placeholder="Quantity" required>
                <button type="button" class="remove-button" onclick="removeRow(this)">Remove</button>
              </div>
            </div>

            <button type="button" onclick="addProductRow()" class="add-btn">+ Add Product</button>
            <strong>Total: ₱<span id="total-price-add">0.00</span></strong> 

            <button type="submit" class="btn">Save Sale</button>
            <a href="manage_sale.php" class="cancel-button">Cancel</a>
          </form>
        </div>
      </main>
    </div>
    <?php include 'includes/footer.php'; ?>
  </div>

<script src="/frontend/js/add_sale.js"></script>
<script src="/frontend/js/toggle.js"></script>
</body>
</html>

<?php $conn->close(); ?>
