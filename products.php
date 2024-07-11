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

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="style.css">
    
</head>

<body>
    <div class="container contentArea">
        <div class="col float-left">
            <div class="card-header card text-white bg-success bg-gradient mb-3">
                <h3 class="display-6 text-left">Add Product</h3>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group row">
                    <div class="mb-1 col-sm">
                        <div class="card-body">
                            <div class="card mb-3 bg-success bg-gradient">
                                <div class="card-header mb-0 text-white">Farm Name</div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-12 mb-1">
                        <select class="form-select">
                            <option selected>Select Category</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-1">
                        <label for="">Name</label>
                        <input type="text" required name="name" placeholder="Enter product name"class="form-control">
                    </div>
                    
                    <div class="col-md-12 mb-1">
                        <label for="">Small Description</label>
                        <textarea rows="3" required name="small_description" placeholder="Enter small description"class="form-control"></textarea>
                    </div>
                    <div class="col-md-6 mb-1">
                        <label for="">Original Price</label>
                        <input type="text" required name="original_price" placeholder="Enter Original Price"class="form-control">
                    </div>
                    <div class="col-md-6 mb-1">
                        <label for="">Selling Price</label>
                        <input type="text" required name="selling_price" placeholder="Enter Selling Price" class="form-control">
                    </div>
                    <div class="col-md-12 mb-1">
                        <label for="">Upload Image</label>
                        <input type="file" required name="image" class="form-control">
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <label for="">Quantity</label>
                            <input type="number" required name="qty" placeholder="Enter Quantity"class="form-control">
                        </div>
                        <div class="col-md-1 ">
                            
                        </div>
                        <div class="col-md-2 mt-1"><br>
                            <label for="">Status</label>
                            <input type="checkbox" name="status">
                        </div>
                        <div class="col-md-3 mt-1"><br>
                            <label for="">Popular</label>
                            <input type="checkbox" name="popular">
                        </div>
                    </div>
                    
                    
                    
                    <div class="col-md-12 mb-1">
                        <button input type="submit" class="btn btn-primary mt-1"  name="add_product_btn">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>

</html>