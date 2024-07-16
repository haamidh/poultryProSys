<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sup_name = $_POST['sup_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $sup_id = $_POST['sup_id'];

    addNewSupplier($con, $user_id, $sup_id, $sup_name, $address, $city, $mobile, $email);
}

function getLastSupplierId($con, $user_id)
{
    $query = $con->prepare("SELECT sup_id FROM supplier WHERE user_id=? ORDER BY sup_id DESC LIMIT 1");
    if (!$query) {
        die("Running Query failed: " . $con->errorInfo()[2]);
    }
    $query->bindParam(1, $user_id, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return 'S0001';
    } else {
        $lastId = $row['sup_id'];
        $numSuffix = intval(substr($lastId, 1));
        $updatedId = sprintf('%04d', $numSuffix + 1);
        return 'S' . $updatedId;
    }
}

function addNewSupplier($con, $user_id, $sup_id, $sup_name, $address, $city, $mobile, $email)
{
    $query = $con->prepare('INSERT INTO supplier(user_id, sup_id, sup_name, address, city, mobile, email) VALUES (:user_id, :sup_id, :sup_name, :address, :city, :mobile, :email)');
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':sup_id', $sup_id);
    $query->bindParam(':sup_name', $sup_name);
    $query->bindParam(':address', $address);
    $query->bindParam(':city', $city);
    $query->bindParam(':mobile', $mobile);
    $query->bindParam(':email', $email);

    return $query->execute();
}

function getAllSupplier($con, $user_id)
{
    $query = $con->prepare('SELECT * FROM supplier WHERE user_id=:user_id');
    $query->bindParam(':user_id', $user_id);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
?>


<div class="container contentArea" style="margin-left: -30px;margin-right:10px">
    <div class="row2">
        <div class="col4 mx-5 my-4" style="text-align: left; width:300px;">
            <div class="card-header card text-white" style="background-color: #40826D;">
                <h2 class="display-6 text-center" style="font-size: 30px; font-weight:500;">Supplier Details</h2>
            </div>
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                <input type="hidden" class="form-control" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>

                <div class="col-md-12">
                    <label for="sup_id" class="form-label">Supplier id:</label>
                    <input type="text" class="form-control" value="<?php echo getLastSupplierId($con, $user_id); ?>" id="sup_id" name="sup_id" readonly>
                </div>
                <div class="col-md-12">
                    <label for="sup_name" class="form-label">Supplier Name:</label>
                    <input type="text" class="form-control" id="sup_name" name="sup_name" required>
                </div>
                <div class="col-md-12">
                    <label for="address" class="form-label" style="text-align: left;">Address:</label>
                    <input type="text" class="form-control" id="address" placeholder="1234 Main St" name="address" required>
                </div>
                <div class="col-md-6">
                    <label for="city" class="form-label">City:</label>
                    <input type="text" class="form-control" id="city" name="city" required>
                </div>
                <div class="col-6">
                    <label for="mobile" class="form-label">Mobile:</label>
                    <input type="text" class="form-control" id="mobile" name="mobile" required>
                </div>
                <div class="col-md-12">
                    <label for="email" class="form-label">Email:</label>
                    <input type="text" class="form-control" id="email" name="email" required>
                </div>
                <div class="col-12" style="text-align: center;">
                    <button type="submit" class="btn btn-primary" name="add_supplier" value="Add supplier">Submit</button>
                </div>
            </form>
        </div>


        <div class="col5" style="margin-right: 10px;">
            <br>
            <table class="table table-striped">
                <thead class="table">
                    <tr>
                        <th scope="col" style="background-color: #40826D;">#</th>
                        <th scope="col" style="background-color: #40826D;">Supplier ID</th>
                        <th scope="col" style="background-color: #40826D;">Supplier Name</th>
                        <th scope="col" style="background-color: #40826D;">Mobile</th>
                        <th scope="col" style="background-color: #40826D;">Edit</th>
                        <th scope="col" style="background-color: #40826D;">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $serialnum = 0;
                    $suppliers = getAllSupplier($con, $user_id);
                    foreach ($suppliers as $supplier) {
                        $serialnum++;
                    ?>
                        <tr>
                            <th><?php echo $serialnum; ?></th>
                            <td><?php echo $supplier['sup_id']; ?></td>
                            <td><?php echo $supplier['sup_name']; ?></td>
                            <td><?php echo $supplier['mobile']; ?></td>
                            <td><a href="edit_supplier.php?id=<?php echo $supplier['sup_id']; ?>" class="btn btn-primary">Edit</a></td>
                            <td><a href="delete_supplier.php?id=<?php echo $supplier['sup_id']; ?>" class="btn btn-danger">Delete</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>


<?php
$frame->last_part();
?>