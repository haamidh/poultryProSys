<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/User.php';
require_once '../classes/Order.php';

require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$database = new Database();
$db = $database->getConnection();

// Handling date filters
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : null;
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : null;

$user = new User($db);
$users = $user->getAllCustomers();

$admin = CheckLogin::checkLoginAndRole($user_id, 'admin');

$adminframe = new AdminFrame();
$adminframe->first_part($admin);

$orders = new Order($db);
$order_payments = $orders->getAllServiceFees($from_date, $to_date);

// Initialize total income
$total_income = 0;
?>

<!-- Custom Styling -->
<style>
    /* Add your existing CSS here */
</style>

<div class="contentArea">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><strong>Income Details</strong></h5>
                    </div>
                    <div class="card-body">
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
                                <div class="col-lg-4 col-md-12 d-flex align-items-end justify-content-center">
                                    <button type="submit" class="btn btn-primary fs-6">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped table-hover text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($order_payments) {
                                    $uid = 1;
                                    foreach ($order_payments as $payment) {
                                        // Sum up the service fees
                                        $total_income += $payment['service_fee'];
                                        ?>
                                        <tr>
                                            <td><?php echo $uid; ?></td>
                                            <td><?php echo htmlspecialchars($payment['order_num']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['service_fee']); ?></td>
                                            <td><?php echo htmlspecialchars(date("d M Y", strtotime($payment['payment_date']))); ?></td>
                                        </tr>
                                        <?php
                                        $uid++;
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="4" class="no-data">No Incomes found</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>

                        <!-- Display Total Income -->
                        <div class="mt-4">
                            <h5><strong>Total Income: <?php echo number_format($total_income, 2); ?> </strong></h5>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$adminframe->last_part();
?>
