<?php
session_start();
include 'includes/config.php';

function validate_input($input) {
  $input = trim($input);
  $input = stripslashes($input);
  $input = htmlspecialchars($input);
  return $input;
}

if (isset($_POST['register'])) {
  if ($_SESSION['csrf_token'] === $_POST['csrf_token']) {
    $username = validate_input($_POST['username']);
    $password = validate_input($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if a user with the same username already exists
    $check_query = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error_message = "A user with this username already exists, please pick another.";
    } else {
      $query = "INSERT INTO users (username, password) VALUES (?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $username, $hashed_password);
      if ($stmt->execute()) {
        echo "Registration successful";
      } else {
        echo "Error: " . $query . "<br>" . $conn->error;
      }
      $stmt->close();
    }
    $check_stmt->close();
  } else {
    echo "Invalid CSRF token";
  }
}

$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/global.css">
</head>
<body>
  <h1>Register</h1>
  <form method="post" action="register.php">
    <label>Username:</label>
    <input type="text" name="username" required>
    <br>
    <label>Password:</label>
    <input type="password" name="password" required>
    <br>
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="submit" name="register" value="Register">
    <br></br>
    <input type="submit" value="Have an account? [Login]" onclick="location.href='login.php'">
    <br></br>
    <div id="error-message"><?php if (isset($error_message)) echo $error_message; ?></div>
  </form>
  
</body>
</html>
