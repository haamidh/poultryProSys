<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Product.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

// Initialize the Product object
$product = new Product($con);

$success_message = '';
$error_message = '';

$nameErr = $unitErr = $categoryErr = $priceErr  = $leastErr = "";
$errors = false;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $unit = $_POST['unit'];
    $category_id = $_POST['category_id'];
    $selling_price = number_format($_POST['selling_price'], 2);
    $least_quantity = number_format($_POST['least_quantity'], 2);
    $description = $_POST['description'];


    if (!Validation::validateTextField($product_name, $nameErr)) {
        $errors = true;
    }

    if (empty($unit)) {
        $unitErr = "*Please select unit";
        $errors = true;
    }

    if (!Validation::validateNumberField($category_id, $categoryErr)) {
        $errors = true;
    }

    if (!Validation::validateAmount($selling_price, $priceErr)) {
        $errors = true;
    }

    if (!Validation::validateDecimalField($least_quantity, $leastErr)) {
        $errors = true;
    }

    if (!Validation::validateNumberField($quantity, $numErr)) {
        $errors = true;
    }


    // Initialize variables for file upload
    $target_file = 'default_image.jpg';
    $file_uploaded = false;

    // Handle file upload
    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../images/ProductImages/";
        $target_file = $target_dir . basename($_FILES["product_img"]["name"]);

        // Check if directory exists, if not, create it
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["product_img"]["tmp_name"], $target_file)) {
            $file_uploaded = true;
        } else {
            $error_message = "Failed to move uploaded file.";
        }
    } else {
        if ($_FILES['product_img']['error'] != UPLOAD_ERR_NO_FILE) {
            $error_message = "Upload error code: " . $_FILES['product_img']['error'];
        }
    }



    $product->setUser_id($user_id);
    $product->setProduct_name($product_name);
    $product->setUnit($unit);
    $product->setCategory_id($category_id);
    $product->setProduct_price($selling_price);
    $product->setProduct_img("images/ProductImages/" . basename($_FILES["product_img"]["name"]));
    $product->setLeastQuantity($least_quantity);
    $product->setDescription($description);


    if (!$errors) {

        if ($product->productExists($user_id)) {
            $error_message = "This product already exists";
        } else {

            // Create a new product
            if ($product->create($user_id)) {
                $success_message = "Added successfully.";
            } else {
                $error_message = "Failed to add.";
            }
        }
    }
}

