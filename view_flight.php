<?php
require 'config.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $mysqli->prepare("SELECT * FROM flights WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$flight = $res ? $res->fetch_assoc() : null;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Flight Details</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    table { border-collapse: collapse; margin: 20px 0; }
    td { padding: 8px 15px; }
  </style>
</head>
<body>
  <h2>Flight Details</h2>
  
  <p><a href="search.php">← Back to Search</a></p>
  
  <?php if (!$flight): ?>
    <p style="color: red;">Flight not found.</p>
  <?php else: ?>
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
        <td><strong>Date:</strong></td>
        <td><?php echo htmlspecialchars($flight['flight_date']); ?></td>
      </tr>
      <tr>
        <td><strong>Departure Time:</strong></td>
        <td><?php echo htmlspecialchars($flight['departure_time']); ?></td>
      </tr>
      <tr>
        <td><strong>Arrival Time:</strong></td>
        <td><?php echo htmlspecialchars($flight['arrival_time']); ?></td>
      </tr>
      <tr>
        <td><strong>Total Seats:</strong></td>
        <td><?php echo (int)$flight['seats_total']; ?></td>
      </tr>
      <tr>
        <td><strong>Seats Booked:</strong></td>
        <td><?php echo (int)$flight['seats_booked']; ?></td>
      </tr>
      <tr>
        <td><strong>Available Seats:</strong></td>
        <td><?php echo (int)$flight['seats_total'] - (int)$flight['seats_booked']; ?></td>
      </tr>
    </table>
    
    <?php if (!empty($_SESSION['user_id'])): ?>
      <p><a href="book_flight.php?id=<?php echo (int)$flight['id']; ?>"><strong>→ Book This Flight</strong></a></p>
    <?php else: ?>
      <p><a href="login.php">Login to book this flight</a></p>
    <?php endif; ?>
  <?php endif; ?>
</body>
</html>