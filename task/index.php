<?php
require_once('db_config.php');

// Pagination variables
$limit = 5; // Records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page

// Calculate offset for pagination
$offset = ($page - 1) * $limit;

// Fetch users with pagination
$stmt_users = $conn->prepare("SELECT u.id, u.name, u.email, u.mobile, COUNT(e.id) AS total_companies, SUM(e.years * 12 + e.months) AS total_months
                             FROM users u
                             LEFT JOIN experiences e ON u.id = e.user_id
                             GROUP BY u.id
                             ORDER BY u.id ASC
                             LIMIT ?, ?");
$stmt_users->bind_param("ii", $offset, $limit);
$stmt_users->execute();
$result_users = $stmt_users->get_result();

// Fetch total number of users for pagination
$stmt_count = $conn->prepare("SELECT COUNT(DISTINCT id) AS total_users FROM users");
$stmt_count->execute();
$row_count = $stmt_count->get_result()->fetch_assoc();
$total_users = $row_count['total_users'];

// Close statements
$stmt_users->close();
$stmt_count->close();

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .pagination {
            margin-top: 10px;
        }
        .pagination a {
            text-decoration: none;
            padding: 8px 16px;
            background-color: #f1f1f1;
            color: black;
            border: 1px solid #ccc;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
        }
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
    </style>
</head>
<body>

<h2>User List</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Total Companies Served</th>
            <th>Total Experience</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result_users->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['mobile']; ?></td>
                <td><?php echo $row['total_companies']; ?></td>
                <td><?php echo floor($row['total_months'] / 12) . " years, " . ($row['total_months'] % 12) . " months"; ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a> |
                    <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Pagination links -->
<div class="pagination">
    <?php
    $pages = ceil($total_users / $limit); // Calculate total pages
    if ($pages > 1) {
        echo "Pages: ";
        for ($i = 1; $i <= $pages; $i++) {
            if ($i == $page) {
                echo "<strong>{$i}</strong> ";
            } else {
                echo "<a href='?page={$i}'>{$i}</a> ";
            }
        }
    }
    ?>
</div>

<br><br>
<a href="add_user.php">Add User</a>

</body>
</html>
