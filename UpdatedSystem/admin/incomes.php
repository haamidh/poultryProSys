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
?>

<!-- Custom Styling -->
<style>
    /* Gradient background */
    body {
        background: linear-gradient(135deg, #3E497A, #ffffff);
        font-family: 'Poppins', sans-serif;
        margin: 0;
    }

    .contentArea {
        background-color: #f8f9fa;
        padding: 30px;
        min-height: 100vh;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .card {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 15px;
        margin-bottom: 30px;
        background: #fff;
    }

    .card-header {
        background-color: #3E497A;
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 25px;
        font-size: 24px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h5 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }

    .table {
        margin-top: 20px;
        width: 100%;
        border-spacing: 0 15px;
    }

    .table-striped tbody tr {
        transition: 0.3s;
        border-radius: 12px;
    }

    .table-striped tbody tr:hover {
        background-color: #f0f2f5;
        transform: translateY(-2px);
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 20px;
        border: none;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background-color: #3E497A;
        border-color: #3E497A;
    }

    .btn-primary:hover {
        background-color: #2c3665;
        border-color: #2c3665;
    }

    .btn i {
        margin-right: 5px;
    }

    /* Search bar */
    .search-bar {
        width: 100%;
        position: relative;
    }

    .search-bar input {
        border-radius: 10px;
        padding: 10px 15px;
        height: 45px;
        width: 100%;
        border: 1px solid #ccc;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    .search-bar i {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }

    /* No data styling */
    .no-data {
        text-align: center;
        font-size: 18px;
        color: #888;
        margin-top: 20px;
    }

    .form-control {
        height: 45px;
        border-radius: 8px;
        padding-left: 15px;
        border: 1px solid #ddd;
    }
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
                                        ?>
                                        <tr>
                                            <td><?php echo $uid; ?></td>
                                            <td><?php echo htmlspecialchars($payment['order_id']); ?></td>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$adminframe->last_part();
?>
