<?php
session_start();
include 'includes/config.php';

function validate_input($input) {
  $input = trim($input);
  $input = stripslashes($input);
  $input = htmlspecialchars($input);
  return $input;
}

if (isset($_POST['login'])) {
  if ($_SESSION['csrf_token'] === $_POST['csrf_token']) {
    $username = validate_input($_POST['username']);
    $password = validate_input($_POST['password']);

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      if (password_verify($password, $row['password'])) {
        $_SESSION['username'] = $username;

$last_login = date("Y-m-d H:i:s");
$last_ip = $_SERVER['REMOTE_ADDR'];

$update_query = "UPDATE users SET last_login = ?, last_ip = ? WHERE username = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("sss", $last_login, $last_ip, $username);
$update_stmt->execute();
$update_stmt->close();

header("Location: dashboard.php");

        exit;
      } else {
        $error_message = "Invalid username or password!";
      }
    } else {
        $error_message = "Invalid username or password!";
    }

    $stmt->close();
  } else {
    $error_message = "Invalid CSRF token!";
  }
}

$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="css/global.css">
</head>
<body>
  <h1>Login</h1>
  <form method="post" action="login.php">
    <label>Username:</label>
    <input type="text" name="username" required>
    <br>
    <label>Password:</label>
    <input type="password" name="password" required>
    <br>
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="submit" name="login" value="Login">
    <br></br>
    <input type="submit" value="Need an account? [Register]" onclick="location.href='register.php'">
    <br></br>
    <div id="error-message"><?php if (isset($error_message)) echo $error_message; ?></div>
  </form>
</body>
</html>
