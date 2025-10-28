<?php
require 'config.php';
if(empty($_SESSION['user_id']) || !$_SESSION['is_admin'])
{
    header('Location: login.php');
    exit;
}

// fetch flights and bookings
$flights_res = $mysqli->query("SELECT * FROM flights ORDER BY flight_date ASC");
$all_flights = $flights_res ? $flights_res->fetch_all(MYSQLI_ASSOC) : [];

// Sort flights
$domestic_flights = [];
$international_flights = [];
foreach ($all_flights as $f) {
    if ($f['flight_type'] === 'International') {
        $international_flights[] = $f;
    } else {
        $domestic_flights[] = $f;
    }
}
$bookings = $mysqli->query("SELECT b.*, u.name, f.flight_code FROM bookings b JOIN users u ON b.user_id=u.id JOIN flights f ON b.flight_id=f.id ORDER BY b.created_at DESC");

// summary numbers
$total_flights = count($all_flights);
$total_bookings = $bookings ? $bookings->num_rows : 0;
$total_users = $mysqli->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 0")->fetch_assoc()['count'];
$pending_bookings = 0;
if ($bookings) {
    $bookings_rows = [];
    while ($r = $bookings->fetch_assoc()) {
        $bookings_rows[] = $r;
        if (isset($r['status']) && strtolower($r['status']) === 'pending') $pending_bookings++;
    }
} else {
    $bookings_rows = [];
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        .stats { margin: 20px 0; }
        .stats span { display: inline-block; margin-right: 30px; padding: 10px; background: #f5f5f5; }
    </style>
</head>
<body>
    <h2>Admin Dashboard - Flight Ticket Booking System</h2>
    
    <p>
        <a href="create_flight.php">Create Flight</a> | 
        <a href="manage_users.php">Manage Users</a> | 
        <a href="index.php">Home</a> | 
        <a href="logout.php">Logout</a>
    </p>
    
    <div class="stats">
        <span><strong>Total Flights:</strong> <?php echo (int)$total_flights; ?></span>
        <span><strong>Total Users:</strong> <?php echo (int)$total_users; ?></span>
        <span><strong>Total Bookings:</strong> <?php echo (int)$total_bookings; ?></span>
        <span><strong>Pending:</strong> <?php echo (int)$pending_bookings; ?></span>
    </div>
    
    <h3>Domestic Flights</h3>
    <table>
        <tr>
            <th>Code</th>
            <th>Source</th>
            <th>Destination</th>
            <th>Departure Date</th>
            <th>Seats Total</th>
            <th>Seats Booked</th>
            <th>Actions</th>
        </tr>
        <?php if (!empty($domestic_flights)): foreach($domestic_flights as $f): ?>
        <tr>
            <td><?php echo htmlspecialchars($f['flight_code']); ?></td>
            <td><?php echo htmlspecialchars($f['source']); ?></td>
            <td><?php echo htmlspecialchars($f['destination']); ?></td>
            <td><?php echo htmlspecialchars($f['flight_date']); ?></td>
            <td><?php echo (int)$f['seats_total']; ?></td>
            <td><?php echo (int)$f['seats_booked']; ?></td>
            <td>
                <a href="edit_flight.php?id=<?php echo (int)$f['id']; ?>">Edit</a> | 
                <a href="view_flight.php?id=<?php echo (int)$f['id']; ?>">View</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="7">No domestic flights found.</td></tr>
        <?php endif; ?>
    </table>
    
    <h3>International Flights</h3>
    <table>
        <tr>
            <th>Code</th>
            <th>Source</th>
            <th>Destination</th>
            <th>Departure Date</th>
            <th>Seats Total</th>
            <th>Seats Booked</th>
            <th>Actions</th>
        </tr>
        <?php if (!empty($international_flights)): foreach($international_flights as $f): ?>
        <tr>
            <td><?php echo htmlspecialchars($f['flight_code']); ?></td>
            <td><?php echo htmlspecialchars($f['source']); ?></td>
            <td><?php echo htmlspecialchars($f['destination']); ?></td>
            <td><?php echo htmlspecialchars($f['flight_date']); ?></td>
            <td><?php echo (int)$f['seats_total']; ?></td>
            <td><?php echo (int)$f['seats_booked']; ?></td>
            <td>
                <a href="edit_flight.php?id=<?php echo (int)$f['id']; ?>">Edit</a> | 
                <a href="view_flight.php?id=<?php echo (int)$f['id']; ?>">View</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="7">No international flights found.</td></tr>
        <?php endif; ?>
    </table>
    
    <h3>All Bookings</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Flight</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Created At</th>
        </tr>
        <?php if (!empty($bookings_rows)): foreach($bookings_rows as $row): ?>
        <tr>
            <td><?php echo (int)$row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['flight_code']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td><?php echo htmlspecialchars($row['priority']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6">No bookings found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>