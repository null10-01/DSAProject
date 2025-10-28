<?php
require 'config.php';
if (empty($_SESSION['is_admin'])) {
    header('Location: login.php');
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

    $stmt = $mysqli->prepare("INSERT INTO flights (flight_code, source, destination, flight_type, flight_date, departure_time, arrival_time, arrival_date, seats_total) VALUES (?,?,?,?,?,?,?,?,?)"); // <-- ADDED field
    $stmt->bind_param('sssssssi', $code, $src, $dst, $type, $date, $dep, $arr, $arr_date, $seats); // <-- ADDED 's' and variable
    $stmt->execute();

    echo "<p>✅ Flight Added!</p><a href='admin_dashboard.php'>Go Back</a>";
    exit;
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create Flight - Admin</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    table { border-collapse: collapse; margin: 20px 0; }
    input[type="text"], input[type="date"], input[type="time"], input[type="number"] { width: 250px; padding: 5px; }
    input[type="submit"] { padding: 8px 20px; margin-top: 10px; }
    .success { color: green; background: #e6ffe6; padding: 10px; margin: 10px 0; border: 1px solid green; }
    .error { color: red; background: #ffe6e6; padding: 10px; margin: 10px 0; border: 1px solid red; }
  </style>
</head>
<body>
  <h2>Create Flight - Admin Panel</h2>
  
  <p><a href="admin_dashboard.php">← Back to Dashboard</a></p>
  
  <?php if(!empty($error)): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  
  <form method="post" action="create_flight.php">
    <table>
      <tr>
        <td>Flight Code:</td>
        <td><input name="flight_code" type="text" required value="<?php echo isset($_POST['flight_code'])?htmlspecialchars($_POST['flight_code']):''; ?>"></td>
      </tr>
      <tr>
        <td>Source:</td>
        <td><input name="source" type="text" required value="<?php echo isset($_POST['source'])?htmlspecialchars($_POST['source']):''; ?>"></td>
      </tr>
      <tr>
        <td>Destination:</td>
        <td><input name="destination" type="text" required value="<?php echo isset($_POST['destination'])?htmlspecialchars($_POST['destination']):''; ?>"></td>
      </tr>
      <tr>
        <td>Flight Type:</td>
        <td>
          <select name="flight_type" required>
              <option value="Domestic">Domestic</option>
              <option value="International">International</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Flight Date:</td>
        <td><input name="flight_date" type="date" required value="<?php echo isset($_POST['flight_date'])?htmlspecialchars($_POST['flight_date']):''; ?>"></td>
      </tr>
      <tr>
        <td>Departure Time:</td>
        <td><input name="departure_time" type="time" value="<?php echo isset($_POST['departure_time'])?htmlspecialchars($_POST['departure_time']):''; ?>"></td>
      </tr>
      <tr>
        <td>Arrival Time:</td>
        <td><input name="arrival_time" type="time" value="<?php echo isset($_POST['arrival_time'])?htmlspecialchars($_POST['arrival_time']):''; ?>"></td>
      </tr>
      <tr>
        <td>Arrival Date:</td>
        <td><input name="arrival_date" type="date" required value="<?php echo isset($_POST['arrival_date'])?htmlspecialchars($_POST['arrival_date']):''; ?>"></td>
      </tr>
      <tr>
        <td>Total Seats:</td>
        <td><input name="seats_total" type="number" min="1" required value="<?php echo isset($_POST['seats_total'])?htmlspecialchars($_POST['seats_total']):''; ?>"></td>
      </tr>
      <tr>
        <td></td>
        <td><input type="submit" value="Create Flight"></td>
      </tr>
    </table>
  </form>
</body>
</html>
