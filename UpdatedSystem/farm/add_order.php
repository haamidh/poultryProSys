<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once '../classes/Product.php';
require '../classes/Stocks.php';

require_once 'Frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Order.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

// Retrieve the id from the session
$user_id = $_SESSION["user_id"];

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);

$db = new Database();
$con = $db->getConnection();

$product = new Product($con);
$products = $product->read($user_id);
// $product_details = $product->readOne($product_id);

?>

<html>

<head>
    <title>Add Orders</title>
</head>

<body>
    <main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
        <div class="container">
            <div class="row my-5 text-center">

                <!-- Add New Order Section -->
                <div class="col-lg-5 col-md-10 col-12 mb-3 px-5">
                    <div class="card shadow">
                        <div class="card-header p-3 text-center" style="background-color: #356854;">
                            <h5 class="card-title text-white mb-0">
                                <strong style="font-size: 24px;">New Order</strong>
                            </h5>
                        </div>
                        <div class="card-body" style="background-color: #F5F5F5;">

                            <!-- Success and Error Messages -->
                            <?php if (isset($success_message)) : ?>
                                <div class="alert alert-success text-center">
                                    <?php echo $success_message; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($error_message)) : ?>
                                <div class="alert alert-danger text-center">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Add Order Form -->
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Select Product</label>
                                    <select name="product_id" id="product_id" class="form-control" required onchange="updateProductDetails()">
                                        <option value="" disabled selected>-- Select Product --</option>
                                        <?php foreach ($products as $singleProduct) {
                                            // Get the stock availability for each product
                                            $Stocks = new Stocks($con, $user_id);
                                            $itemStock = $Stocks->getProductAvailableQuantity($singleProduct['product_id']);
                                        ?>
                                            <option value="<?= $singleProduct['product_id'] ?>"
                                                data-image="../<?= $singleProduct['product_img'] ?>"
                                                data-price="<?= $singleProduct['product_price'] ?>"
                                                data-stock="<?= $itemStock ?>"
                                                data-unit="<?= $singleProduct['unit'] ?>">
                                                <?= $singleProduct['product_name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>

                                </div>
                                <div class="card-body text-center" style="background-color: white;">
                                    <img id="productImage" src="" alt="Product Image" style="max-width: 150px; max-height: 150px;" />
                                </div>
                                <script>
                                    function updateProductDetails() {
                                        // Get the selected option element
                                        var selectElement = document.getElementById("product_id");
                                        var selectedOption = selectElement.options[selectElement.selectedIndex];

                                        // Get the data attributes for image, price, and stock
                                        var productImage = selectedOption.getAttribute("data-image");
                                        var productPrice = parseFloat(selectedOption.getAttribute("data-price"));
                                        var availableStock = parseInt(selectedOption.getAttribute("data-stock"));
                                        var stockUnit = selectedOption.getAttribute("data-unit");

                                        // Update the image
                                        var imageElement = document.getElementById("productImage");
                                        imageElement.src = productImage ? productImage : ""; // Fallback to empty if no image

                                        // Update the price display
                                        var priceElement = document.getElementById("productPrice");
                                        priceElement.textContent = productPrice ? "Price: Rs. " + productPrice : "Price not available";

                                        // Set the product price in the hidden input
                                        document.getElementById("productPriceInput").value = productPrice;

                                        // Update the available stock
                                        var stockElement = document.getElementById("availableStock");
                                        stockElement.textContent = availableStock ? "Available Stock: " + availableStock : "Stock not available";

                                        // Update the unit
                                        var stockUnitElement = document.getElementById("stockUnit");
                                        stockUnitElement.textContent = stockUnit ? "" + stockUnit : "";

                                        // Set max value for the quantity input field based on available stock
                                        var quantityInput = document.getElementById("quantity");
                                        if (availableStock && availableStock > 0) {
                                            quantityInput.max = availableStock;
                                            quantityInput.value = Math.min(1, availableStock); // Default to 1 or lower if no stock
                                            quantityInput.disabled = false; // Enable input if stock is available
                                        } else {
                                            quantityInput.max = 0;
                                            quantityInput.value = 0; // No stock available
                                            quantityInput.disabled = true; // Disable input if no stock
                                        }

                                        // Add an event listener to the quantity input to calculate total amount
                                        quantityInput.addEventListener('input', function() {
                                            var quantity = parseInt(this.value);
                                            var quantity = parseFloat(this.value);
                                            var totalAmount = quantity * productPrice;
                                            document.getElementById("totalAmountInput").value = totalAmount.toFixed(2); // Set total amount in the hidden input
                                        });
                                    }
                                </script>

                                <div class="mb-3">
                                    <p id="productPrice" style="font-weight: bold;"></p>
                                    <input type="hidden" id="productPriceInput" name="productPriceInput" value="">


                                </div>

                                <div class="mb-3">

                                    <p id="availableStock" style="font-weight: bold;">
                                    <p id="stockUnit" style="font-weight: bold;"></p>
                                    </p>

                                </div>


                                <div class="mb-3 row">
                                    <div class="col-md-6">
                                        <label class="form-label">Quantity:</label>
                                        <input type="number" id="quantity" class="form-control" min="1" max="" placeholder="Select quantity">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Total:</strong></label>
                                        <input type="number" id="totalAmountInput" class="form-control" name="totalAmountInput" value="" readonly>
                                    </div>
                                </div>

                                

                                <div class="mb-3">
                                    <label class="form-label">Description:</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-block" name="add_med">Add Medicine</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <!-- Medicine Details Section -->
                <div class="col-lg-7 col-md-10 col-12 mb-3">
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                            <h5 class="card-title text-white mb-0">
                                <strong style="font-size:25px;">Medicine Details</strong>
                            </h5>
                            <div class="input-group" style="width: 250px;">
                                <input type="text" id="searchMedInput" class="form-control" placeholder="Search medicine..." onkeyup="searchMedicine()">
                                <span class="input-group-text">
                                    <i class="bi bi-search" style="color: #3E497A;"></i>
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 table-striped table-bordered text-center">
                                    <thead style="background-color: #3E497A; color: white;">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Med Name</th>
                                            <th scope="col">Description</th>
                                            <th scope="col" style="width:32%">Option</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $serialnum = 0;
                                        foreach ($medicines as $medicine) {
                                            $serialnum++;
                                        ?>
                                            <tr>
                                                <th><?php echo $serialnum; ?></th>
                                                <td><?php echo $medicine['med_name']; ?></td>
                                                <td><?php echo $medicine['description']; ?></td>
                                                <td>
                                                    <a href="buy_medicine.php?med_id=<?php echo $medicine['med_id']; ?>" class="btn btn-primary text-dark py-1 px-2"><i class="bi bi-plus-square-fill" style="font-size:18px;"></i></a>
                                                    <a href="use_medicine.php?med_id=<?php echo $medicine['med_id']; ?>" class="btn btn-warning text-dark py-1 px-2"><i class="bi bi-dash-square-fill" style="font-size:18px;"></i></a>
                                                    <a href="edit_medicine.php?med_id=<?php echo $medicine['med_id']; ?>" class="btn btn-success text-light py-1 px-2">Edit</a>
                                                    <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $medicine['med_id']; ?>)">Delete</button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Confirmation for Deletion -->
    <script>
        function myFunction(med_id) {
            if (confirm("Are you sure you want to delete this medicine?")) {
                window.location.href = "delete_medicine.php?med_id=" + med_id;
            }
        }

        function searchMedicine() {
            var input = document.getElementById("searchMedInput");
            var filter = input.value.toUpperCase();
            var table = document.querySelector(".table");
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) {
                var medName = rows[i].getElementsByTagName("td")[0];
                var description = rows[i].getElementsByTagName("td")[1];

                if (medName || description) {
                    var nameValue = medName.textContent || medName.innerText;
                    var descriptionValue = description.textContent || description.innerText;

                    if (
                        nameValue.toUpperCase().indexOf(filter) > -1 ||
                        descriptionValue.toUpperCase().indexOf(filter) > -1
                    ) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>

</html>