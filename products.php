<?php
if (session_status() == PHP_SESSION_NONE) session_start();

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please Login before Proceeding");
    exit();
}
$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

echo "hello world";

?>