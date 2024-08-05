<?php
ob_start(); // Start output buffering

// Session and error handling
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';

// Check whether user is logged in with the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farm') {
    header("Location: login.php?msg=Please Login before Proceeding");
    exit();
}

// Get user ID and supplier ID
$user_id = $_SESSION['user_id'];

// Ensure 'sup_id' is set in the GET request
if (!isset($_GET['sup_id'])) {
    echo "Supplier ID is missing.";
    exit();
}

$sup_id = $_GET['sup_id'];

// Database connection
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

// Function to update supplier
function updateSupplier($con, $user_id, $sup_id, $sup_name, $sup_address, $sup_city, $sup_mobile, $sup_email)
{
    $query = $con->prepare('UPDATE supplier SET sup_name = :sup_name, address = :sup_address, city = :sup_city, mobile = :sup_mobile, email = :sup_email WHERE user_id = :user_id AND sup_id = :sup_id');
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':sup_id', $sup_id);
    $query->bindParam(':sup_name', $sup_name);
    $query->bindParam(':sup_address', $sup_address);
    $query->bindParam(':sup_city', $sup_city);
    $query->bindParam(':sup_mobile', $sup_mobile);
    $query->bindParam(':sup_email', $sup_email);

    return $query->execute();
}

// Function to get supplier details
function getSupplierDetails($con, $user_id, $sup_id)
{
    $query = $con->prepare('SELECT sup_name, address, city, mobile, email FROM supplier WHERE user_id = :user_id AND sup_id = :sup_id');
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':sup_id', $sup_id);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sup_id = $_POST['sup_id'];
    $sup_name = $_POST['sup_name'];
    $sup_address = $_POST['address'];
    $sup_city = $_POST['city'];
    $sup_mobile = $_POST['mobile'];
    $sup_email = $_POST['email'];

    if (updateSupplier($con, $user_id, $sup_id, $sup_name, $sup_address, $sup_city, $sup_mobile, $sup_email)) {
        header("Location: supplier.php?msg=Supplier Updated Successfully");
        exit();
    } else {
        echo "<p>Failed to update supplier.</p>";
    }
}

// Get supplier details for the form
$supplier = getSupplierDetails($con, $user_id, $sup_id);
$sup_name = $supplier['sup_name'];
$sup_address = $supplier['address'];
$sup_city = $supplier['city'];
$sup_mobile = $supplier['mobile'];
$sup_email = $supplier['email'];

?>
<div class="container contentArea" style="margin-left: -30px; margin-right: 10px">
    <div class="row2">
        <div class="col4 mx-5 my-4" style="text-align: left; width: 500px;">
            <div class="card-header card text-white" style="background-color: #40826D;">
                <h2 class="display-6 text-center" style="font-size: 30px; font-weight: 500;">Supplier Details</h2>
            </div>
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <input type="hidden" class="form-control" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>
                <div class="col-md-12">
                    <label for="sup_id" class="form-label">Supplier ID:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($sup_id); ?>" id="sup_id" name="sup_id" readonly>
                </div>
                <div class="col-md-12">
                    <label for="sup_name" class="form-label">Supplier Name:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($sup_name); ?>" id="sup_name" name="sup_name" required>
                </div>
                <div class="col-md-12">
                    <label for="address" class="form-label">Address:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($sup_address); ?>" id="address" name="address" required>
                </div>
                <div class="col-md-6">
                    <label for="city" class="form-label">City:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($sup_city); ?>" id="city" name="city" required>
                </div>
                <div class="col-6">
                    <label for="mobile" class="form-label">Mobile:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($sup_mobile); ?>" id="mobile" name="mobile" required>
                </div>
                <div class="col-md-12">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($sup_email); ?>" id="email" name="email" required>
                </div>
                <div class="col-12" style="text-align: center;">
                    <button type="submit" class="btn btn-primary" name="update_supplier">Update Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$frame->last_part();
ob_end_flush(); // End output buffering
?>