<?php
require_once 'marketplaceFrame.php';
require 'config.php';

$marketPlaceFrame = new marketPlaceFrame();
$marketPlaceFrame->navbar();

$db = new Database();
$conn = $db->getConnection();
$product_id = $_GET["product_id"];

$sql = "SELECT product_id,product_name, quantity, category_id, product_price, product_img, description FROM products";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

?>

