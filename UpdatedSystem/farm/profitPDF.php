<?php

// Start session if not started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once "../classes/config.php";
require_once "../classes/checkLogin.php";
require_once "../classes/Expenses.php";
require_once "../classes/Incomes.php";
require_once "../classes/Stocks.php";
require_once dirname(__FILE__) . '/../vendor/autoload.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farm') {
    header('Location: ../login.php');
    exit();
}

// Get user ID from session
$user_id = $_SESSION["user_id"];

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

// Use the actual farm details from the $farm array
$farm_name = $farm['name'];
$address = $farm['address'];

// Get 'from' and 'to' dates from the form submission, if available
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate the Product class
$product = new Product($db);

// Instantiate Incomes, Expenses, and Stocks classes
$incomes = new Incomes($db, $farm['user_id'], $product, $from_date, $to_date);
$expenses = new Expenses($db, $user_id, $from_date, $to_date);
$stocks = new Stocks($db, $user_id, $from_date, $to_date);

$action = isset($_GET['action']) && $_GET['action'] === 'download' ? 'D' : 'I';

// Fetch data from Incomes, Expenses, and Stocks classes
$incomeData = $incomes->getAllData();
$totalIncome = $incomes->getTotalAmount();

$expensesDataCategorized = $expenses->getAllDataCategorized();
$totalExpense = $expenses->getTotalAmount();

$stocksDataCategorized = $stocks->getAllDataCategorized($from_date, $to_date);
$totalStocks = $stocks->getTotalStockValue($from_date, $to_date);

$profitLoss = $totalIncome + $totalStocks - $totalExpense;

// Generate the HTML content
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profit Report</title>
    <style>
        body {
            font-family: sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo" style="text-align: center;">
            <a class="navbar-brand mx-5">
                <img src="../images/logo-poultryPro2.jpeg" alt="logo-poultryPro" style="border-radius: 50%; width: 40px; height: 30px;">
                <span style="font-size: 25px; font-weight: bold;">PoultryPro</span>
            </a>
        </div>
        <p>Farm Name&nbsp;: &nbsp;' . htmlspecialchars($farm_name) . '</p>
        <p>Address&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; ' . htmlspecialchars($address) . '</p>
        <p>From&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;' . htmlspecialchars($from_date) . '</p>
        <p>To&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;' . htmlspecialchars($to_date) . '</p>
    </div>

    <h2 style="text-align:center;">Profit & Loss Statement</h2>

    <table class="table">
        <thead>
            <tr><th colspan="5">Incomes</th></tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" style="padding-left:150px;">Total Orders</td>
                <td class="text-right">' . number_format($totalIncome, 2) . '</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-left:150px;"><strong>Total Income</strong></td>
                <td class="text-right"><strong>' . number_format($totalIncome, 2) . '</strong></td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <table class="table">
        <thead>
            <tr><th colspan="5">Expenses</th></tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" style="padding-left:150px;">Total Birds</td>
                <td class="text-right">' . number_format($expensesDataCategorized['total_birds'], 2) . '</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-left:150px;">Total Medicine</td>
                <td class="text-right">' . number_format($expensesDataCategorized['total_medicine'], 2) . '</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-left:150px;">Total Feeds</td>
                <td class="text-right">' . number_format($expensesDataCategorized['total_feeds'], 2) . '</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-left:150px;">Total Miscellaneous</td>
                <td class="text-right">' . number_format($expensesDataCategorized['total_miscellaneous'], 2) . '</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-left:150px;"><strong>Total Expenses</strong></td>
                <td class="text-right"><strong>' . number_format($totalExpense, 2) . '</strong></td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <table class="table">
        <thead>
            <tr><th colspan="5">Stocks</th></tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" style="padding-left:150px;">Total Birds Stock Value</td>
                <td class="text-right">' . number_format($stocksDataCategorized['total_bird_stock_amount'], 2) . '</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-left:150px;">Total Medicine Stock Value</td>
                <td class="text-right">' . number_format($stocksDataCategorized['total_medicine_stock_amount'], 2) . '</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-left:150px;">Total Feeds Stock Value</td>
                <td class="text-right">' . number_format($stocksDataCategorized['total_feed_stock_amount'], 2) . '</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-left:150px;"><strong>Total Stocks Value</strong></td>
                <td class="text-right"><strong>' . number_format($totalStocks, 2) . '</strong></td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <h3>Profit/Loss</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Total Income</th>
                <th>Total Expenses</th>
                <th>Stock Available</th>
                <th>Profit/Loss</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-right">' . number_format($totalIncome, 2) . '</td>
                <td class="text-right">' . number_format($totalExpense, 2) . '</td>
                <td class="text-right">' . number_format($totalStocks, 2) . '</td>
                <td class="text-right"><strong>' . number_format($profitLoss, 2) . '</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>';

// Generate PDF using Mpdf
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output('profit_loss_report.pdf', $action);

?>
