<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../classes/config.php';
require_once '../../classes/checkLogin.php';
require_once 'CustomerFrame.php';
require_once '../../classes/Order.php';


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

?>

<style>
    body {
        background-color: #f5f7fa;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }

    .card:hover {
        transform: scale(1.15);
    }

    .card-title {
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 15px;
    }

    .card-text {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .card-body {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.1), rgba(243, 243, 243, 0.8));
        padding: 20px;
    }

    .card-footer {
        background-color: #fff;
        border-top: 1px solid #e0e0e0;
    }

    .table {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .thead-dark {
        background-color: #34495e;
        color: #fff;
    }

    .btn {
        padding: 10px 15px;
        font-size: 16px;
        border-radius: 25px;
        transition: all 0.3s ease-in-out;
    }

    .btn-primary {
        background-color: #1abc9c;
        border: none;
        color: white;
    }

    .btn-primary:hover {
        background-color: #16a085;
    }

    
</style>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto overlay-container">
    <div class="container">
        <div class="row my-4 mx-4 text-center">
            <div class="col-lg-6 col-md-6 col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4" style="background-color: #1abc9c;">
                        <h5 class="card-title text-white">Total Orders</h5>
                        <h6 class="card-text text-white"><?php echo count($orders); ?></h6>
                    </div>
                    <div class="card-footer" style="background-color: #d1f2eb;">
                        <a href="orders.php" class="text-dark">More Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- <?php
        //$frame->last_part();
        ?> -->
<script>
    // Toggle notification dropdown visibility
    document.getElementById('noti_number').addEventListener('click', function(event) {
        event.stopPropagation();
        var notificationList = document.getElementById('notification_list');
        notificationList.style.display = (notificationList.style.display === 'none' || notificationList.style.display === '') ? 'block' : 'none';
    });

    document.addEventListener('click', function(event) {
        var notificationList = document.getElementById('notification_list');
        var notiIcon = document.getElementById('noti_number');
        if (notificationList.style.display === 'block' && !notiIcon.contains(event.target) && !notificationList.contains(event.target)) {
            notificationList.style.display = 'none';
        }
    });
</script>