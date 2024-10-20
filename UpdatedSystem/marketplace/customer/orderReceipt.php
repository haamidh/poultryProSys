<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../classes/config.php';
require_once '../../classes/checkLogin.php';
require_once '../../classes/Product.php';
require_once '../../classes/Order.php';
require_once '../../classes/OrderDetails.php';
require_once dirname(__FILE__) . '/../../vendor/autoload.php';

// Ensure the user is logged in as a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../../login.php");
    exit();
}

// Retrieve the user id from the session
$user_id = $_SESSION["user_id"];

// Get the order number from the URL
$order_num = isset($_GET['order_num']) ? $_GET['order_num'] : '';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Orders and OrderDetails classes
$products = new Product($db);
$orders = new Order($db);
$orderDetails = new OrderDetails($db);

// Fetch the order data and billing details using the provided order number
$order = $orders->getOrder($order_num);
$billingDetails = $orderDetails->getBillingDetails($order_num);

if (!$order || !$billingDetails) {
    die("Invalid order number.");
}

// Get action (view or download)
$action = isset($_GET['action']) && $_GET['action'] === 'download' ? 'D' : 'I';

// Generate HTML content for PDF
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Receipt</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .container { width: 100%; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { width: 50px; height: 50px; border-radius: 50%; }
        .header h1 { margin: 0; font-size: 18px; }
        .details-container { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .details { flex: 1; } /* Allow details to take up most of the space */
        .stamp { flex: 0 0 150px; text-align: right; margin-top:-150px; margin-right:100px;} /* Fixed width for the stamp */
        .items table { width: 100%; border-collapse: collapse; }
        .items th, .items td { border: 1px solid #000; padding: 8px; text-align: left; }
        .items th { background-color: #f2f2f2; }
        .items td.quantity{ text-align: center; } /* Center align quantity and price */
        .items td.price { text-align: right; } /* Center align quantity and price */
        .footer { text-align: center; margin-top: 20px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Receipt Header -->
        <div class="header">
        
            <a class="navbar-brand mx-5">
                <img src="../../images/logo-poultryPro2.jpeg" alt="logo-poultryPro" style="border-radius: 50%; width: 40px; height: 30px;">
                <span style="font-size: 18px; font-weight: bold;">PoultryPro</span>
            </a>
            <p>123 Farm Road, Poultry City</p>
            <p>+94 77 123 4567 | info@poultrypro.com</p>
        </div>
        
        <!-- Billing Details and Paid Stamp -->
        <div class="details-container">
            <!-- Billing Details -->
            <div class="details">
                <p><strong>Customer Name:</strong> ' . htmlspecialchars($billingDetails['first_name']) . ' ' . htmlspecialchars($billingDetails['last_name']) . '</p>
                <p><strong>Address:</strong> ' . htmlspecialchars($billingDetails['address']) . ', ' . htmlspecialchars($billingDetails['city']) . '</p>
                <p><strong>Order Number:</strong> ' . htmlspecialchars($order_num) . '</p>
                <p><strong>Date:</strong> ' . date('Y-m-d') . '</p>
            </div>
            
            <!-- Paid Stamp -->
            <div class="stamp">
                <img src="../../images/paidStamp.png" alt="Paid Stamp" style="width: 150px; height: 150px;">
            </div>
        </div>

        <!-- Order Items -->
        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="quantity">Quantity</th>
                        <th class="price">Unit Price</th>
                        <th class="price">Total</th>
                    </tr>
                </thead>
                <tbody>';

$totalAmount = 0;
$uid = 1;
foreach ($order as $item) {
    $html .= '<tr>
        <td>' . $uid . '</td>
        <td>' . $products->getProductName(htmlspecialchars($item['product_id'])) . '</td>
        <td class="quantity">' . htmlspecialchars($item['quantity']) . '</td>
        <td class="price">' . number_format($item['unit_price'], 2) . '</td>
        <td class="price">' . number_format($item['total'], 2) . '</td>
    </tr>';
    $totalAmount += $item['total'];
    $uid++;
}

$html .= '
<tr>
    <td colspan="4" class="quantity"><strong>Total Amount</strong></td>
    <td class="price"><strong>' . number_format($totalAmount, 2) . '</strong></td>
</tr>';

$html .= '</tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your order!</p>
            <p>If you have any questions, feel free to contact us at +94 77 123 4567.</p>
        </div>
    </div>
</body>
</html>';




// Generate PDF
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output('order_receipt.pdf', $action);
