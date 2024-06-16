<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'Bird.php';
require_once 'checkLogin.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$database = new Database();
$db = $database->getConnection();

$bird = new Bird($db);

$next_batch_id = $bird->getNextBatchId($user_id);


$query = "SELECT sup_id, sup_name FROM supplier WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = htmlspecialchars(strip_tags($_POST['user_id']));
    $batch_id = htmlspecialchars(strip_tags($_POST['batch_id']));
    $sup_id = htmlspecialchars(strip_tags($_POST['sup_id']));
    $sup_name = htmlspecialchars(strip_tags($_POST['sup_name']));
    $bird_type = htmlspecialchars(strip_tags($_POST['bird_type']));
    $unit_price = htmlspecialchars(strip_tags($_POST['unit_price']));
    $quantity = htmlspecialchars(strip_tags($_POST['quantity']));
    $total_cost = htmlspecialchars(strip_tags($_POST['total_cost']));
    $date = htmlspecialchars(strip_tags($_POST['date']));

    $bird->setUserID($user_id);
    $bird->setBatchId($batch_id);
    $bird->setSupId($sup_id);
    $bird->setSupName($sup_name);
    $bird->setBirdType($bird_type);
    $bird->setUnitPrice($unit_price);
    $bird->setQuantity($quantity);
    $bird->setTotalCost($total_cost);
    $bird->setDate($date);

    if ($bird->create($user_id)) {
        header("Location: birds.php?user_id=" . htmlspecialchars($_SESSION['user_id']));
        exit();
    } else {
        $error_message = "Something went wrong. Please try again.";
        echo $error_message;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add New Batch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>ADD NEW BATCH</h3>
                    </div>
                    <div class="card-body"> 
                        <form id="batchForm" action="add_birds.php" method="post">
                            <div class="row">
                                <div class="col">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                                </div>
                            </div>   

                            <div class="row">
                                <div class="col">
                                    <label>Batch ID:</label>
                                    <input type="text" class="form-control" name="batch_id" value="<?php echo $next_batch_id; ?>" readonly>
                                </div>
                                <div class="col">
                                    <label>Date:</label>
                                    <input type="date" class="form-control" name="date" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <br>
                                    <label>Supplier ID:</label>
                                    <select name="sup_id" id="sup_id" class="form-control" required>
                                        <option value="">Select Supplier ID</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?php echo htmlspecialchars($supplier['sup_id']); ?>" data-sup-name="<?php echo htmlspecialchars($supplier['sup_name']); ?>">
                                                <?php echo htmlspecialchars($supplier['sup_id']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col"> 
                                    <label>Supplier Name:</label>
                                    <input type="text" name="sup_name" id="sup_name" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Type:</label>
                                <div class="form-check form-check-inline mx-5" name="type">
                                    <input class="form-check-input" type="radio" name="bird_type" value="chick" required>
                                    <label class="form-check-label">Chick</label>
                                </div>
                                <div class="form-check form-check-inline" name="type">
                                    <input class="form-check-input" type="radio" name="bird_type" value="hen" required>
                                    <label class="form-check-label">Hen</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <label>Unit Price:</label>
                                    <input type="text" class="form-control" name="unit_price" id="unitPrice" required>
                                </div>
                                <div class="col">
                                    <label>Number of Birds:</label>
                                    <input type="number" class="form-control" name="quantity" id="quantity" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <label>Total:</label>
                                    <input type="text" class="form-control" name="total_cost" id="totalCost" readonly required>
                                </div>
                            </div>

                            <br>
                            <input type="submit" class="btn btn-primary" name="submit" value="Add">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Get references to input fields
        const unitPriceInput = document.getElementById('unitPrice');
        const quantityInput = document.getElementById('quantity');
        const totalCostInput = document.getElementById('totalCost');
        const supIdSelect = document.getElementById('sup_id');
        const supNameInput = document.getElementById('sup_name');

        // Calculate total cost when unit price or quantity changes
        unitPriceInput.addEventListener('input', calculateTotalCost);
        quantityInput.addEventListener('input', calculateTotalCost);

        function calculateTotalCost() {
            // Get values of unit price and quantity
            const unitPrice = parseFloat(unitPriceInput.value);
            const quantity = parseFloat(quantityInput.value);

            // Calculate total cost
            const totalCost = unitPrice * quantity;

            // Update total cost input field
            totalCostInput.value = totalCost.toFixed(2); // Assuming you want to display the total with 2 decimal places
        }

        // Automatically fill supplier name based on supplier ID
        supIdSelect.addEventListener('change', function() {
            const selectedOption = supIdSelect.options[supIdSelect.selectedIndex];
            supNameInput.value = selectedOption.getAttribute('data-sup-name');
        });
    </script>
</body>
</html>
