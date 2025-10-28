<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    
    // Input validation - learned about this from a security tutorial
    if (empty($email) || empty($pass)) {
        $err = "Email and password are required!";
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = "Please enter a valid email address!";
    } 
    else {
        // Use prepared statement to prevent SQL injection
        $stmt = $mysqli->prepare("SELECT id, password, is_admin, name FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows) {
            $u = $result->fetch_assoc();
            if (password_verify($pass, $u['password'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);
                $_SESSION['user_id'] = $u['id'];
                $_SESSION['is_admin'] = $u['is_admin'];
                $_SESSION['user_name'] = $u['name'];
                $_SESSION['login_time'] = time();
                
                if ($u['is_admin']) {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: search.php');
                }
                exit;
            }
        }
        $err = "Invalid credentials!";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login - Flight Ticket Booking</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    table { border-collapse: collapse; margin: 20px 0; }
    input[type="text"], input[type="email"], input[type="password"] { width: 250px; padding: 5px; }
    input[type="submit"] { padding: 8px 20px; margin-top: 10px; }
    .error { color: red; background: #ffe6e6; padding: 10px; margin: 10px 0; border: 1px solid red; }
    .success { color: green; background: #e6ffe6; padding: 10px; margin: 10px 0; border: 1px solid green; }
  </style>
</head>
<body>
  <h2>Flight Ticket Booking System - Login</h2>
  
  <?php if(!empty($err)): ?>
    <div class="error"><?php echo htmlspecialchars($err); ?></div>
  <?php endif; ?>
  
  <?php if(!empty($_SESSION['success_message'])): ?>
    <div class="success"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
  <?php endif; ?>
  
  <form method="post" action="login.php">
    <table>
      <tr>
        <td>Email:</td>
        <td><input name="email" type="email" required value="<?php echo isset($_POST['email'])?htmlspecialchars($_POST['email']):''; ?>"></td>
      </tr>
      <tr>
        <td>Password:</td>
        <td><input name="password" type="password" required></td>
      </tr>
      <tr>
        <td></td>
        <td><input type="submit" value="Login"></td>
      </tr>
    </table>
  </form>
  
  <p><a href="register.php">Create an account</a> | <a href="index.php">Home</a></p>
</body>
</html>