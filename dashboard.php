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


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>POS Dashboard</title>
<link rel="stylesheet" href="css/styles.css" />
<link rel="stylesheet" href="css/dashboard.css">
<link rel="stylesheet" href="css/report.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="app-wrapper">
<?php include 'includes/header.php'; ?>

<div class="main-area">
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/main.php'; ?>
</div>

<?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/dashboard_charts_data.php'; ?>
<script src="/frontend/js/toggle.js"></script>
<script src="/frontend/js/chart.js"></script>
</body>
</html>
<?php include 'includes/security.php' ?>
