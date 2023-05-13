<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

// Handle form submission for changing username and password
if (isset($_POST['update'])) {
  $new_username = $_POST['new_username'];
  $new_password = $_POST['new_password'];
  $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

  $query = "UPDATE users SET username = ?, password = ? WHERE username = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("sss", $new_username, $hashed_password, $_SESSION['username']);

  if ($stmt->execute()) {
    $_SESSION['username'] = $new_username;
    $success_message = "Your username and password have been updated.";
  } else {
    $error_message = "There was an error updating your username and password.";
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Account</title>
  <link rel="stylesheet" href="css/global.css">
</head>
<body>
<?php include("includes/nav.php");?>
  <h1>Change Username and Password</h1>
  <form method="post" action="update_account.php">
    <label>New Username:</label>
    <input type="text" name="new_username" required>
    <br>
    <label>New Password:</label>
    <input type="password" name="new_password" required>
    <br>
    <input type="submit" name="update" value="Update">
    <?php if (isset($success_message)): ?>
      <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
      <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>
  </form>
</body>
</html>
