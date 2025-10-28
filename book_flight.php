<?php
require 'functions.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$flight_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$flight_id) {
    header('Location: search.php');
    exit;
}

// Get flight details
$stmt = $mysqli->prepare("SELECT * FROM flights WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $flight_id);
$stmt->execute();
$res = $stmt->get_result();
$flight = $res ? $res->fetch_assoc() : null;

if (!$flight) {
    header('Location: search.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user's priority level using prepared statement
    $stmt = $mysqli->prepare("SELECT priority_level FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    $priority = (int)$u['priority_level'];

    // Create booking
    $stmt = $mysqli->prepare("INSERT INTO bookings (user_id, flight_id, priority, status) VALUES (?, ?, ?, 'WAITLISTED')");
    $stmt->bind_param('iii', $user_id, $flight_id, $priority);

    if ($stmt->execute()) {
        allocate_seats($flight_id);
        header('Location: profile.php');
        exit;
    } else {
        $err = 'Error: ' . $mysqli->error;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Book Flight</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    table { border-collapse: collapse; margin: 20px 0; }
    td { padding: 8px 15px; }
    .error { color: red; background: #ffe6e6; padding: 10px; margin: 10px 0; border: 1px solid red; }
    input[type="submit"] { padding: 10px 20px; font-size: 16px; }
  </style>
</head>
<body>
  <h2>Book Flight</h2>
  
  <p>
    <a href="view_flight.php?id=<?php echo (int)$flight['id']; ?>">← Back</a> | 
    <a href="profile.php">My Profile</a> | 
    <a href="search.php">Search Flights</a>
  </p>
  
  <?php if(!empty($err)): ?>
    <div class="error"><?php echo htmlspecialchars($err); ?></div>
  <?php endif; ?>
  
  <h3>Flight Details</h3>
  <table>
    <tr>
      <td><strong>Flight Code:</strong></td>
      <td><?php echo htmlspecialchars($flight['flight_code']); ?></td>
    </tr>
    <tr>
      <td><strong>Route:</strong></td>
      <td><?php echo htmlspecialchars($flight['source']); ?> → <?php echo htmlspecialchars($flight['destination']); ?></td>
    </tr>
    <tr>
      <td><strong>Departure Date:</strong></td> <td><?php echo htmlspecialchars($flight['flight_date']); ?></td>
    </tr>
    <tr>
      <td><strong>Departure Time:</strong></td> <td><?php echo htmlspecialchars($flight['departure_time']); ?></td>
    </tr>
    <tr>
      <td><strong>Arrival Date:</strong></td> <td><?php echo htmlspecialchars($flight['arrival_date']); ?></td>
    </tr>
    <tr>
      <td><strong>Arrival Time:</strong></td> <td><?php echo htmlspecialchars($flight['arrival_time']); ?></td>
    </tr>
    <tr>
      <td><strong>Available Seats:</strong></td>
      <td><?php echo (int)$flight['seats_total'] - (int)$flight['seats_booked']; ?> / <?php echo (int)$flight['seats_total']; ?></td>
    </tr>
  </table>
  
  <form method="post" action="book_flight.php?id=<?php echo (int)$flight['id']; ?>">
    <input type="submit" value="Confirm Booking">
  </form>
</body>
</html>

