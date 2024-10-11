<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';
require_once '../classes/Expenses.php';
require_once '../classes/Incomes.php';
require_once '../classes/Stocks.php';
require_once '../classes/Product.php';
require_once '../classes/Order.php';
require_once '../classes/Notification.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];


$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);

$database = new Database();
$db = $database->getConnection();

$from_date = date('Y-m-d');
$to_date = date('Y-m-d', strtotime('+1 day'));

// Instantiate the Product class
$product = new Product($db);

$expenses = new Expenses($db, $farm['user_id'], $from_date, $to_date);
$total_expenses = $expenses->getTotalAmount();
$incomes = new Incomes($db, $farm['user_id'], $product, $from_date, $to_date);
$total_incomes = $incomes->getTotalAmount();
$stocks = new Stocks($db, $farm['user_id']);

$total_stocks = $stocks->getTotalStockValue($from_date, $to_date);
$total_profit = $total_incomes + $total_stocks - $total_expenses;

$order = new Order($db);
$today_orders = $order->todayOrders(date('Y-m-d'), $farm['user_id']);

$product = new Product($db);
$products = $product->read($user_id);

$notification = new Notification($db);
$notification->setUser_id($user_id);
$notificationCount = $notification->getAllNotificationCount();
$notifications = $notification->getAllNotifications();

$incomeCharts = new Incomes($db, $farm['user_id'], $from_date, $to_date);
// Fetch daily and monthly income for charts
$daily_income = $incomeCharts->getDailyIncome();
$monthly_income = $incomeCharts->getMonthlyIncome();
?>
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }

    .card:hover {
        transform: scale(1.15);;
    }

    .card-title {
        font-weight: bold;
        font-size: 18px;
        text-transform: uppercase;
        margin-bottom: 15px;
    }

    .card-text {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .card-body {

        background: linear-gradient(145deg, rgba(255, 255, 255, 0.1), rgba(243, 243, 243, 0.8));
        border-radius: 10px 10px 0 0;
        padding: 20px;
    }

    .card-footer {
        background-color: #fff;
        border-top: 1px solid #e0e0e0;
    }

    .card-footer a {
        font-weight: bold;
        transition: color 0.3s ease-in-out;
    }

    .card-footer a:hover {
        color: #007bff;
    }

    .table {
        border-radius:0 0 10px 10px ;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .thead-dark {
        background-color: #343a40;
        color: #fff;
    }

    .table td, .table th {
        padding: 15px;
        vertical-align: middle;
    }

    #noti_number {
        font-size: 28px;
        position: relative;
        cursor: pointer;
        transition: color 0.3s ease-in-out;
    }

    #noti_number:hover {
        color: #ff4757;
    }

    #noti_number::after {
        content: "<?php echo $notificationCount; ?>";
        position: absolute;
        top: -10px;
        right: -10px;
        background: red;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }

    #notification_list {
        display: none;
        background: #ffffff;
        color: #333;
        position: absolute;
        right: 50px;
        top: 80px;
        width: 350px;
        border: 1px solid #ddd;
        box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        border-radius: 10px;
    }

    #notification_list ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    #notification_list li {
        padding: 10px 20px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 16px;
    }

    #notification_list li:last-child {
        border-bottom: none;
    }

    #notification_list li:hover {
        background: #f0f0f0;
    }

    .btn {
        padding: 10px 15px;
        font-size: 16px;
        border-radius: 25px;
        transition: all 0.3s ease-in-out;
    }

    .btn-danger {
        background-color: #e74c3c;
        border: none;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c0392b;
    }

    .btn-primary {
        background-color: #3498db;
        border: none;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2980b9;
    }
    /*    .overlay-container {
            position: relative;  Needed for positioning the overlay 
        }
    
        .overlay-container::before {
            content: "";  Necessary for pseudo-elements 
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);  Adjust the color and opacity as needed 
            z-index: 1;  Ensure the overlay is on top 
            pointer-events: none;  Allows clicks to go through the overlay 
        }*/

    .chart-container {
        transition: transform 0.3s ease; /* Smooth transition for scaling */
    }

    .chart-container:hover {
        transform: scale(1.10); /* Increase size by 5% on hover */
    }

    @media (min-width: 992px) {
        .chart-container:hover {
            flex: 0 0 calc(10/12 * 100%); /* Adjust flex width for lg size */
        }
    }

    .noti_li{
        transition: transform 0.3s ease; 
    }

    .noti_li:hover {
        transform: scale(1.05); 
    }


