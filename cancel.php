<?php
require 'functions.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
if (isset($_GET['booking_id'])) {
    $bid = (int)$_GET['booking_id'];
    
    // Use prepared statements for security
    $stmt = $mysqli->prepare("SELECT flight_id, status FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $bid, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res && $res->num_rows) {
        $b = $res->fetch_assoc();
        $flight_id = (int)$b['flight_id'];
        
        if ($b['status'] === 'CONFIRMED') {
            $stmt = $mysqli->prepare("UPDATE flights SET seats_booked = seats_booked - 1 WHERE id = ?");
            $stmt->bind_param('i', $flight_id);
            $stmt->execute();
        }
        
        $stmt = $mysqli->prepare("UPDATE bookings SET status = 'CANCELLED' WHERE id = ?");
        $stmt->bind_param('i', $bid);
        $stmt->execute();
        
        allocate_seats($flight_id);
    }
}
header('Location: profile.php');
exit;