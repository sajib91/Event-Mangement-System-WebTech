<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Debug</title>
</head>
<body>
    <h1>Session Debug Info</h1>
    <pre>
Session Data:
<?php
var_dump($_SESSION);
?>

Session ID: <?php echo session_id(); ?>

User ID: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET'; ?>
Username: <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'NOT SET'; ?>
Role: <?php echo isset($_SESSION['role']) ? $_SESSION['role'] : 'NOT SET'; ?>
Role (lowercase): <?php echo isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : 'NOT SET'; ?>

Server Request Method: <?php echo $_SERVER['REQUEST_METHOD']; ?>
    </pre>
    <a href="request_event.php">Go to Request Event</a>
</body>
</html>
