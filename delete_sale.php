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




if (isset($_GET['id'])) {
  $id = (int)$_GET['id'];

  // Delete related sale_items first (to maintain foreign key constraints if used)
  $conn->query("DELETE FROM sale_items WHERE sale_id = $id");

  // Delete from sales table
  $conn->query("DELETE FROM sales WHERE id = $id");

  header("Location: manage_sale.php?deleted=1");
  exit;
} else {
  echo "Invalid request.";
}

$conn->close();
?>
