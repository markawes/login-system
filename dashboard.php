<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

$query = "SELECT last_login, last_ip FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $last_login = date('l, F j, Y, g:i A', strtotime($row['last_login']));
  $last_ip = $row['last_ip'];
} else {
  $last_login = "Unknown";
  $last_ip = "Unknown";
}
$stmt->close();

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <?php include("includes/nav.php");?>
  <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
  <div class="login-info-bar">
   Last login: <?php echo $last_login; ?> | IP: <?php echo $last_ip; ?>
  </div>
  <p>This is your dashboard.</p>
</body>
</html>







