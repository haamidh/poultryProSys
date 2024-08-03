<?php
if (session_status() == PHP_SESSION_NONE) session_start();

ob_start(); // Start output buffering

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';
require_once 'BuyMedicine.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please Login before Proceeding");
    ob_end_flush(); // Flush the buffer
    exit();
}

$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);
$buyMedicine = new BuyMedicine($con);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $med_id = $_POST['med_id'];
    $med_name = $_POST['med_name'];
    $sup_id = $_POST['sup_id'];
    $sup_name = $_POST['sup_name'];
    $unit_price = $_POST['unit_price'];
    $quantity = $_POST['quantity'];
    $total = $unit_price * $quantity;

    // Set BuyMedicine properties
    $buyMedicine->setUserId($user_id);
    $buyMedicine->setMedId($med_id);
    $buyMedicine->setMedName($med_name);
    $buyMedicine->setSupId($sup_id);
    $buyMedicine->setSupName($sup_name);
    $buyMedicine->setUnitPrice($unit_price);
    $buyMedicine->setQuantity($quantity);
    $buyMedicine->setTotal($total);
    $buyMedicine->setCreatedAt(date('Y-m-d H:i:s'));

    if ($buyMedicine->create($user_id)) {
        header('Location: buy_medicine.php?msg=Medicine Purchased Successfully');
        ob_end_flush(); // Flush the buffer
        exit();
    } else {
        echo "Record not added";
    }
}

// Fetch existing medicines
try {
    $query = "SELECT med_id, med_name FROM medicine WHERE user_id = :user_id";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch existing suppliers
try {
    $query = "SELECT sup_id, sup_name FROM supplier WHERE user_id = :user_id";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<script>
    function updateMedName() {
        var medSelect = document.getElementById('med_id');
        var medName = medSelect.options[medSelect.selectedIndex].getAttribute('data-med-name');
        document.getElementById('med_name').value = medName;
    }

    function updateSupName() {
        var supSelect = document.getElementById('sup_id');
        var supName = supSelect.options[supSelect.selectedIndex].getAttribute('data-sup-name');
        document.getElementById('sup_name').value = supName;
    }

    function calculateTotalCost() {
        const unitPriceInput = document.getElementById('unit_price');
        const quantityInput = document.getElementById('quantity');
        const totalCostInput = document.getElementById('total');

        const unitPrice = parseFloat(unitPriceInput.value);
        const quantity = parseFloat(quantityInput.value);

        const totalCost = unitPrice * quantity;

        totalCostInput.value = totalCost.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('unit_price').addEventListener('input', calculateTotalCost);
        document.getElementById('quantity').addEventListener('input', calculateTotalCost);
    });
</script>

<div class="container contentArea" style="margin-left: -30px;margin-right:10px">

    <div class="row2">
        <div class="col4 mx-5 my-4" style="text-align: left; width:500px;">

            <div class="card-header card text-white" style="background-color: #40826D;">
                <h2 class="display-6 text-center" style="font-size: 30px; font-weight:500;">Buy Medicine</h2>
            </div>
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                <input type="hidden" class="form-control" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>

                <div class="col-md-4">
                    <label for="med_id" class="form-label">Medicine ID:</label>
                    <select class="form-control" name="med_id" id="med_id" required onchange="updateMedName()">
                        <option value="">Select Medicine</option>
                        <?php foreach ($medicines as $medicine) : ?>
                            <option value="<?php echo $medicine['med_id']; ?>" data-med-name="<?php echo $medicine['med_name']; ?>"><?php echo $medicine['med_id']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-8">
                    <label for="med_name" class="form-label">Medicine Name:</label>
                    <input class="form-control" type="text" name="med_name" id="med_name" readonly>
                </div>

                <div class="col-md-4">
                    <label for="sup_id" class="form-label">Supplier ID:</label>
                    <select class="form-control" name="sup_id" id="sup_id" required onchange="updateSupName()">
                        <option value="">Select Supplier</option>
                        <?php foreach ($suppliers as $supplier) : ?>
                            <option value="<?php echo $supplier['sup_id']; ?>" data-sup-name="<?php echo $supplier['sup_name']; ?>"><?php echo $supplier['sup_id']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-8">
                    <label for="sup_name" class="form-label">Supplier Name:</label>
                    <input class="form-control" type="text" name="sup_name" id="sup_name" readonly>
                </div>

                <div class="col-md-4">
                    <label for="unit_price" class="form-label">Unit Price:</label>
                    <input class="form-control" type="number" step="0.01" name="unit_price" id="unit_price" required>
                </div>

                <div class="col-md-4">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input class="form-control" type="number" name="quantity" id="quantity" required>
                </div>
                <div class="col-md-4">
                    <label for="total" class="form-label">Total:</label>
                    <input class="form-control" type="text" name="total" id="total" readonly>
                </div>

                <div class="col-md-12" style="text-align: center;">
                    <button type="submit" class="btn btn-primary" name="buy_medicine">Buy Medicine</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row2">
        <div class="col5" style="margin-right: 10px;">
            <br>
            <table class="table table-striped">
                <thead class="table">
                    <tr>
                        <th scope="col" style="background-color: #40826D;">#</th>
                        <th scope="col" style="background-color: #40826D;">MedID</th>
                        <th scope="col" style="background-color: #40826D;">Medicine Name</th>
                        <th scope="col" style="background-color: #40826D;">Supplier ID</th>
                        <th scope="col" style="background-color: #40826D;">Supplier Name</th>
                        <th scope="col" style="background-color: #40826D;">Unit Price</th>
                        <th scope="col" style="background-color: #40826D;">Quantity</th>
                        <th scope="col" style="background-color: #40826D;">Total</th>
                        <th scope="col" colspan="2" style="background-color: #40826D;">Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $serialnum = 0;
                    $bought_medicines = $buyMedicine->read($user_id);
                    foreach ($bought_medicines as $medicine) {
                        $serialnum++;
                    ?>
                        <tr>
                            <th><?php echo $serialnum; ?></th>
                            <td><?php echo $medicine['med_id']; ?></td>
                            <td><?php echo $medicine['med_name']; ?></td>
                            <td><?php echo $medicine['sup_id']; ?></td>
                            <td><?php echo $medicine['sup_name']; ?></td>
                            <td><?php echo $medicine['unit_price']; ?></td>
                            <td><?php echo $medicine['quantity']; ?></td>
                            <td><?php echo $medicine['total']; ?></td>
                            <td><a href="edit_buy_medicine.php?buyMedicine_id=<?php echo $medicine['buyMedicine_id']; ?>" class="btn btn-primary">Edit</a></td>
                            <td><a href="delete_buy_medicine.php?buyMedicine_id=<?php echo $medicine['buyMedicine_id']; ?>" class="btn btn-danger">Delete</a></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$frame->last_part();
?>
