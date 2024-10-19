<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/Bird.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$batch_id = $_GET['edit'];

$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$database = new Database();
$db = $database->getConnection();

$bird = new Bird($db);

// Retrieve suppliers from the database and populate the select options
$query = "SELECT sup_id, sup_name FROM supplier";
$stmt = $db->prepare($query);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$batchErr = $dateErr = $supErr = $ageErr = $typeErr = $priceErr = $numErr = "";
$errors = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $batch = htmlspecialchars(strip_tags($_POST['batch']));
    $sup_id = htmlspecialchars(strip_tags($_POST['sup_id']));
    $bird_type = htmlspecialchars(strip_tags($_POST['bird_type']));
    $age = htmlspecialchars(strip_tags($_POST['age'])); // Add this line
    $unit_price = htmlspecialchars(strip_tags($_POST['unit_price']));
    $quantity = htmlspecialchars(strip_tags($_POST['quantity']));
    $total_cost = htmlspecialchars(strip_tags($_POST['total_cost']));
    $date = htmlspecialchars(strip_tags($_POST['date']));

    $bird->setBatch($batch);
    $bird->setSupId($sup_id);
    $bird->setBirdType($bird_type);
    $bird->setAge($age); // Add this line
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

        if ($bird->update($batch_id)) {
            header("Location: birds.php");
            exit();
        } else {
            $error_message = "Something went wrong. Please try again.";
            echo $error_message;
        }
    }
} else {
    if (isset($_GET['edit'])) {
        $batch_id = $_GET['edit'];
        $bird_data = $bird->readOne($batch_id);
    } else {
        header("Location: birds.php");
        exit();
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
                        <form id="batchForm" action="update_birds.php?edit=<?php echo htmlspecialchars($batch_id); ?>" method="post">

                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label">Batch:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="batch" value="<?php echo htmlspecialchars($bird_data['batch']); ?>" required readonly>
                                            <small class="text-danger"><?php echo $batchErr ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label">Date:</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($bird_data['date']); ?>" required>
                                            <small class="text-danger"><?php echo $dateErr ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label">Supplier:</label>
                                        <div class="col-sm-9">
                                            <select name="sup_id" id="sup_id" class="form-control" required>
                                                <option value="" disabled>Select Supplier</option>
                                                <?php foreach ($suppliers as $supplier): ?>
                                                    <option value="<?php echo htmlspecialchars($supplier['sup_id']); ?>" <?php echo $bird_data['sup_id'] == $supplier['sup_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($supplier['sup_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="text-danger"><?php echo $supErr ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label">Age:</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" name="age" id="age" value="<?php echo htmlspecialchars($bird_data['age']); ?>" required>
                                            <small class="text-danger"><?php echo $ageErr ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <label class=" col-form-label">Type:</label>
                                    <div class="form-check form-check-inline mx-5">
                                        <input class="form-check-input" type="radio" name="bird_type" value="CHICK" <?php echo $bird_data['bird_type'] == 'CHICK' ? 'checked' : ''; ?> required>
                                        <label class="form-check-label">Chick</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="bird_type" value="HEN" <?php echo $bird_data['bird_type'] == 'HEN' ? 'checked' : ''; ?> required>
                                        <label class="form-check-label">Hen</label>
                                    </div>
                                    <small class="text-danger"><?php echo $typeErr ?></small>
                                </div>

                            </div>


                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Unit Price:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="unit_price" id="priceInput" value="<?php echo htmlspecialchars($bird_data['unit_price']); ?>" required onkeyup="validatePrice()">
                                            <small id="priceError" class="text-danger"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <label class="col-sm-5 col-form-label">Number of Birds:</label>
                                        <div class="col-sm-7">
                                            <input type="number" class="form-control" name="quantity" id="quantity" value="<?php echo htmlspecialchars($bird_data['quantity']); ?>" required>
                                            <small class="text-danger"><?php echo $numErr ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row mb-3 justify-content-center">
                                <label class="col-sm-2 col-form-label text-center">Total:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="total_cost" id="totalCost" value="<?php echo number_format((float) $bird_data['total_cost'], 2, '.', ''); ?>" readonly required>
                                </div>
                            </div>






                            <div class="row" style="text-align:center;">
                                <input type="submit" class="btn btn-primary" name="submit" value="Update">
                            </div>

                            <div class="row mt-2" style="text-align:center;">
                                <a href="birds.php" class="btn btn-danger">Back</a>
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



<?php
$frame->last_part();
?>