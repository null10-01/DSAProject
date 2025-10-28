<?php
require 'config.php';
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $err = "Invalid request. Please try again.";
    } else {
        // Input validation rules
        $validationRules = [
            'name' => ['required' => true, 'min_length' => 2, 'max_length' => 100],
            'email' => ['required' => true, 'type' => 'email', 'max_length' => 150],
            'password' => ['required' => true, 'min_length' => 6]
        ];
        
        $errors = validateInput($_POST, $validationRules);
        
        if (!empty($errors)) {
            $err = implode(', ', $errors);
        } else {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $pass = $_POST['password'];
            $priority = 1; // All new users start with priority level 1
            
            // Check if email already exists
            $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $err = "Email already exists. Please use a different email.";
            } else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                
                $stmt = $mysqli->prepare("INSERT INTO users (name,email,password,priority_level) VALUES (?,?,?,?)");
                $stmt->bind_param('sssi', $name, $email, $hash, $priority);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Registration successful! Please login.";
                    header('Location: login.php');
                    exit;
                } else {
                    $err = "Registration failed. Please try again.";
                    error_log("Registration error: " . $stmt->error);
                }
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register - Flight Booking</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    table { border-collapse: collapse; margin: 20px 0; }
    input[type="text"], input[type="email"], input[type="password"] { width: 250px; padding: 5px; }
    input[type="submit"] { padding: 8px 20px; margin-top: 10px; }
    .error { color: red; background: #ffe6e6; padding: 10px; margin: 10px 0; border: 1px solid red; }
  </style>
</head>
<body>
  <h2>Flight Booking System - Register</h2>
  
  <?php if(!empty($err)): ?>
    <div class="error"><?php echo htmlspecialchars($err); ?></div>
  <?php endif; ?>
  
  <form method="post" action="register.php">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    <table>
      <tr>
        <td>Name:</td>
        <td><input name="name" type="text" required value="<?php echo isset($_POST['name'])?htmlspecialchars($_POST['name']):''; ?>"></td>
      </tr>
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
        <td><input type="submit" value="Register"></td>
      </tr>
    </table>
  </form>
  
  <p><a href="login.php">Already have an account? Sign in</a> | <a href="index.php">Home</a></p>
</body>
</html>