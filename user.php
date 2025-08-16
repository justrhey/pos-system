<?php
session_start();



// Database connection
$conn = new mysqli("localhost", "root", "", "takezopos");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Toggle status
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $conn->query("UPDATE users SET is_active = NOT is_active WHERE id = $id");
    header("Location: user.php");
    exit;
}

// Delete user
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: user.php");
    exit;
}

// Filters
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$role = isset($_GET['role']) ? $conn->real_escape_string($_GET['role']) : '';

// Build query
$sql = "SELECT * FROM users WHERE 1";
if ($search !== '') {
    $sql .= " AND name LIKE '%$search%'";
}
if ($role !== '') {
    $sql .= " AND role = '$role'";
}
$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Users</title>
    <link rel="stylesheet" href="frontend/css/styles.css" />
    <link rel="stylesheet" href="frontend/css/user.css" />
</head>
<body>
<div class="user-wrapper">
    <?php include 'includes/header.php'; ?>

    <div class="main-area">
        <?php include 'includes/sidebar.php'; ?>

        <main class="app-main">
            <div class="page-header">
                <h1>Users</h1>
            </div>

            <div class="page-content">
                <div class="user-toolbar">
                    <form method="get" class="filter-form">
                        <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($search) ?>" />

                        <!-- Updated name from 'user' to 'role' to match PHP -->
                        <select name="role">
                            <option value="">All Roles</option>
                            <option value="Admin" <?= $role == 'Admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="Cashier" <?= $role == 'Cashier' ? 'selected' : '' ?>>Cashier</option>
                        </select>

                        <button type="submit">Filter</button>
                        <a href="add_user.php" class="btn">Add User</a>
                    </form>
                </div>

                <table class="user-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['role']) ?></td>
                                <td>
                                    <a href="?toggle=<?= $row['id'] ?>" class="btn <?= $row['is_active'] ? 'active' : 'inactive' ?>">
                                        <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn">Edit</a>
                                    <a href="?delete=<?= $row['id'] ?>" class="btn danger" onclick="return confirm('Delete this user?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No users found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<script src="/frontend/js/toggle.js"></script>
</body>
</html>

<?php $conn->close(); ?>
