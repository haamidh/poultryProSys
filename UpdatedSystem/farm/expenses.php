<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once 'frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Expenses.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
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

// Instantiate the Expenses class
$expenses = new Expenses($db, $farm['user_id'], $from_date, $to_date);

// Fetch all data and total amount
$all_data = $expenses->getAllData();
$total_amount = $expenses->getTotalAmount();
$has_data = !empty($all_data);
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row py-5 px-3">
            <div class="col-md-12">
                <div class="card shadow-lg border-0 rounded">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Expenses Details</h3>
                        <span class="text-light fs-5">Report Date: <?php echo date('F j, Y'); ?></span>
                    </div>
                    <div class="card-body p-4">
                        <!-- Date filter form -->
                        <form method="GET" action="" class="mb-4">
                            <div class="row g-3">
                                <div class="col-lg-4 col-md-6">
                                    <label for="from_date">From Date:</label>
                                    <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" class="form-control">
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <label for="to_date">To Date:</label>
                                    <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" class="form-control">
                                </div>
                                <div class="col-lg-4 col-md-12 d-flex align-items-end justify-content-between">
                                    <button type="submit" class="btn btn-primary fs-6">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <a href="expensesPDF.php?from_date=<?php echo htmlspecialchars($from_date); ?>&to_date=<?php echo htmlspecialchars($to_date); ?>&action=download" class="btn btn-danger fs-6 text-light">
                                        <i class="bi bi-file-earmark-arrow-down"></i> Download
                                    </a>
                                    <a href="expensesPDF.php?from_date=<?php echo htmlspecialchars($from_date); ?>&to_date=<?php echo htmlspecialchars($to_date); ?>" class="btn btn-success fs-6 text-light">
                                        <i class="bi bi-file-earmark-text"></i> View PDF
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table displaying expenses details -->
                    <div class="table-responsive px-4 py-2">
                        <table class="table table-striped table-hover text-center align-middle">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Payment Date</th>
                                    <th>Payment Detail</th>
                                    
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($has_data) {
                                    $uid = 1;
                                    foreach ($all_data as $data) {
                                        ?>
                                        <tr>
                                            <td><?php echo $uid ?></td>
                                            <td><?php echo date('F j, Y', strtotime($data['date'])) ?></td>
                                            <td><?php echo htmlspecialchars($data['detail']) ?></td>
                                            
                                            <td class="text-end"><?php echo number_format($data['amount'], 2) ?></td>
                                        </tr>
                                        <?php
                                        $uid++;
                                    }
                                    ?>
                                    <!-- Total amount row -->
                                    <tr>
                                        <td colspan="3" ><strong>Total Amount</strong></td>
                                        <td class="text-end"><strong><?php echo number_format($total_amount, 2); ?></strong></td>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No data found</td>
                                    </tr>
                                <?php } ?>
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
