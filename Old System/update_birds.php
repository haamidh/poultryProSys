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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $batch_id = htmlspecialchars(strip_tags($_POST['batch_id']));
    $sup_id = htmlspecialchars(strip_tags($_POST['sup_id']));
    $sup_name = htmlspecialchars(strip_tags($_POST['sup_name']));
    $bird_type = htmlspecialchars(strip_tags($_POST['bird_type']));
    $unit_price = htmlspecialchars(strip_tags($_POST['unit_price']));
    $quantity = htmlspecialchars(strip_tags($_POST['quantity']));
    $total_cost = htmlspecialchars(strip_tags($_POST['total_cost']));
    $date = htmlspecialchars(strip_tags($_POST['date']));

    $bird->setBatchId($batch_id);
    $bird->setUserID($user_id);
    $bird->setSupId($sup_id);
    $bird->setSupName($sup_name);
    $bird->setBirdType($bird_type);
    $bird->setUnitPrice($unit_price);
    $bird->setQuantity($quantity);
    $bird->setTotalCost($total_cost);
    $bird->setDate($date);

    if ($bird->update($batch_id, $user_id)) {
        header("Location: birds.php?user_id=" . htmlspecialchars($_SESSION['user_id']));
        exit();
    } else {
        $error_message = "Something went wrong. Please try again.";
        echo $error_message;
    }
} else {
    if (isset($_GET['edit'])) {
        $batch_id = $_GET['edit'];
        $bird->setBatchId($batch_id);
        $bird_data = $bird->readOne();
    } else {
        header("Location: birds.php?user_id=" . htmlspecialchars($_SESSION['user_id']));
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Update Batch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>UPDATE BATCH</h3>
                    </div>
                    <div class="card-body">
                        <form id="batchForm" action="update_birds.php?edit=<?php echo htmlspecialchars($batch_id); ?>" method="post">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                            <div class="row">
                                <div class="col">
                                    <label>Batch ID:</label>
                                    <input type="text" class="form-control" name="batch_id" value="<?php echo htmlspecialchars($bird_data['batch_id']); ?>" required readonly>
                                </div>
                                <div class="col">
                                    <label>Date:</label>
                                    <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($bird_data['date']); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <br>
                                    <label>Supplier ID:</label>
                                    <select name="sup_id" required>
                                        <option value="S001" <?php echo $bird_data['sup_id'] == 'S001' ? 'selected' : ''; ?>>S 001</option>
                                        <option value="S002" <?php echo $bird_data['sup_id'] == 'S002' ? 'selected' : ''; ?>>S 002</option>
                                        <option value="S003" <?php echo $bird_data['sup_id'] == 'S003' ? 'selected' : ''; ?>>S 003</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label>Supplier Name:</label>
                                    <input type="text" name="sup_name" class="form-control" value="<?php echo htmlspecialchars($bird_data['sup_name']); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Type:</label>
                                <div class="form-check form-check-inline mx-5" name="type">
                                    <input class="form-check-input" type="radio" name="bird_type" value="chick" <?php echo $bird_data['bird_type'] == 'chick' ? 'checked' : ''; ?> required>
                                    <label class="form-check-label">Chick</label>
                                </div>
                                <div class="form-check form-check-inline" name="type">
                                    <input class="form-check-input" type="radio" name="bird_type" value="hen" <?php echo $bird_data['bird_type'] == 'hen' ? 'checked' : ''; ?> required>
                                    <label class="form-check-label">Hen</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label>Unit Price:</label>
                                    <input type="text" class="form-control" name="unit_price" id="unitPrice" value="<?php echo htmlspecialchars($bird_data['unit_price']); ?>" required>
                                </div>
                                <div class="col">
                                    <label>Number of Birds:</label>
                                    <input type="number" class="form-control" name="quantity" id="quantity" value="<?php echo htmlspecialchars($bird_data['quantity']); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label>Total:</label>
                                    <input type="text" class="form-control" name="total_cost" id="totalCost" value="<?php echo htmlspecialchars($bird_data['total_cost']); ?>" readonly required>
                                </div>
                            </div>
                            <br>
                            <input type="submit" class="btn btn-primary" name="submit" value="Update">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const unitPriceInput = document.getElementById('unitPrice');
        const quantityInput = document.getElementById('quantity');
        const totalCostInput = document.getElementById('totalCost');

        unitPriceInput.addEventListener('input', calculateTotalCost);
        quantityInput.addEventListener('input', calculateTotalCost);

        function calculateTotalCost() {
            const unitPrice = parseFloat(unitPriceInput.value);
            const quantity = parseFloat(quantityInput.value);
            const totalCost = unitPrice * quantity;
            totalCostInput.value = totalCost.toFixed(2);
        }
    </script>
</body>

</html>