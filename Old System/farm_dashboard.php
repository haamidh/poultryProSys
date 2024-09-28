<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';
require_once 'Expenses.php';
require_once 'Incomes.php';
require_once 'Stocks.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];;

$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);

$database = new Database();
$db = $database->getConnection();

$from_date = date('Y-m-d');
$to_date = date('Y-m-d');
$expenses = new Expenses($db, $farm['user_id'], $from_date, $to_date);
$total_expenses = $expenses->getTotalAmount();
$incomes = new Incomes($db, $farm['user_id'], $from_date, $to_date);
$total_incomes = $incomes->getTotalAmount();
$stocks = new Stocks($db, $farm['user_id'], $from_date, $to_date);
$total_stocks = $stocks->getTotalStockAmount();
$total_profit = $total_incomes + $total_stocks - $total_expenses;

?>

<div class="contentArea">

    <div class="row2" style="height: 200px">
        <div class="col">
            <h5 style="height: 130px;padding-top: 50px;background-color: #9B59B6;font-weight:bold;font-size: 13px;color:white;">TODAY ORDERS<br></h5>
            <hr class="dropdown-divider" style="font-weight: bold;color:black">
            <h5 style="background-color: #D4C8DE; height: 50px;padding-top: 8px"><a href="" style="color:black;">More Details</a></h5>
        </div>
        <div class="col" style="margin-left:20px;">
            <h5 style="height: 130px;padding-top: 50px;background-color:#989E12;font-weight:bold;font-size: 13px;color:white;">TODAY INCOME<br><strong style="font-size: 20px;font-weight:bold;color:white;">RS. <?php echo number_format($total_incomes, 2); ?></strong></h5>
            <h5 style="background-color: #F1F4B0; height: 50px;padding-top: 8px"><a href="farm_incomes.php" style="color:black;">More Details</a></h5>
        </div>
        <div class="col" style="margin-left:20px;">
            <h5 style="height: 130px;padding-top: 50px;background-color:#B71717;font-weight:bold;font-size: 13px;color:white;">TODAY EXPENSES<br><strong style="font-size: 20px;font-weight:bold;color:white;">RS. <?php echo number_format($total_expenses, 2); ?></strong></h5>
            <h5 style="background-color: #F0A9A9; height: 50px;padding-top: 8px"><a href="farm_expenses.php" style="color:black;">More Details</a></h5>
        </div>
        <div class="col" style="margin-left:20px;">
            <h5 style="height: 130px;padding-top: 50px;background-color: #1E8449;font-weight:bold;font-size: 13px;color:white;">TODAY PROFIT<br><strong style="font-size: 20px;font-weight:bold;color:white;">RS. <?php echo number_format($total_profit, 2); ?></strong></h5>
            <h5 style="background-color: #D4EAE2; height: 50px;padding-top: 8px"><a href="farm_incomes.php" style="color:black;">More Details</a></h5>
        </div>
    </div>


    <hr class="dropdown-divider" style="color:black">

    <div class="row3">
        <h1>PRICE LIST</h1>
    </div>

</div>

<?php
$frame->last_part();
?>