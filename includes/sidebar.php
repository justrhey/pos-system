<aside class="app-sidebar" id="sidebar">
<div class="user-panel">
  <p><strong>
    <?= isset($_SESSION['name'], $_SESSION['role']) 
        ? htmlspecialchars($_SESSION['name'] . ' ' . $_SESSION['role']) 
        : 'Cashier' ?>
  </strong></p>
</div>
<nav class="menu">
  <ul>
    <li><a href="dashboard.php">Dashboard</a></li>

    <li class="dropdown">
      <a class="dropdown-btn" onclick="toggleDropdown(event)">Products ▾</a>
      <div class="dropdown-content">
        <a href="products.php">Products</a>
        <a href="add_product.php">Add Product</a>
      </div>
    </li>

    <li class="dropdown">
      <a class="dropdown-btn" onclick="toggleDropdown(event)">Sales ▾</a>
      <div class="dropdown-content">
        <a href="manage_sale.php">Manage Sales</a>
        <a href="add_sale.php">Create Sale</a>
      </div>
    </li> 

    <li class="dropdown">
      <a class="dropdown-btn" onclick="toggleDropdown(event)">Categories ▾</a>
      <div class="dropdown-content">
        <a href="categories.php">Manage Categories</a>
        <a href="add_category.php">Add  Category</a>
      </div>
    </li>
    <li><a href="customer.php">Customers</a></li>
    <li><a href="report.php">Reports</a></li>
    <li><a href="user.php">Users</a></li> 
  </ul>
</nav>
</aside>
