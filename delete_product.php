<?php

$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}



if (isset($_GET['id'])) {
  $id = (int)$_GET['id'];

  // Optional: check if product exists
  $check = $conn->query("SELECT * FROM products WHERE id = $id");
  if ($check->num_rows > 0) {
    // Delete product
    $conn->query("DELETE FROM products WHERE id = $id");
  }

  // Redirect back to products page
  header("Location: products.php?deleted=1");
  exit;
} else {
  echo "Invalid product ID.";
}

$conn->close();
?>
