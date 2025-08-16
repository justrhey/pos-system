<?php
session_start();

// Redirect to login if user not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>POS Layout</title>
  <link rel="stylesheet" href="/frontend/css/styles.css" />
</head>
<body>



    <?php include 'login.php'; ?> 

</body>
</html>
