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


$sale_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;



// Fetch sale info
$sale_result = $conn->query("SELECT * FROM sales WHERE id = $sale_id");
$sale = $sale_result->fetch_assoc();

// Fetch sale items
$items_result = $conn->query("SELECT * FROM sale_items WHERE sale_id = $sale_id");

// Fetch product list
$products_result = $conn->query("SELECT id, description, sellingPrice, stock FROM products ORDER BY description");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$customer = trim($_POST['customer']);
$seller = trim($_POST['seller']);
$payment = trim($_POST['payment_method']);
$product_ids = $_POST['product_id'];
$quantities = $_POST['quantity'];

// Recalculate total
$new_total = 0;
$new_items = [];

foreach ($product_ids as $i => $pid) {
  $pid = (int)$pid;
  $qty = (int)$quantities[$i];

  $res = $conn->query("SELECT description, sellingPrice FROM products WHERE id = $pid");
  if ($row = $res->fetch_assoc()) {
    $subtotal = $qty * $row['sellingPrice'];
    $new_items[] = [
      'product_name' => $row['description'],
      'quantity' => $qty,
      'price' => $row['sellingPrice']
    ];
    $new_total += $subtotal;
  }
}

// Update sale table
$stmt = $conn->prepare("UPDATE sales SET customer_name = ?, seller_name = ?, payment_method = ?, total = ? WHERE id = ?");
$stmt->bind_param("sssdi", $customer, $seller, $payment, $new_total, $sale_id);
$stmt->execute();
$stmt->close();

// Delete old items
$conn->query("DELETE FROM sale_items WHERE sale_id = $sale_id");

// Insert new items
foreach ($new_items as $item) {
  $stmt = $conn->prepare("INSERT INTO sale_items (sale_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("isid", $sale_id, $item['product_name'], $item['quantity'], $item['price']);
  $stmt->execute();
  $stmt->close();
}

header("Location: manage_sale.php?updated=1");
exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Sale</title>
<link rel="stylesheet" href="/frontend/css/styles.css">
  <link rel="stylesheet" href="/frontend/css/edit_sale.css">
</head>
<body>
<div class="app-wrapper">
<?php include 'includes/header.php'; ?>
<div class="main-area">
  <?php include 'includes/sidebar.php'; ?>

  <main class="app-main">
    <div class="page-header">
      <h1>Edit Sale</h1>
    </div>

    <div class="page-content">
      <?php if (!$sale): ?>
        <p style="color: red;">Sale not found.</p>
      <?php else: ?>
      <form method="POST" class="edit-sale-form">
        <label>Customer:</label>
        <input type="text" name="customer" value="<?= htmlspecialchars($sale['customer_name']) ?>" required>

        <label>Seller:</label>
        <input type="text" name="seller" value="<?= htmlspecialchars($sale['seller_name']) ?>" required>

        <label>Payment Method:</label>
        <select name="payment_method" required>
          <option value="">Select</option>
          <option value="Cash" <?= $sale['payment_method'] == 'Cash' ? 'selected' : '' ?>>Cash</option>
          <option value="Card" <?= $sale['payment_method'] == 'Card' ? 'selected' : '' ?>>Card</option>
          <option value="Mobile Money" <?= $sale['payment_method'] == 'Mobile Money' ? 'selected' : '' ?>>Mobile Money</option>
        </select>

        <h3>Products</h3>
        <div id="product-list">
          <?php while ($item = $items_result->fetch_assoc()): ?>
            <div class="product-row">
              <select name="product_id[]" required>
                <option value="">Select Product</option>
                <?php
                $products_result->data_seek(0);
                while ($p = $products_result->fetch_assoc()):
                  $selected = ($p['description'] == $item['product_name']) ? 'selected' : '';
                ?>
                  <option value="<?= $p['id'] ?>" data-price="<?= $p['sellingPrice'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($p['description']) ?> ($<?= number_format($p['sellingPrice'], 2) ?>)
                  </option>
                <?php endwhile; ?>
              </select>
              <input type="number" name="quantity[]" value="<?= $item['quantity'] ?>" min="1" required>
              <button type="button" class="cancel-button" onclick="removeRow(this)">Remove</button>
            </div>
          <?php endwhile; ?>
        </div>

        <button type="button" onclick="addProductRow()" class="btn-small-add">+ Add Product</button>

        <strong>Total: $<span id="total-price-add"><?= number_format($sale['total'], 2) ?></span></strong>

        <br><br>
        <button type="submit" class="btn">Update Sale</button>
        <a href="manage_sale.php" class="cancel-button">Cancel</a>
      </form>
      <?php endif; ?>
    </div>
  </main>
</div>
</div>


<script src="/frontend/js/toggle.js"></script>
<script src="/frontend/js/edit_sale.js"></script>


</body>
</html>

<?php $conn->close(); ?>
