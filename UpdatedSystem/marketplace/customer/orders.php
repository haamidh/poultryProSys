<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../classes/config.php';
require_once '../../classes/checkLogin.php';
require_once 'CustomerFrame.php';
require_once '../../classes/Order.php';
require_once '../../classes/Product.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$customer = CheckLogin::checkLoginAndRole($user_id, 'customer');
$frame = new CustomerFrame();
$frame->first_part($customer);

$database = new Database();
$db = $database->getConnection();

$order = new Order($db);
$orders = $order->getCustomerOrders($user_id);
$products = new Product($db);

?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto overlay-container">
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-body">
                <h3 class="card-title text-center mb-4" style="font-family: 'Georgia', serif;">Your Orders</h3>

                <?php if (!empty($orders)) : ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Order Date</th>
                                    <th scope="col">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order) : ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($order['order_num']); ?></td>
                                        <td><?php echo htmlspecialchars($products->getProductName($order['product_id'])); ?></td>
                                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                        <td class="fw-bold">Rs <?php echo htmlspecialchars(number_format($order['total'], 2)); ?></td>
                                        <td>
                                            <?php
                                            // Determine status display based on the value of 'status'
                                            if ($order['status'] == 1) {
                                                $statusClass = 'badge bg-success';
                                                $statusText = 'Success';
                                            } elseif ($order['status'] == 0) {
                                                $statusClass = 'badge bg-danger';
                                                $statusText = 'Cancelled';
                                            } else {
                                                $statusClass = 'badge bg-secondary';
                                                $statusText = 'Pending';
                                            }
                                            ?>
                                            <span class="<?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($statusText); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($order['ordered_date']))); ?></td>
                                        <td><a href="orderReceipt.php?order_num=<?php echo $order['order_num']; ?>&action=download" class="btn btn-success mx-2">Download PDF</a><a href="orderReceipt.php?order_num=<?php echo $order['order_num']; ?>" class="btn btn-success">View PDF</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="alert alert-info text-center" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i> No orders found.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- <?php
        //$frame->last_part();
        ?> -->
