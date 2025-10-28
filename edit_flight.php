<?php
require 'config.php';
if (empty($_SESSION['is_admin'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $mysqli->prepare("SELECT * FROM flights WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$flight = $res ? $res->fetch_assoc() : null;

if (!$flight) {
    header('Location: admin_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = sanitize($_POST['flight_code']);
    $src = sanitize($_POST['source']);
    $dst = sanitize($_POST['destination']);
    $type = sanitize($_POST['flight_type']);
    $date = sanitize($_POST['flight_date']);
    $dep = sanitize($_POST['departure_time']);
    $arr = sanitize($_POST['arrival_time']);
    $arr_date = sanitize($_POST['arrival_date']); // <-- ADD THIS
    $seats = (int)$_POST['seats_total'];

    $stmt = $mysqli->prepare("UPDATE flights SET flight_code=?, source=?, destination=?, flight_type=?, flight_date=?, departure_time=?, arrival_time=?, arrival_date=?, seats_total=? WHERE id=?"); // <-- ADDED field
    $stmt->bind_param('sssssssii', $code, $src, $dst, $type, $date, $dep, $arr, $arr_date, $seats, $id); // <-- ADDED 's' and variable
    
    if ($stmt->execute()) {
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $err = 'Error updating flight: ' . $mysqli->error;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Flight - Admin</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    table { border-collapse: collapse; margin: 20px 0; }
    input[type="text"], input[type="date"], input[type="time"], input[type="number"] { width: 250px; padding: 5px; }
    input[type="submit"] { padding: 8px 20px; margin-top: 10px; }
    .error { color: red; background: #ffe6e6; padding: 10px; margin: 10px 0; border: 1px solid red; }
  </style>
</head>
<body>
  <h2>Edit Flight - Admin Panel</h2>
  
  <p>
    <a href="admin_dashboard.php">← Back to Dashboard</a> | 
    <a href="manage_users.php">Manage Users</a> | 
    <a href="logout.php">Logout</a>
  </p>
  
  <?php if(!empty($err)): ?>
    <div class="error"><?php echo htmlspecialchars($err); ?></div>
  <?php endif; ?>
  
  <form method="post" action="edit_flight.php?id=<?php echo (int)$flight['id']; ?>">
    <table>
      <tr>
        <td>Flight Code:</td>
        <td><input name="flight_code" type="text" required value="<?php echo htmlspecialchars($flight['flight_code']); ?>"></td>
      </tr>
      <tr>
        <td>Source:</td>
        <td><input name="source" type="text" required value="<?php echo htmlspecialchars($flight['source']); ?>"></td>
      </tr>
      <tr>
        <td>Destination:</td>
        <td><input name="destination" type="text" required value="<?php echo htmlspecialchars($flight['destination']); ?>"></td>
      </tr>
      <tr>
        <td>Flight Type:</td>
        <td>
          <select name="flight_type" required>
          <option value="Domestic" <?php if ($flight['flight_type'] === 'Domestic') echo 'selected'; ?>>Domestic</option>
          <option value="International" <?php if ($flight['flight_type'] === 'International') echo 'selected'; ?>>International</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Flight Date:</td>
        <td><input name="flight_date" type="date" required value="<?php echo htmlspecialchars($flight['flight_date']); ?>"></td>
      </tr>
      <tr>
        <td>Departure Time:</td>
        <td><input name="departure_time" type="time" value="<?php echo htmlspecialchars($flight['departure_time']); ?>"></td>
      </tr>
      <tr>
        <td>Arrival Time:</td>
        <td><input name="arrival_time" type="time" value="<?php echo htmlspecialchars($flight['arrival_time']); ?>"></td>
      </tr>
      <tr>
        <td>Arrival Date:</td>
        <td><input name="arrival_date" type="date" required value="<?php echo htmlspecialchars($flight['arrival_date']); ?>"></td>
      </tr>
      <tr>
        <td>Total Seats:</td>
        <td><input name="seats_total" type="number" min="1" required value="<?php echo (int)$flight['seats_total']; ?>"></td>
      </tr>
      <tr>
        <td></td>
        <td>
          <input type="submit" value="Update Flight">
          <a href="admin_dashboard.php">Cancel</a>
        </td>
      </tr>
    </table>
  </form>
</body>
</html>