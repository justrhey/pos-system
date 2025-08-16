<main class="app-main">
  <div class="page-header">
    <h1>Dashboard</h1>
  </div>

  <div class="page-content">
    <?php include 'top_boxes.php'; ?> <!-- This inserts the top boxes here -->

    <!-- Line Chart: Sales Over Time -->
    <div class="dashboard-section">
      <h3>Total Sales Over Time</h3>
      <canvas id="salesChart" height="80"></canvas>
    </div>

    <!-- Doughnut Chart: Best-Selling Products -->
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