</style>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto overlay-container">
    <div class="container">
        <div class="row my-4 mx-4 text-center">
            <div style="text-align: right;">
                <i class="bi bi-bell-fill" aria-hidden="true" id="noti_number"></i>
            </div>
        </div>
        <div id="notification_list">
            <ul>
                <?php foreach ($notifications as $notification): ?>
                    <li class="noti_li"><?php echo $notification; ?> is low in stock!</li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="row my-4 mx-4 text-center">

            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white">TODAY ORDERS</h5>
                        <h6 class="card-text text-white"> <?php echo number_format($today_orders); ?></h6>
                    </div>
                    <div class="card-footer" style="background-color: #D4C8DE;">
                        <a href="#" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #989E12;">
                        <h5 class="card-title text-white">TODAY INCOME</h5>
                        <h6 class="card-text text-white">Rs. <?php echo number_format($total_incomes, 2); ?></h6>
                    </div>
                    <div class="card-footer" style="background-color: #F1F4B0;">
                        <a href="incomes.php?from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #B71717;">
                        <h5 class="card-title text-white">TODAY EXPENSES</h5>
                        <h6 class="card-text text-white">Rs. <?php echo number_format($total_expenses, 2); ?></h6>
                    </div>
                    <div class="card-footer" style="background-color: #F0A9A9;">
                        <a href="expenses.php?from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #1E8449;">
                        <h5 class="card-title text-white">TODAY PROFIT</h5>
                        <h6 class="card-text text-white">Rs. <?php echo number_format($total_profit, 2); ?></h6>
                    </div>
                    <div class="card-footer" style="background-color: #D4EAE2;">
                        <a href="profit.php?from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="row my-2 justify-content-center mt-5">
            <div class="col-lg-8 col-md-12 col-12">
                <div class="card">
                    <div class="card-body text-center p-3" style="background-color: #6c757d;">
                        <h5 class="card-title text-white mb-0"><strong style="font-size:25px;">Today's Price List</strong></h5>
                    </div>
                    <div class="card-footer p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="thead-dark text-center">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Today's Price</th>
                                        <th scope="col">Unit</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialnum = 0;
                                    foreach ($products as $product) {
                                        $serialnum++;
                                        ?>
                                        <tr>
                                            <th class="text-center"><?php echo $serialnum; ?></th>
                                            <td><?php echo $product['product_name']; ?></td>
                                            <td style="text-align:right">
                                                <form action="update_product_price.php" method="POST" style="display: inline;">
                                                    <span><?php echo "Rs. " . number_format($product['product_price'], 2); ?></span>
                                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                    <input type="text" name="new_price" class="form-control d-none" value="<?php echo $product['product_price']; ?>">
                                                </form>
                                            </td>
                                            <td class="text-center"><?php echo "1 " . $product['unit']; ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger text-light py-1 px-2" onclick="makeEditable(this)">Change Price</button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Charts Section -->
        <div class="row mt-5 mb-5 justify-content-center align-items-center">
            <div class="col-lg-8 px-4 mt-3 chart-container">
                <h5 class="fw-bold fs-3">Daily Income Chart</h5>
                <canvas id="dailyIncomeChart"></canvas>
            </div>
            <div class="col-lg-8 px-4 mt-5 chart-container">
                <h5 class="fw-bold fs-3">Monthly Income Chart</h5>
                <canvas id="monthlyIncomeChart"></canvas>
            </div>
        </div>


    </div>
</main>


<?php
$frame->last_part();
?>
<script>
    // Toggle notification dropdown visibility
    document.getElementById('noti_number').addEventListener('click', function (event) {
        event.stopPropagation(); // Prevent the click event from propagating to the document
        var notificationList = document.getElementById('notification_list');
        notificationList.style.display = (notificationList.style.display === 'none' || notificationList.style.display === '') ? 'block' : 'none';
    });

// Close notification list when clicking outside of it
    document.addEventListener('click', function (event) {
        var notificationList = document.getElementById('notification_list');
        var notiIcon = document.getElementById('noti_number');

        // Check if the click is outside the notification icon and the list
        if (notificationList.style.display === 'block' && !notiIcon.contains(event.target) && !notificationList.contains(event.target)) {
            notificationList.style.display = 'none';
        }
    });


    function makeEditable(button) {
        const td = button.closest('tr').querySelector('td:nth-child(3)');
        const form = td.querySelector('form');
        const span = form.querySelector('span');
        const input = form.querySelector('input[name="new_price"]');

        span.classList.toggle('d-none');
        input.classList.toggle('d-none');

        if (!input.classList.contains('d-none')) {
            input.focus();
        } else {
            form.submit();
        }
    }
</script>

<!-- Include Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Chart.js Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dailyIncomeCtx = document.getElementById('dailyIncomeChart').getContext('2d');
        const monthlyIncomeCtx = document.getElementById('monthlyIncomeChart').getContext('2d');

        // Daily Income Data
        const dailyLabels = <?php echo json_encode(array_column($daily_income, 'day')); ?>;
        const dailyData = <?php echo json_encode(array_column($daily_income, 'total')); ?>;

        // Monthly Income Data
        const monthlyLabels = <?php echo json_encode(array_column($monthly_income, 'month')); ?>;
        const monthlyData = <?php echo json_encode(array_column($monthly_income, 'total')); ?>;

        // Daily Income Chart
        const dailyIncomeChart = new Chart(dailyIncomeCtx, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                        label: 'Daily Income',
                        data: dailyData,
                        backgroundColor: 'rgba(153, 102, 255, 0.4)',
                        borderColor: 'blue',
                        fill: true
                    }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Monthly Income Chart
        const monthlyIncomeChart = new Chart(monthlyIncomeCtx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                        label: 'Monthly Income',
                        data: monthlyData,
                        backgroundColor: 'rgba(20, 90, 50, 0.9)',
                        borderColor: 'rgba(20, 90, 50, 1)',
                        borderWidth: 2
                    }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

