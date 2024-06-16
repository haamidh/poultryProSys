<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

$customer = CheckLogin::checkLoginAndRole($user_id, 'customer');

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoultryPro: Poultry Farm Management Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .navbar-nav .nav-item a {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header pt-1 navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#" style="font-weight: bold; font-size: 20px;">
            <?php echo strtoupper(htmlspecialchars($customer['username'])); ?>
        </a><br>
        <a style="font-weight: bold;">
            <?php echo strtoupper(htmlspecialchars($customer['address']) . " - " . htmlspecialchars($customer['city'])); ?>
        </a>
    </div>
</body>

</html>

<?php
echo "Customer page updated soon";
?>