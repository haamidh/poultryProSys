<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please Login before Proceeding");
    exit();
}

$farm_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($farm_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = getLastProductId($con, $farm_id);
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit']; 
    $category_id = $_POST['category_id'];
    $product_price = $_POST['selling_price'];
    $product_img = $_FILES['product_img'];
    $description = $_POST['product_description'];

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($product_img["name"]);
    move_uploaded_file($product_img["tmp_name"], $target_file);

    if ($con == false) {
        die("Error Establishing Connection: " . $con->errorInfo());
    }

    if (addNewProduct($con, $product_id, $farm_id, $product_name, $quantity, $unit, $category_id, $product_price, $target_file, $description)) {
        header('Location: products.php?msg=Data Updated Successfully&user_id=' . $farm_id);
        ob_end_flush(); // Flush the buffer
        exit();
    } else {
        echo "Record not added";
    }
}

function getLastProductId($con, $farm_id){
    $query = $con->prepare("SELECT product_id FROM products WHERE farm_id=? ORDER BY product_id DESC LIMIT 1");

    if (!$query) {
        die("Running Query failed: " . $con->errorInfo()[2]);
    }

    $query->bindParam(1, $farm_id, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return 'P0001';
    } else {
        $lastId = $row['product_id'];
        $numSuffix = intval(substr($lastId, 1));
        $updatedId = sprintf('%04d', $numSuffix + 1);
        return 'P' . $updatedId;
    }
}

function addNewProduct($con, $product_id, $farm_id, $product_name, $quantity, $unit, $category_id, $product_price, $product_img, $description)
{
    $query = $con->prepare('INSERT INTO products (product_id, farm_id, product_name, quantity, unit, category_id, product_price, product_img, description) VALUES (:product_id, :user_id, :product_name, :quantity, :unit, :category_id, :product_price, :product_img, :description)');

    $query->bindParam(':product_id', $product_id);
    $query->bindParam(':user_id', $farm_id);
    $query->bindParam(':product_name', $product_name);
    $query->bindParam(':quantity', $quantity);
    $query->bindParam(':unit', $unit);
    $query->bindParam(':category_id', $category_id);
    $query->bindParam(':product_price', $product_price);
    $query->bindParam(':product_img', $product_img);
    $query->bindParam(':description', $description);

    return $query->execute();
}

function fetchCategories($con) {
    $query = $con->prepare('SELECT * FROM product_categories');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container contentArea" style="margin-left: -30px;margin-right:10px">

    <div class="row2">
        <div class="col4 mx-5 my-4" style="text-align: left; width:500px;">

            <div class="card-header card text-white" style="background-color: #40826D;">
                <h2 class="display-6 text-center" style="font-size: 30px; font-weight:500;">Product Details</h2>
            </div>
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">

                <input type="hidden" class="form-control" value="<?php echo $farm_id; ?>" name="user_id" id="user_id" readonly>
                <div class="col-md-12">
                    <label for="product_id" class="form-label">Product ID:</label>
                    <input class="form-control" type="text" name="product_id" id="product_id" value="<?php echo getLastProductId($con, $farm_id); ?>" readonly>
                </div>
                <div class="col-md-12">
                    <label for="product_category" class="form-label">Select Category:</label>
                    
                    <select class="form-select" name="category_id">
                    <?php  
                    $categories = fetchCategories($con); 
                    foreach($categories as $category) {?>
                        <option value="<?php echo $category["category_id"] ?>"><?php echo $category["category_name"]; ?></option> 
                    <?php } ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label for="product_name" class="form-label">Product Name:</label>
                    <input class="form-control" type="text" name="product_name" id="product_name" required>
                </div>

                <div class="col-md-12">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input class="form-control" type="number" name="quantity" id="quantity" required>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Unit:</label>
                    </div>
                    <div class="col-md-4 form-check">
                        <input type="radio" class="form-check-input" name="unit" value="kilogram" id="kilogram">
                        <label class="form-check-label" for="kilogram">kg</label>
                    </div>
                    <div class="col-md-4 form-check">
                        <input type="radio" class="form-check-input" name="unit" value="pieces" id="pieces">
                        <label class="form-check-label" for="pieces">Pieces</label>
                    </div>
                </div>

                <div class="col-md-12">
                    <label for="selling_price" class="form-label">Selling Price:</label>
                    <input class="form-control" type="number" name="selling_price" id="selling_price" required>
                </div>

                <div class="col-md-12">
                    <label for="product_img" class="form-label">Product Image:</label>
                    <input class="form-control" type="file" name="product_img" id="product_img" required>
                </div>

                <div class="col-md-12">
                    <label for="product_description" class="form-label">Description:</label>
                    <textarea class="form-control" id="product_description" name="product_description" rows="3" required></textarea>
                </div>

                <div class="col-md-12" style="text-align: center;">
                    <button type="submit" class="btn btn-primary" name="add_product">Add Product</button>
                </div>
            </form>
        </div>

    </div>

</div>
<?php
$frame->last_part();
?>
