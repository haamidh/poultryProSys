<?php
require_once 'marketplaceFrame.php';
require 'config.php';

$marketPlaceFrame = new marketPlaceFrame();
$marketPlaceFrame->navbar();

$db = new Database();
$conn = $db->getConnection();
?>