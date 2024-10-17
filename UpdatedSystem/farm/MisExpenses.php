<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';
require_once '../classes/MisExpenses.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

$misEx = new MisExpenses($con);
$misEx->setUser_id($user_id);


function fetchCategories($con) {
    $query = $con->prepare('SELECT * FROM miscellaneous_categories');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

$categories = fetchCategories($con);

$misEx = $misEx->read($user_id);

?>

<html>

<head>
    <title>miscellaneous</title>
    <style>
        .form-label {
            text-align: left;
            display: block;
            /* Ensures it behaves like a block-level element */
        }

        .card {
            border: none;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
        <div class="container">
            <div class="row my-5 text-center">

                <div class="col-lg-5 col-md-10 col-12 mb-3 px-5">
                    <div class="card shadow">
                        <div class="card-header p-3 text-center" style="background-color: #356854;">
                            <h5 class="card-title text-white mb-0">
                                <strong style="font-size: 24px;">Add Miscellaneous </strong>
                            </h5>
                        </div>
                        <div class="card-body" style="background-color: #F5F5F5;"></div>
                        

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
    
                        <div class="col-sm-6">
                                    <div class="row">
                                        <label class="col-sm-8 col-form-label">Select Category:</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <select name="category_id" class="form-control" required>
                                                <option disabled selected>Select Category</option>
                                                <?php foreach ($categories as $category) { ?>
                                                    <option value="<?= $category['category_id'] ?>">
                                                        <?= $category['category_name'] ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="staticEmail" class="form-control">Expense_name</label>
                                <input type="text" class="form-control" id="expense_name"
                                    placeholder="e.g : transportation,lightbill" name="expense_name">
                            </div>
                            <div class="mb-3">
                                <label for="staticEmail" class="form-control">Handled_by</label>
                                <input type="text" class="form-control" id="handled_by" name="handled_by">
    
                            </div>
                            <div class="mb-3">
                                <label for="staticEmail" class="form-control">Expense_amount</label>
                                <input type="text" class="form-control" id="expense_amount" placeholder="e.g : Rs.100/="
                                    name="expense_amount">
    
                            </div>
                            <!-- <div class="mb-3">
        <label for="inputPassword" class="form-control">category_description</label>
          <input type="text" class="form-control" id="category_description" name="category_description" placeholder="detailed description">
      </div> -->
                            <div class="mb-3">
                                <label for="category_description" class="form-control">misc_description</label>
                                <input type="text" class="form-control" id="misc_description" name="misc_description"
                                    placeholder="detailed description">
    
                            </div>
                            <div class="mb-3">
                                <label for="category_description" class="form-control">payment_method</label>
                                <input type="text" class="form-control" id="payment_method" name="payment_method"
                                    placeholder="cheque,cash,card">
    
                            </div>
                            <div class="mb-3">
                                <label for="category_description" class="form-control">date</label>
                                <input type="text" class="form-control" id="date" name="date" placeholder="date">
    
                            </div>
                            <div class="d-grid gap-2">
                                <input type="submit" id="submit" value="submit">
                            </div>
    
    
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    
    
    </body>
    
    </html>