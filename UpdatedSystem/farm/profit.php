<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once '../classes/config.php';
require_once 'Frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Incomes.php';
require_once '../classes/Expenses.php';
require_once '../classes/Stocks.php';
require_once '../classes/Product.php';

// Check if user is logged in and has appropriate role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION["user_id"];

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);

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

// Fetch data from Incomes, Expenses, and Stocks classes
$incomeData = $incomes->getAllData();
$totalIncome = $incomes->getTotalAmount();

$expensesDataCategorized = $expenses->getAllDataCategorized();
$totalExpense = $expenses->getTotalAmount();

$stocksDataCategorized = $stocks->getAllDataCategorized($from_date, $to_date);
$totalStocks = $stocks->getTotalStockValue($from_date, $to_date);

$profitLoss = $totalIncome + $totalStocks - $totalExpense;
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container py-4">
        <div class="row py-5 px-3">
            <div class="col-md-12">
                <div class="card shadow-lg border-0 rounded">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h3 class="mb-0 fw-bold fs-2">Profit & Loss Statement</h3>
                        <span class="text-light fs-5">Report Date: <?php echo date('F j, Y'); ?></span>
                    </div>
                    <div class="card-body ">
                        <!-- Date Filter Form -->
                        <form method="GET" action="" class="mb-4">
                            <div class="row g-3">
                                <div class="col-lg-4 col-md-6">
                                    <label for="from_date" class="form-label">From Date</label>
                                    <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" class="form-control">
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label for="to_date" class="form-label">To Date</label>
                                    <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" class="form-control">
                                </div>
                                <div class="col-lg-4 col-md-12 d-flex align-items-end justify-content-between">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <a href="profitPDF.php?from_date=<?php echo htmlspecialchars($from_date); ?>&to_date=<?php echo htmlspecialchars($to_date); ?>&action=download" class="btn btn-danger text-light">
                                        <i class="bi bi-file-earmark-arrow-down"></i> Download
                                    </a>
                                    <a href="profitPDF.php?from_date=<?php echo htmlspecialchars($from_date); ?>&to_date=<?php echo htmlspecialchars($to_date); ?>" class="btn btn-success text-light">
                                        <i class="bi bi-file-earmark-text"></i> View PDF
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Income Table -->
                        <div class="table-responsive mb-4 pt-4">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark text-center fs-3">
                                    <tr>
                                        <th colspan="5">Incomes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" class="text-start">Total Orders</td>
                                        <td class="text-end"><?php echo number_format($totalIncome, 2); ?></td>
                                    </tr>
                                    <tr class="fs-5">
                                        <td colspan="4" class="text-start fw-bold">Total Income</td>
                                        <td class="text-end fw-bold"><?php echo number_format($totalIncome, 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Expenses Table -->
                        <div class="table-responsive mb-4 pt-3">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark text-center fs-3">
                                    <tr>
                                        <th colspan="5">Expenses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" class="text-start">Total Birds</td>
                                        <td class="text-end"><?php echo number_format($expensesDataCategorized['total_birds'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-start">Total Medicine</td>
                                        <td class="text-end"><?php echo number_format($expensesDataCategorized['total_medicine'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-start">Total Feeds</td>
                                        <td class="text-end"><?php echo number_format($expensesDataCategorized['total_feeds'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-start">Total Miscellaneous</td>
                                        <td class="text-end"><?php echo number_format($expensesDataCategorized['total_miscellaneous'], 2); ?></td>
                                    </tr>
                                    <tr class="fs-5">
                                        <td colspan="4" class="text-start fw-bold">Total Expenses</td>
                                        <td class="text-end fw-bold"><?php echo number_format($totalExpense, 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Stocks Table -->
                        <div class="table-responsive mb-4 pt-3">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark text-center fs-3">
                                    <tr>
                                        <th colspan="5">Stocks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" class="text-start">Total Products Stock Value</td>
                                        <td class="text-end"><?php echo number_format($stocksDataCategorized['total_products'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-start">Total Medicine Stock Value</td>
                                        <td class="text-end"><?php echo number_format($stocksDataCategorized['total_medicine'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-start">Total Feeds Stock Value</td>
                                        <td class="text-end"><?php echo number_format($stocksDataCategorized['total_feeds'], 2); ?></td>
                                    </tr>
                                    <tr class="fs-5">
                                        <td colspan="4" class="text-start fw-bold">Total Stocks Value</td>
                                        <td class="text-end fw-bold "><?php echo number_format($totalStocks, 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Profit/Loss Summary -->
                        <h4 class="mb-3 text-dark fw-bold">Profit/Loss Summary</h4>
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark text-center ">
                                <tr>
                                    <th>Total Income</th>
                                    <th>Total Expenses</th>
                                    <th>Stock Available</th>
                                    <th class="fs-5">Profit/Loss</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center"><?php echo number_format($totalIncome, 2); ?></td>
                                    <td class="text-center"><?php echo number_format($totalExpense, 2); ?></td>
                                    <td class="text-center"><?php echo number_format($totalStocks, 2); ?></td>
                                    <td class="text-center fw-bold fs-5"><?php echo number_format($profitLoss, 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$frame->last_part();
?>

