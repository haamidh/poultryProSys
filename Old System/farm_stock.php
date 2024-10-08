<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'frame.php';
require_once 'checkLogin.php';
require_once 'Stocks.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: login.php");
    exit();
}

// Retrieve the id from the session
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

// Instantiate the Stocks class
$stocks = new Stocks($db, $farm['user_id'], $from_date, $to_date);

// Fetch all data, total quantity and total amount
$all_data = $stocks->getAllStockData();
$total_quantity = $stocks->getTotalStockQuantity();
$total_amount = $stocks->getTotalStockAmount();
?>

<div class="contentArea">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>STOCK DETAILS</h3>
                    </div>
                    <div class="card-body">
                        <!-- Add date filter form -->
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col">
                                    <label for="from_date">From Date:</label>
                                    <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" class="form-control">
                                </div>
                                <div class="col">
                                    <label for="to_date">To Date:</label>
                                    <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" class="form-control">
                                </div>
                                <div class="col" style="padding-top:20px;">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <button class="btn btn-danger">
                                        <a href="farm_stockPDF.php?from_date=<?php echo htmlspecialchars($from_date); ?>&to_date=<?php echo htmlspecialchars($to_date); ?>" class="text-light">Export PDF</a>
                                    </button>
                                </div>
                            </div>
                        </form>


                        <table class="table" style="margin-top: -50px;">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Stock Detail</th>
                                    <th scope="col" style="text-align: right; padding-right: 40px;">Quantity</th>
                                    <th scope="col" style="text-align: right; padding-right: 40px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Display the sorted data
                                if (!empty($all_data)) {
                                    $uid = 1;
                                    foreach ($all_data as $data) {
                                ?>
                                        <tr>
                                            <td><?php echo $uid ?></td>
                                            <td><?php echo htmlspecialchars(substr($data['date'], 0, 10)) ?></td>
                                            <td><?php echo htmlspecialchars($data['detail']) ?></td>
                                            <td style="text-align: right; padding-right: 20px;"><?php echo number_format($data['quantity'], 2) ?></td>
                                            <td style="text-align: right; padding-right: 20px;"><?php echo number_format($data['amount'], 2) ?></td>
                                        </tr>
                                    <?php
                                        $uid++;
                                    }
                                    ?>
                                    <!-- Total quantity and amount row -->
                                    <tr>
                                        <td colspan="3" style="text-align: right; padding-right: 80px;"><strong>Total Quantity</strong></td>
                                        <td style="text-align: right; padding-right: 20px;"><strong><?php echo number_format($total_quantity, 2); ?></strong></td>
                                        <td style="text-align: right; padding-right: 20px;"><strong><?php echo number_format($total_amount, 2); ?></strong></td>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">No data found</td>
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

<?php
$frame->last_part();
?>