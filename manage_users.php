<?php
require 'config.php';

// Check if user is admin
if (empty($_SESSION['is_admin'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Handle priority update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_priority') {
    $user_id = (int)$_POST['user_id'];
    $new_priority = (int)$_POST['priority_level'];
    
    // Validate priority level (1-5)
    if ($new_priority >= 1 && $new_priority <= 5) {
        $stmt = $mysqli->prepare("UPDATE users SET priority_level = ? WHERE id = ? AND is_admin = 0");
        $stmt->bind_param('ii', $new_priority, $user_id);
        
        if ($stmt->execute()) {
            $success = "Priority level updated successfully!";
        } else {
            $error = "Failed to update priority level.";
        }
    } else {
        $error = "Invalid priority level. Must be between 1 and 5.";
    }
}

// Fetch all users (excluding admins)
$users = $mysqli->query("
    SELECT u.id, u.name, u.email, u.priority_level, u.created_at,
           COUNT(DISTINCT b.id) as total_bookings,
           SUM(CASE WHEN b.status = 'CONFIRMED' THEN 1 ELSE 0 END) as confirmed_bookings
    FROM users u
    LEFT JOIN bookings b ON u.id = b.user_id
    WHERE u.is_admin = 0
    GROUP BY u.id, u.name, u.email, u.priority_level, u.created_at
    ORDER BY u.created_at DESC
");
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Users - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        .success { color: green; background: #e6ffe6; padding: 10px; margin: 10px 0; border: 1px solid green; }
        .error { color: red; background: #ffe6e6; padding: 10px; margin: 10px 0; border: 1px solid red; }
        input[type="number"] { width: 60px; }
        input[type="submit"] { padding: 5px 10px; }
    </style>
</head>
<body>
    <h2>Manage Users - Admin Panel</h2>
    
    <p>
        <a href="admin_dashboard.php">Dashboard</a> | 
        <a href="create_flight.php">Create Flight</a> | 
        <a href="logout.php">Logout</a>
    </p>
    
    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <h3>User Priority Management</h3>
    
    <?php if ($users && $users->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registered</th>
                <th>Total Bookings</th>
                <th>Confirmed</th>
                <th>Priority Level</th>
                <th>Update Priority</th>
            </tr>
            <?php while($user = $users->fetch_assoc()): ?>
            <tr>
                <td><?php echo (int)$user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                <td><?php echo (int)$user['total_bookings']; ?></td>
                <td><?php echo (int)$user['confirmed_bookings']; ?></td>
                <td><?php echo (int)$user['priority_level']; ?></td>
                <td>
                    <form method="post" action="manage_users.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_priority">
                        <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                        <input type="number" name="priority_level" min="1" max="5" value="<?php echo (int)$user['priority_level']; ?>" required>
                        <input type="submit" value="Update">
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</body>
</html>
