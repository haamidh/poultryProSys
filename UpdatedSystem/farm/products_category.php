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

?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 text-center justify-content-center">

        <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <div class="card shadow">
                    <div class="card-header p-3 text-center" style="background-color: #356854;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Add New Product category</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #D4C8DE;">

                    <div class="row p-2">
                        <div class="col-sm-12">
                            <div class="row">
                                <label class="col-sm-6 col-form-label">New Category Name:</label>
                                <div class="col-sm-12 mb-3">
                                    <input type="text" class="form-control" id="least_quantity"
                                        name="least_quantity">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row px-3" style="text-align:center;">
                        <button type="submit" class="btn btn-primary" name="add_category">Add Category</button>
                    </div>

                    </div>
                </div>
        </div>


        </div>
    </div>
</main>