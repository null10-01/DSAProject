<?php
require 'config.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];

// Use prepared statements to prevent SQL injection
$stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$stmt = $mysqli->prepare("
    SELECT b.*, f.flight_code, f.source, f.destination, f.flight_date, f.departure_time, f.arrival_date, f.arrival_time 
    FROM bookings b 
    JOIN flights f ON b.flight_id=f.id 
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->bind_param('i', $uid);
$stmt->execute();
$bookings = $stmt->get_result();
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        .info-table { width: auto; margin-bottom: 30px; }
        .info-table td { padding: 5px 15px 5px 5px; }
    </style>
</head>
<body>
    <h2>Profile</h2>
    
    <p>
        <a href="search.php">Search Flights</a> | 
        <a href="index.php">Home</a> | 
        <a href="logout.php">Logout</a>
    </p>
    
    <h3>User Information</h3>
    <table class="info-table">
        <tr>
            <td><strong>Name:</strong></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
        </tr>
        <tr>
            <td><strong>Email:</strong></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
        </tr>
        <tr>
            <td><strong>Priority Level:</strong></td>
            <td><?= (int)$user['priority_level'] ?></td>
        </tr>
    </table>
    
    <h3>My Bookings</h3>
    
    <?php if ($bookings && $bookings->num_rows > 0): ?>
        <table>
        <table>
            <tr>
                <th>ID</th>
                <th>Flight Code</th>
                <th>Route</th>
                <th>Departure</th> 
                <th>Arrival</th> 
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while($b = $bookings->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$b['id'] ?></td>
                    <td><?= htmlspecialchars($b['flight_code']) ?></td>
                    <td><?= htmlspecialchars($b['source']) ?> → <?= htmlspecialchars($b['destination']) ?></td>
                    <td><?= htmlspecialchars($b['flight_date']) ?> <?= htmlspecialchars($b['departure_time']) ?></td>
                    <td><?= htmlspecialchars($b['arrival_date']) ?> <?= htmlspecialchars($b['arrival_time']) ?></td>
                    <td><?= htmlspecialchars($b['status']) ?></td>
                <td>
                    <?php if($b['status'] != 'CANCELLED'): ?>
                        <a href="cancel.php?booking_id=<?= (int)$b['id'] ?>" 
                           onclick="return confirm('Cancel this booking?');">Cancel</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No bookings yet. <a href="search.php">Search for flights</a> to make your first booking.</p>
    <?php endif; ?>
</body>
</html>
