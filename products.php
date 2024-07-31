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
$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);


?>

<div class="container contentArea" style="margin-left: -30px;margin-right:10px">

    <div class="row2">
        <div class="col4 mx-5 my-4" style="text-align: left; width:500px;">

            <div class="card-header card text-white" style="background-color: #40826D;">
                <h2 class="display-6 text-center" style="font-size: 30px; font-weight:500;">Product Details</h2>
            </div>
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                <input type="hidden" class="form-control" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>
                <div class="col-md-12">
                    <label for="feed_id" class="form-label">Product ID:</label>
                    <input class="form-control" type="text" name="med_id" id="med_id" readonly>
                </div>
                <div class="col-md-12">
                    <label for="feed_id" class="form-label">Select Category:</label>
                    <select class="form-select">
                        <option disabled>Select Category</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label for="product_name" class="form-label">Product Name:</label>
                    <input class="form-control" type="text" name="product_name" id="product_name" required>
                </div>


                <div class="col-md-12">
                    <label for="selling_price" class="form-label">Selling Price:</label>
                    <input class="form-control" type="text" name="selling_price" id="selling_price" required>
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
                    <button type="submit" class="btn btn-primary" name="add_medicine">Add Medicine</button>
                </div>
            </form>
        </div>

    </div>

</div>
<?php
$frame->last_part();
?>