// Fetch product categories for dropdown
function fetchCategories($con)
{
    $query = $con->prepare('SELECT * FROM product_categories');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

$categories = fetchCategories($con);

$products = $product->read($user_id);
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 text-center">

            <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <div class="card shadow">
                    <div class="card-header p-3 text-center" style="background-color: #356854;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Add New Product</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #D4C8DE;">

                        <?php if (!empty($success_message)) : ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error_message)) : ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">

                            <div class="row p-2">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <label class="col-sm-8 col-form-label">Product Name:</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <input type="text" name="product_name" id="product_name" class="form-control" required onkeyup="validateProductName()">
                                            <small class="text-danger" id="nameErr"><?php echo $nameErr ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <label class="col-sm-8 col-form-label">Select Category:</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <select name="category_id" id="category_id" class="form-control" required onchange="validateCategory()">
                                                <option value="" disabled selected>Select Category</option>
                                                <?php foreach ($categories as $category) { ?>
                                                    <option value="<?= $category['category_id'] ?>">
                                                        <?= $category['category_name'] ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <small class="text-danger" id="categoryErr"><?php echo $categoryErr ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <label class="col-sm-8 col-form-label">Selling Price:</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <input type="text" name="selling_price" id="priceInput" class="form-control" required onkeyup="validatePrice()">
                                            <small class="text-danger" id="priceError"><?php echo $priceErr ?></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Unit:</label>
                                    </div>

                                    <div class="col-sm-8">
                                        <div class="row mb-3 px-3">
                                            <div class="col-sm-6 form-check">
                                                <input type="radio" class="form-check-input" name="unit" value="kilogram" id="kilogram" required>
                                                <label class="form-check-label" for="kilogram">kg</label>
                                            </div>
                                            <div class="col-sm-6 form-check">
                                                <input type="radio" class="form-check-input" name="unit" value="piece" id="pieces" required>
<<<<<<< Updated upstream
                                                <label class="form-check-label" for="Pieces">Pieces</label>
=======
                                                <label class="form-check-label" for="pieces">Pieces</label>
>>>>>>> Stashed changes
                                            </div>
                                            <small class="text-danger"><?php echo $unitErr ?></small>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Product Image:</label>
                                    </div>
                                    <div class="col-sm-12">
                                        <input type="file" name="product_img" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Notification Threshold:</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="least_quantity" name="least_quantity" required onkeyup="validateLeastQuantity()">
                                            <small class="text-danger" id="quantityErr"><?php echo $leastErr ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Description:</label>
                                        <div class="col-sm-12">
                                            <textarea name="description" class="form-control" rows="3" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row px-3" style="text-align:center;">
                                <button type="submit" class="btn btn-primary" name="add_product">Add Product</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-10 col-12 mb-3 my-2">

                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title p-2 text-white mb-0"><strong style="font-size:25px;">Product Details</strong></h5>
                        <div class="input-group" style="width: 250px;">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search for products..." onkeyup="searchProduct()">
                            <span class="input-group-text">
                                <i class="bi bi-search" style="color: #3E497A;"></i>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-striped table-bordered">
                                <thead style="background-color: #3E497A; color: white;">
                                    <tr style="text-align:center;">
                                        <th scope="col">#</th>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialnum = 0;
                                    foreach ($products as $product) {
                                        $serialnum++;
                                    ?>
                                        <tr>
                                            <th><?php echo $serialnum; ?></th>
                                            <td><?php echo $product['product_name']; ?></td>
                                            <td style="text-align:right;">Rs. <?php echo $product['product_price']; ?></td>
                                            <td><?php echo $product['unit']; ?></td>
                                            <td style="text-align:center;">
                                                <a href="product_stock.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-primary text-dark py-1 px-2"><i class="bi bi-plus-square-fill" style="font-size:18px;"></i></a>
                                                <a href="edit_product.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-success text-light py-1 px-2">Edit</a>
                                                <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $product['product_id']; ?>)">Delete</button>
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

<?php $frame->last_part(); ?>
<script>
    function myFunction(product_id) {
        if (confirm("Are you sure you want to delete this product?")) {
            window.location.href = "delete_product.php?product_id=" + product_id;
        }
    }

    function searchProduct() {
        // Get the input value
        var input = document.getElementById("searchInput");
        var filter = input.value.toUpperCase();

        // Get the table and rows
        var table = document.querySelector(".table");
        var rows = table.getElementsByTagName("tr");

        // Loop through all table rows (except the first one, which is the header)
        for (var i = 1; i < rows.length; i++) {
            var productName = rows[i].getElementsByTagName("td")[0];
            var productPrice = rows[i].getElementsByTagName("td")[1];
            var productUnit = rows[i].getElementsByTagName("td")[2];

            // Check if the current row matches the search query
            if (productName || productPrice || productUnit) {
                var nameValue = productName.textContent || productName.innerText;
                var priceValue = productPrice.textContent || productPrice.innerText;
                var unitValue = productUnit.textContent || productUnit.innerText;

                // If the input matches any of these values, show the row
                if (
                    nameValue.toUpperCase().indexOf(filter) > -1 ||
                    priceValue.toUpperCase().indexOf(filter) > -1 ||
                    unitValue.toUpperCase().indexOf(filter) > -1
                ) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }

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
            priceInput.value = "";
        }
    }

    function validateProductName() {
        const productNameInput = document.getElementById('product_name');
        const nameErr = document.getElementById('nameErr');
        const productNameValue = productNameInput.value;

        const regex = /^[a-zA-Z\s]{3,}$/; // Minimum 3 alphabetic characters, allows spaces

        nameErr.textContent = "";
        productNameInput.classList.remove("is-invalid");

        if (productNameValue.length > 0 && !regex.test(productNameValue)) {
            nameErr.textContent = "Product name not valid (only letters and spaces, at least 3 characters).";
            productNameInput.classList.add("is-invalid");
            productNameInput.value = productNameValue.replace(/[^a-zA-Z\s]/g, '');
        }
    }

    function validateCategory() {
        const categoryInput = document.getElementById('category_id');
        const categoryErr = document.getElementById('categoryErr');
        const categoryValue = categoryInput.value;

        const regex = /^[1-9]\d*$/; // Only positive numbers

        categoryErr.textContent = "";
        categoryInput.classList.remove("is-invalid");

        if (categoryValue.length > 0 && !regex.test(categoryValue)) {
            categoryErr.textContent = "Category ID not valid (must be a positive number).";
            categoryInput.classList.add("is-invalid");
            categoryInput.value = categoryValue.replace(/[^0-9]/g, '');
        }
    }

    function validateLeastQuantity() {
        const quantityInput = document.getElementById('least_quantity');
        const quantityErr = document.getElementById('quantityErr');
        const quantityValue = quantityInput.value;

        const regex = /^(0|[1-9]\d*)(\.\d{1,2})?$/; // Allows positive numbers with up to two decimal places

        quantityErr.textContent = "";
        quantityInput.classList.remove("is-invalid");

        if (quantityValue.length > 0 && !regex.test(quantityValue)) {
            quantityErr.textContent = "Quantity not valid (must be a positive number with up to two decimal places).";
            quantityInput.classList.add("is-invalid");
            quantityInput.value = quantityValue.replace(/[^0-9.]/g, ''); // Replace invalid characters
        }

        // Additional check to prevent starting with a decimal
        if (quantityInput.value.length > 0 && quantityInput.value[0] === '.') {
            quantityErr.textContent = "Quantity cannot start with a decimal point.";
            quantityInput.classList.add("is-invalid");
            quantityInput.value = "";
        }
    }
</script>