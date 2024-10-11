<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once 'Frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Stocks.php';
require_once '../classes/Feed.php';
require_once '../classes/UseFeed.php';
require_once '../classes/BuyFeed.php';
require_once '../classes/Medicine.php';
require_once '../classes/UseMedicine.php';
require_once '../classes/BuyMedicine.php';
require_once '../classes/Product.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);

// Get "from" and "to" date from the submission, if available
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

$database = new Database();
$db = $database->getConnection();

$stocks = new Stocks($db, $farm['user_id']);

// Fetch data from Stocks class
$feed_data = $stocks->getAllStockData($from_date, $to_date);
$total_feed_amount = $stocks->getTotalStockAmount($from_date, $to_date);

$medicine_data = $stocks->getAllMedicineStockData($from_date, $to_date);
$total_medicine_amount = $stocks->getTotalMedicineStockAmount($from_date, $to_date);

$product_data = $stocks->getAllProductStockData($from_date, $to_date);
$total_product_amount = $stocks->getTotalProductStockAmount($from_date, $to_date);
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container-fluid px-4">
        <div class="row py-5">
            <div class="col-md-12">
                <div class="card shadow-lg border-0 mb-5">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Stock Details</h3>
                        <span class="text-light fs-5">Report Date: <?php echo date('F j, Y'); ?></span>
                    </div>
                    <div class="card-body p-4">
                        <form method="GET" action="" class="mb-4">
                            <div class="row g-3">
                                <div class="col-lg-4 col-md-6">
                                    <label for="from_date" class="form-label">From Date:</label>
                                    <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" class="form-control">
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label for="to_date" class="form-label">To Date:</label>
                                    <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" class="form-control">
                                </div>
                                <div class="col-lg-4 col-md-12 d-flex align-items-end justify-content-between">
                                    <button type="submit" class="btn btn-primary fs-6">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <a href="stockPDF.php?from_date=<?php echo htmlspecialchars($from_date); ?>&to_date=<?php echo htmlspecialchars($to_date); ?>&action=download" class="btn btn-danger fs-6 text-light">
                                        <i class="bi bi-file-earmark-arrow-down"></i> Download
                                    </a>
                                    <a href="stockPDF.php?from_date=<?php echo htmlspecialchars($from_date); ?>&to_date=<?php echo htmlspecialchars($to_date); ?>" class="btn btn-success fs-6 text-light">
                                        <i class="bi bi-file-earmark-text"></i> View PDF
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Feed Stock Table -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h4 class="mb-3 text-dark">Feed Stock</h4>
                                <table class="table table-striped table-hover text-center align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Stock Detail</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($feed_data)) {
                                            $uid = 1;
                                            foreach ($feed_data as $data) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $uid; ?></td>
                                                    <td class="text-start"><?php echo htmlspecialchars($data['detail']); ?></td>
                                                    <td><?php echo number_format($data['quantity'], 2); ?></td>
                                                    <td><?php echo number_format($data['amount'], 2); ?></td>
                                                </tr>
        <?php $uid++;
    } ?>
                                            <tr class="fw-bold">
                                                <td colspan="3">Total Value</td>
                                                <td><?php echo number_format($total_feed_amount, 2); ?></td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="4" class="text-muted">No feed data found</td>
                                            </tr>
<?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Medicine Stock Table -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h4 class="mb-3 text-dark">Medicine Stock</h4>
                                <table class="table table-striped table-hover text-center align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Stock Detail</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
if (!empty($medicine_data)) {
    $uid = 1;
    foreach ($medicine_data as $data) {
        ?>
                                                <tr>
                                                    <td><?php echo $uid; ?></td>
                                                    <td class="text-start"><?php echo htmlspecialchars($data['detail']); ?></td>
                                                    <td><?php echo number_format($data['quantity'], 2); ?></td>
                                                    <td><?php echo number_format($data['amount'], 2); ?></td>
                                                </tr>
                                                <?php $uid++;
                                            } ?>
                                            <tr class="fw-bold">
                                                <td colspan="3">Total Value</td>
                                                <td><?php echo number_format($total_medicine_amount, 2); ?></td>
                                            </tr>
<?php } else { ?>
                                            <tr>
                                                <td colspan="4" class="text-muted">No medicine data found</td>
                                            </tr>
<?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Product Stock Table -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h4 class="mb-3 text-dark">Product Stock</h4>
                                <table class="table table-striped table-hover text-center align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Stock Detail</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
if (!empty($product_data)) {
    $uid = 1;
    foreach ($product_data as $data) {
        ?>
                                                <tr>
                                                    <td><?php echo $uid; ?></td>
                                                    <td class="text-start"><?php echo htmlspecialchars($data['detail']); ?></td>
                                                    <td><?php echo number_format($data['quantity'], 2); ?></td>
                                                    <td><?php echo number_format($data['amount'], 2); ?></td>
                                                </tr>
                                                <?php $uid++;
                                            } ?>
                                            <tr class="fw-bold">
                                                <td colspan="3">Total Value</td>
                                                <td><?php echo number_format($total_product_amount, 2); ?></td>
                                            </tr>
<?php } else { ?>
                                            <tr>
                                                <td colspan="4" class="text-muted">No product data found</td>
                                            </tr>
<?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$frame->last_part();
?> 
