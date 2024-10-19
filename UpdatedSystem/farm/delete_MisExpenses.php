<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/MisExpenses.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?msg=Please Login before Proceeding');
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    
    // Create an instance of the Supplier class
    
    $misEx = new MisExpenses($con);
    
    // Delete the supplier using the delete method from the Supplier class
    if ($misEx ->delete($category_id)) {
        header('Location: MisExpenses.php?msg=Expenses Deleted Successfully');
    } else {
        header('Location: MisExpenses.php?msg=Failed to Delete Expenses');
    }
    exit();
} else {
    header('Location: MisExpenses.php?msg=Expense ID not provided');
    exit();
}
