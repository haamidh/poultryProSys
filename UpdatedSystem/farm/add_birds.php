<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once '../classes/Bird.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Validation.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$database = new Database();
$db = $database->getConnection();

$bird = new Bird($db);
$next_batch = $bird->getNextBatch($user_id);

$query = "SELECT sup_id,sup_name FROM supplier WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$batchErr = $dateErr = $supErr = $ageErr = $typeErr = $priceErr = $numErr = "";
$errors = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = htmlspecialchars(strip_tags($_POST['user_id']));
    $batch = htmlspecialchars(strip_tags($_POST['batch']));
    $sup_id = htmlspecialchars(strip_tags($_POST['sup_id']));
    $age = htmlspecialchars(strip_tags($_POST['age']));
    $bird_type = htmlspecialchars(strip_tags($_POST['bird_type']));
    $unit_price = htmlspecialchars(strip_tags($_POST['unit_price']));
    $quantity = htmlspecialchars(strip_tags($_POST['quantity']));
    $total_cost = htmlspecialchars(strip_tags($_POST['total_cost']));
    $date = htmlspecialchars(strip_tags($_POST['date']));

    $bird->setUserID($user_id);
    $bird->setBatch($batch);
    $bird->setSupId($sup_id);
    $bird->setAge($age);
    $bird->setBirdType($bird_type);
    $bird->setUnitPrice($unit_price);
    $bird->setQuantity($quantity);
    $bird->setTotalCost($total_cost);
    $bird->setDate($date);

    if (!Validation::validateTextField($batch, $batchErr)) {
        $errors = true;
    }

    if (empty($sup_id)) {
        $supErr = "*Please select supplier";
        $errors = true;
    }

    if (!Validation::validateNumberField($age, $ageErr)) {
        $errors = true;
    }

    if (empty($bird_type)) {
        $birdErr = "*Please select bird type";
        $errors = true;
    }

    if (!Validation::validateAmount($unit_price, $priceErr)) {
        $errors = true;
    }

    if (!Validation::validateNumberField($quantity, $numErr)) {
        $errors = true;
    }

    if (!$errors) {
        if ($bird->create($user_id)) {
            header("Location: birds.php?message=New batch added successfully.");
            exit();
        } else {
            $error_message = "Something went wrong. Please try again.";
            echo $error_message;
        }
    }
}

$frame = new Frame();
$frame->first_part($farm);
?>
<style>
    .card-header {
        background-color: #3E497A;
        color: white;
    }

    .card {
        background-color: rgba(255, 255, 255, 0.3);
        border: none;
        border-radius: 10px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        margin-top: 50px;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 16px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
        transition: border 0.3s ease;
    }

    .form-control:focus {
        border-color: #3E497A;
        box-shadow: 0 0 5px rgba(62, 73, 122, 0.5);
    }

    .btn-primary {
        background-color: #3E497A;
        border-color: #3E497A;
    }

    .btn-primary:hover {
        background-color: #2E3A5A;
        border-color: #2E3A5A;
    }

    .btn-danger {
        background-color: #D9534F;
        border-color: #D9534F;
    }

    .btn-danger:hover {
        background-color: #C9302C;
        border-color: #C9302C;
    }

    .col-form-label {
        color: #3E497A;
        font-weight: bold;
    }

    .form-check-input:checked {
        background-color: #3E497A;
        border-color: #3E497A;
    }

    .form-check-label {
        color: #3E497A;
    }

    #totalCost {
        background-color: #E9ECEF;
        border-color: #3E497A;
    }
</style>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title p-2 text-white mb-0"><strong style="font-size:25px;">Add New Batch</strong></h5>
                    </div>
                    <div class="card-body">
                        <form id="batchForm" action="add_birds.php" method="post">
                            <div class="row">
                                <div class="col">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                                </div>
                            </div>

                            <div class="row px-2">
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label">Batch:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="batch" value="<?php echo $next_batch; ?>" readonly>
                                            <small class="text-danger"><?php echo $batchErr ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label">Date:</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="date" required>
                                            <small class="text-danger"><?php echo $dateErr ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row px-2">
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label">Supplier:</label>
                                        <div class="col-sm-9">
                                            <select name="sup_id" id="sup_id" class="form-control" required>
                                                <option value="" selected>Select Supplier</option>
                                                <?php foreach ($suppliers as $supplier): ?>
                                                    <option value="<?php echo htmlspecialchars($supplier['sup_id']); ?>">
                                                        <?php echo htmlspecialchars($supplier['sup_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>  
                                            <small class="text-danger"><?php echo $supErr ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row ">
                                        <label class="col-sm-3 col-form-label">Age:</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" name="age" id="age" required>
                                            <small class="text-danger"><?php echo $ageErr ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row px-2 mb-2">
                                <div class="form-group">
                                    <label class="col-form-label">Type:</label>
                                    <div class="form-check form-check-inline mx-5">
                                        <input class="form-check-input" type="radio" name="bird_type" value="CHICK" required>
                                        <label class="form-check-label">Chick</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="bird_type" value="HEN" required>
                                        <label class="form-check-label">Hen</label>
                                    </div>
                                    <small class="text-danger"><?php echo $typeErr ?></small>
                                </div>
                            </div>

                            <div class="row px-2">
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Unit Price:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="unit_price" id="priceInput" required onkeyup="validatePrice()">
                                            <small id="priceError" class="text-danger"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Quantity:</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" name="quantity" id="quantity" required onkeyup="calculateTotalCost()">
                                            <small class="text-danger"><?php echo $numErr ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row px-2">
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Total Cost:</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="total_cost" id="totalCost" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col text-center mt-3">
                                    <button type="submit" class="btn btn-primary">Add Batch</button>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function validatePrice() {
        const priceInput = document.getElementById('priceInput');
        const priceError = document.getElementById('priceError');
        const priceValue = priceInput.value;

        const regex = /^(0|[1-9]\d*)(\.\d{1,2})?$/;

        priceError.textContent = "";
        priceInput.classList.remove("is-invalid");

        if (priceValue.length > 0 && !regex.test(priceValue)) {
            priceError.textContent = "Price not valid";
            priceInput.classList.add("is-invalid");
        } 

        priceInput.value = priceValue.replace(/[^0-9.]/g, '');

        if (priceInput.value.length > 0 && priceInput.value[0] === '.') {
            priceError.textContent = "Price cannot start with a decimal point.";
            priceInput.classList.add("is-invalid");
        }
        calculateTotalCost();
    }

    function calculateTotalCost() {
        const priceInput = document.getElementById('priceInput').value;
        const quantityInput = document.getElementById('quantity').value;
        const totalCostInput = document.getElementById('totalCost');

        if (priceInput && quantityInput) {
            const totalCost = parseFloat(priceInput) * parseInt(quantityInput);
            totalCostInput.value = totalCost.toFixed(2); // Format to two decimal places
        } else {
            totalCostInput.value = ''; // Clear total cost if inputs are invalid
        }
    }
</script>
