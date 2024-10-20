<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../classes/config.php';
require_once '../../classes/checkLogin.php';
require_once 'CustomerFrame.php';
require_once '../../classes/Order.php';
require_once '../../classes/User.php';  // Add the User class

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

$user = new User($db);  // Initialize the User class
$customerDetails = $user->userDetails($user_id);  // Fetch customer details

?>

<style>
    body {
        background-color: #f5f7fa;
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
        margin: 0 auto;
        /* Center the card */
    }

    .card:hover {
        transform: scale(1.05);
    }

    .card-title {
        font-weight: bold;
        font-size: 24px;
        margin-bottom: 15px;
    }

    .card-text {
        font-size: 18px;
        margin-bottom: 20px;
    }

    .card-body {
        padding: 20px;
    }

    .profile-image {
        border-radius: 50%;
        width: 300px;
        height: 300px;
        object-fit: cover;
        border: 4px solid #1abc9c;
        /* Border color to match the theme */
        margin: 20px auto;
        /* Centering */
    }

    .profile-image:hover {
        transform: scale(1.10);
    }

    .centered-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* Aligns vertically */
        height: 100vh;
        /* Full height */
    }

    .text-center {
        text-align: center;
        /* Center text horizontally */
    }

    .list-group-item {
        background-color: #f8f9fa;
        /* Light background color for list items */
        transition: background-color 0.3s ease;
    }

    .list-group-item:hover {
        background-color: #e9ecef;
        /* Slightly darker on hover */
    }
</style>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 overflow-auto">
    <div class="container centered-content">
        <div class="row my-4 text-center">
            <div class="col-lg-5 col-md-5 col-12 mb-3">
                <div>
                    <img src="../../images/profile.png" alt="Profile Image" class="profile-image"> <!-- Default image -->
                </div>
            </div>

            <!-- Display Customer Details -->
            <div class="col-lg-6 col-md-7 col-12 mb-3 mt-2 ">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <h5 class="card-title text-center text-success">Customer Details</h5>
                        <hr>
                        <?php if (!empty($customerDetails)) : ?>
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Name:</strong>
                                    <span><?php echo htmlspecialchars($customerDetails['username']); ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Email:</strong>
                                    <span><?php echo htmlspecialchars($customerDetails['email']); ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Phone:</strong>
                                    <span><?php echo htmlspecialchars($customerDetails['mobile']); ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Address:</strong>
                                    <span><?php echo htmlspecialchars($customerDetails['address']); ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>City:</strong>
                                    <span><?php echo htmlspecialchars($customerDetails['city']); ?></span>
                                </div>
                            </div>
                        <?php else : ?>
                            <p class="text-center">No customer details available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>



<!-- <?php
        //$frame->last_part();
        ?> -->