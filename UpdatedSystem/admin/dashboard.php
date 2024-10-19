<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';
require_once '../classes/User.php';
require_once '../classes/Order.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];

$admin = CheckLogin::checkLoginAndRole($user_id, 'admin');

$from_date = date('Y-m-d');
$to_date = date('Y-m-d', strtotime('+1 day'));

$users = new User($db);
$farms = count($users->getAllFarms());
$customers = count($users->getAllCustomers());

$adminframe = new AdminFrame();
$adminframe->first_part($admin);

$orders = new Order($db);
$order_payments = $orders->getAllServiceFees($from_date, $to_date);
$today_income = 0;

// Loop through the array and sum the service fees
foreach ($order_payments as $payment) {
    $today_income += $payment['service_fee'];
}

$daily_income = $orders->getDailyServiceFees($to_date);
$monthly_income = $orders->getMonthlyServiceFees($to_date);
?>
<style>
    body {
        background-color: #f4f6f9;
    }

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
    .chart-container {
        margin-top: 50px;
        margin-bottom: 50px;
        padding: 20px;
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .chart-container:hover {
        transform: scale(1.05); /* Increase size by 5% on hover */
    }


</style>
<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto overlay-container">
    <div class="container">
        <div class="row my-5 mx-4 text-center align-items-center justify-content-center">

            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white">TOTAL CUSTOMERS</h5>
                        <h6 class="card-text text-white"><?php echo number_format($customers); ?> </h6>
                    </div>
                    <div class="card-footer" style="background-color: #D4C8DE;">
                        <a href="customers.php" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #989E12;">
                        <h5 class="card-title text-white">TOTAL FARMS</h5>
                        <h6 class="card-text text-white"><?php echo number_format($farms); ?></h6>
                    </div>
                    <div class="card-footer" style="background-color: #F1F4B0;">
                        <a href="farms.php" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #B71717;">
                        <h5 class="card-title text-white">TODAY INCOMES</h5>
                        <h6 class="card-text text-white">Rs. <?php echo number_format($today_income, 2); ?></h6>
                    </div>
                    <div class="card-footer" style="background-color: #F0A9A9;">
                        <a href="incomes.php?from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>" class="text-dark">More Details</a>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Chart.js Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dailyIncomeCtx = document.getElementById('dailyIncomeChart').getContext('2d');
        const monthlyIncomeCtx = document.getElementById('monthlyIncomeChart').getContext('2d');

        // Daily Income Data
        const dailyLabels = <?php echo json_encode(array_column($daily_income, 'day')); ?>;
        const dailyData = <?php echo json_encode(array_column($daily_income, 'total_service_fees')); ?>;

        // Monthly Income Data
        const monthlyLabels = <?php echo json_encode(array_column($monthly_income, 'month')); ?>;
        const monthlyData = <?php echo json_encode(array_column($monthly_income, 'total_service_fees')); ?>;

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


<?php
$adminframe->last_part();
?>