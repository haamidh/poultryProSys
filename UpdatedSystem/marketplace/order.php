<?php


session_start();
echo 'this page is working';
echo '<pre>';
print_r($_SESSION['order_details']);
echo '</pre>';