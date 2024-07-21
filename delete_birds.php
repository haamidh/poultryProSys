<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'Bird.php';
require_once 'checkLogin.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$database = new Database();
$db = $database->getConnection();

$bird = new Bird($db);

if (isset($_GET['delete'])) {
    $batch_id = $_GET['delete'];
    
   
    if ($bird->delete($batch_id, $user_id)) {
        echo"hiufishgg112121";
        header("Location: birds.php");
        echo"hiufishgg";
    } else {
        echo "Failed to delete batch.";
    }
    exit();
} else {
    header("Location: birds.php");
    exit();
}
?>
