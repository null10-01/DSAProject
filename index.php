<?php
require 'config.php';
// fetch flights
$flights_res = $mysqli->query("SELECT * FROM flights ORDER BY flight_date ASC");
$flights = $flights_res ? $flights_res->fetch_all(MYSQLI_ASSOC) : [];

// Sort flights into domestic and international
$domestic_flights = [];
$international_flights = [];
foreach ($flights as $f) {
    if ($f['flight_type'] === 'International') {
        $international_flights[] = $f;
    } else {
        $domestic_flights[] = $f;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Flight Booking System</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
  </style>
</head>
<body>
  <h2>Flight Ticket Booking System</h2>
  
  <p>
    <?php if (isLoggedIn()): ?>
      <a href="search.php">Search Flights</a> | 
      <a href="profile.php">My Profile</a> | 
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="search.php">Search Flights</a> | 
      <a href="login.php">Login</a> | 
      <a href="register.php">Register</a>
    <?php endif; ?>
  </p>
  
  <h3>Domestic Flights</h3>
  
  <?php if (!empty($domestic_flights)): ?>
    <table>
      <tr>
        <th>Flight Code</th>
        <th>Source</th>
        <th>Destination</th>
        <th>Departure Date</th>
        <th>Action</th>
      </tr>
      <?php foreach($domestic_flights as $f): ?>
      <tr>
        <td><?php echo htmlspecialchars($f['flight_code']); ?></td>
        <td><?php echo htmlspecialchars($f['source']); ?></td>
        <td><?php echo htmlspecialchars($f['destination']); ?></td>
        <td><?php echo htmlspecialchars($f['flight_date']); ?></td>
        <td><a href="view_flight.php?id=<?php echo (int)$f['id']; ?>">View Details</a></td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php else: ?>
    <p>No domestic flights available.</p>
  <?php endif; ?>
  
  <hr style="margin: 30px 0;">
  
  <h3>International Flights</h3>
  
  <?php if (!empty($international_flights)): ?>
    <table>
      <tr>
        <th>Flight Code</th>
        <th>Source</th>
        <th>Destination</th>
        <th>Departure Date</th>
        <th>Action</th>
      </tr>
      <?php foreach($international_flights as $f): ?>
      <tr>
        <td><?php echo htmlspecialchars($f['flight_code']); ?></td>
        <td><?php echo htmlspecialchars($f['source']); ?></td>
        <td><?php echo htmlspecialchars($f['destination']); ?></td>
        <td><?php echo htmlspecialchars($f['flight_date']); ?></td>
        <td><a href="view_flight.php?id=<?php echo (int)$f['id']; ?>">View Details</a></td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php else: ?>
    <p>No international flights available.</p>
  <?php endif; ?>
  
  <p><a href="search.php">→ Advanced Search</a></p>
</body>
</html>