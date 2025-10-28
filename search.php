<?php
require 'config.php';
if(empty($_SESSION['user_id'])) header('Location: login.php');


// Pagination parameters
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 6; // Flights per page - chose 6 because it fits nicely in 2 rows
$offset = ($page - 1) * $limit; // learned this formula from a tutorial

// Build search conditions with proper validation
$where = [];
$params = [];
$types = '';

if(!empty($_GET['source'])) {
    $where[] = "source LIKE ?";
    $params[] = '%' . trim($_GET['source']) . '%';
    $types .= 's';
}
if(!empty($_GET['destination'])) {
    $where[] = "destination LIKE ?";
    $params[] = '%' . trim($_GET['destination']) . '%';
    $types .= 's';
}
if(!empty($_GET['date'])) {
    $where[] = "flight_date = ?";
    $params[] = trim($_GET['date']);
    $types .= 's';
}
if(!empty($_GET['flight_code'])) {
    $where[] = "flight_code LIKE ?";
    $params[] = '%' . trim($_GET['flight_code']) . '%';
    $types .= 's';
}

if(!empty($_GET['flight_type'])) {
  $where[] = "flight_type = ?";
  $params[] = trim($_GET['flight_type']);
  $types .= 's';
}

if(!empty($_GET['arrival_date'])) {
  $where[] = "arrival_date = ?";
  $params[] = trim($_GET['arrival_date']);
  $types .= 's';
}
// Build base query
$whereClause = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';
$baseQuery = "SELECT * FROM flights" . $whereClause . " ORDER BY flight_date ASC";

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM flights" . $whereClause;
$countStmt = $mysqli->prepare($countQuery);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalFlights = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalFlights / $limit);

// Get paginated results
$query = $baseQuery . " LIMIT ? OFFSET ?";
$stmt = $mysqli->prepare($query);
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$flights = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Search Flights</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
    input[type="text"], input[type="date"] { padding: 5px; }
    input[type="submit"] { padding: 6px 15px; margin-top: 10px; }
  </style>
</head>
<body>
  <h2>Flight Ticket Booking System - Search Flights</h2>
  
  <p>
    <?php if (isLoggedIn()): ?>
      <a href="profile.php">My Profile</a> | 
      <a href="index.php">Home</a> | 
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a> | 
      <a href="register.php">Register</a> | 
      <a href="index.php">Home</a>
    <?php endif; ?>
  </p>
  
  <h3>Search Filters</h3>
  <form method="get" action="search.php">
    Source: <input name="source" type="text" value="<?php echo isset($_GET['source'])?htmlspecialchars($_GET['source']):''; ?>">
    Destination: <input name="destination" type="text" value="<?php echo isset($_GET['destination'])?htmlspecialchars($_GET['destination']):''; ?>">
    Date: <input name="date" type="date" value="<?php echo isset($_GET['date'])?htmlspecialchars($_GET['date']):''; ?>">
    Flight Code: <input name="flight_code" type="text" value="<?php echo isset($_GET['flight_code'])?htmlspecialchars($_GET['flight_code']):''; ?>">
    Flight Type: <select name="flight_type">
      <option value="">All Types</option>
      <option value="Domestic" <?php if (!empty($_GET['flight_type']) && $_GET['flight_type'] == 'Domestic') echo 'selected'; ?>>Domestic</option>
      <option value="International" <?php if (!empty($_GET['flight_type']) && $_GET['flight_type'] == 'International') echo 'selected'; ?>>International</option>
    </select>
    Arrival Date: <input name="arrival_date" type="date" value="<?php echo isset($_GET['arrival_date'])?htmlspecialchars($_GET['arrival_date']):''; ?>">
    <input type="submit" value="Search">
    <a href="search.php">Clear</a>
  </form>
  
  <h3>Available Flights (<?php echo $totalFlights; ?> found)</h3>
  
  <?php if (!empty($flights)): ?>
    <table>
    <table>
      <tr>
        <th>Flight Code</th>
        <th>Source</th>
        <th>Destination</th>
        <th>Departure Date</th> <th>Departure</th>
        <th>Arrival</th>
        <th>Arrival Date</th> <th>Available Seats</th>
        <th>Action</th>
      </tr>
      <?php foreach($flights as $f): ?>
      <tr>
        <td><?php echo htmlspecialchars($f['flight_code']); ?></td>
        <td><?php echo htmlspecialchars($f['source']); ?></td>
        <td><?php echo htmlspecialchars($f['destination']); ?></td>
        <td><?php echo htmlspecialchars($f['flight_date']); ?></td>
        <td><?php echo htmlspecialchars($f['departure_time']); ?></td>
        <td><?php echo htmlspecialchars($f['arrival_time']); ?></td>
        <td><?php echo htmlspecialchars($f['arrival_date']); ?></td>
        <td><?php echo (int)$f['seats_total'] - (int)$f['seats_booked']; ?> / <?php echo (int)$f['seats_total']; ?></td>
        <td><a href="view_flight.php?id=<?php echo (int)$f['id']; ?>">View/Book</a></td>
      </tr>
      <?php endforeach; ?>
    </table>
    
    <?php if ($totalPages > 1): ?>
      <p>
        <?php if ($page > 1): ?>
          <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">« Previous</a>
        <?php endif; ?>
        
        Page <?php echo $page; ?> of <?php echo $totalPages; ?>
        
        <?php if ($page < $totalPages): ?>
          <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next »</a>
        <?php endif; ?>
      </p>
    <?php endif; ?>
  <?php else: ?>
    <p>No flights found. Try different search criteria or <a href="search.php">view all flights</a>.</p>
  <?php endif; ?>
</body>
</html>