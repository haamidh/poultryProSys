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

//db
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve POST data
    $category_id = $_POST["category_id"];
    $expense_name = $_POST["expense_name"];
    $handled_by = $_POST["handled_by"];
    $expense_amount = $_POST["expense_amount"];
    $misc_description = $_POST["misc_description"];
    $payment_method = $_POST["payment_method"];
    $date = $_POST["date"];  // Fetch the date input from the form

    // Set the values in the MisExpenses object
    $misEx->setCategory_id($category_id);
    $misEx->setExpense_name($expense_name);
    $misEx->setHandled_by($handled_by);
    $misEx->setExpense_amount($expense_amount);
    $misEx->setMisc_description($misc_description);
    $misEx->setPayment_method($payment_method);
    $misEx->setdate($date);  // Ensure date is correctly set

    // Check if the expense already exists
    if ($misEx->miscellaneousExpensesExists($user_id)) {
        $error_message = "This Miscellaneous Expense already exists";
    } else {
        // Try to create a new expense
        if ($misEx->create($user_id)) {
            $success_message = "Miscellaneous Expense added successfully.";
        } else {
            $error_message = "Failed to add miscellaneous Expense.";
        }
    }
}








function fetchCategories($con)
{
    $query = $con->prepare('SELECT * FROM miscellaneous_category');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

$categories = fetchCategories($con);

$misEx = $misEx->read($user_id);

?>

<html>

<head>
    <title>Expenses</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f8fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: #356854;
            color: #fff;
            border-radius: 10px 10px 0 0;
            padding: 15px;
            text-align: center;
        }

        .card-title {
            font-size: 24px;
            margin: 0;
        }

        .card-body {
            background-color: #F5F5F5;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }

        .form-control {
            border-radius: 5px;
            height: 45px;
            margin-bottom: 20px;
            padding: 10px 15px;
            font-size: 16px;
        }

        .form-label {
            text-align: left;
            display: block;
            font-weight: bold;
        }

        .btn {
            background-color: #3E497A;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #2c3665;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .table {
            width: 100%;
            background-color: #fff;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table thead {
            background-color: #3E497A;
            color: white;
        }

        .table th,
        .table td {
            padding: 15px;
            text-align: center;
        }

        .table tbody tr {
            background-color: #fff;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.05);
            border-radius: 5px;
        }

        .table tbody tr:hover {
            background-color: #f0f2f5;
        }

        .table tbody tr td {
            border-top: 1px solid #e9ecef;
        }

        .input-group {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .input-group input {
            border-radius: 5px;
            height: 45px;
            padding: 10px;
        }

        .input-group span {
            background-color: white;
            border: none;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        /* Responsive styling */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .card-header {
                padding: 10px;
            }

            .card-body {
                padding: 20px;
            }

            .form-control {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
        <div class="container">
            <div class="row my-5">

                <!-- Expense Form Section -->
                <div class="col-lg-5 col-md-10 col-12 mb-3">
                    <div class="card shadow">
                        <div class="card-header" style="background-color: #356854;">
                            <h5 class="card-title text-white mb-0">
                                <strong style="font-size: 24px;">Add Expenses</strong>
                            </h5>
                        </div>
                        <div class="card-body" style="background-color: #F5F5F5;">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Select Category:</label>
                                    <select name="category_id" class="form-control" required>
                                        <option disabled selected>Select Category</option>
                                        <?php foreach ($categories as $category) { ?>
                                            <option value="<?= $category['category_id'] ?>">
                                                <?= $category['category_name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="expense_name" class="form-label">Expense Name</label>
                                    <input type="text" class="form-control" id="expense_name" name="expense_name" placeholder="e.g: transportation, light bill" required>
                                </div>
                                <div class="mb-3">
                                    <label for="handled_by" class="form-label">Handled By</label>
                                    <input type="text" class="form-control" placeholder="Name of the person who performed this expense" id="handled_by" name="handled_by" required>
                                </div>
                                <div class="mb-3">
                                    <label for="expense_amount" class="form-label">Expense Amount</label>
                                    <input type="number" class="form-control" id="expense_amount" name="expense_amount" placeholder="e.g: Rs.100/=" required>
                                </div>
                                <div class="mb-3">
                                    <label for="misc_description" class="form-label">Miscellaneous Description</label>
                                    <input type="text" class="form-control" id="misc_description" name="misc_description" placeholder="Enter description">
                                </div>
                                <div class="mb-3">
    <label for="payment_method" class="form-label">Payment Method</label>
    <select class="form-control" id="payment_method" name="payment_method" required>
        <option value="" disabled selected>Select Payment Method</option>
        <option value="cheque">Cheque</option>
        <option value="cash">Cash</option>
        <option value="card">Card</option>
        <option value="online">Online Payment</option>
        <option value="bank_transfer">Bank Transfer</option>
    </select>
</div>

                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" required>
                                </div>
                                <div class="d-grid gap-2">
                                    <input type="submit" class="btn" value="Submit">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Miscellaneous Expenses Details Section -->
                <div class="col-lg-7 col-md-10 col-12 mb-3">
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                            <h5 class="card-title text-white mb-0">
                                <strong style="font-size: 25px;">Miscellaneous Expenses Details</strong>
                            </h5>
                            <div class="input-group" style="width: 250px;">
                                <input type="text" id="searchSupplierInput" class="form-control" placeholder="Search Expenses name..." onkeyup="searchMiscellaneous()">
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
                                            <th scope="col">Expense Name</th>
                                            <th scope="col">Handled By</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col" style="width: 32%">Option</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $serialnum = 0;
                                        foreach ($misEx as $misEx) {
                                            $serialnum++;
                                        ?>
                                            <tr>
                                                <th><?php echo $serialnum; ?></th>
                                                <td><?php echo $misEx['expense_name']; ?></td>
                                                <td><?php echo $misEx['handled_by']; ?></td>
                                                <td><?php echo $misEx['expense_amount']; ?></td>
                                                <td>
                                                    <a href="edit_MisExpenses.php?expense_id=<?php echo $misEx['expense_id']; ?>" class="btn btn-success text-light py-1 px-2">Edit</a>
                                                    <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $misEx['expense_id']; ?>)">Delete</button>
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
        function myFunction(category_id) {
            if (confirm("Are you sure you want to delete this Expense?")) {
                window.location.href = "delete_MisExpenses.php?category_id=" + category_id;
            }
        }

        function searchMiscellaneous() {
            var input = document.getElementById("searchSupplierInput");
            var filter = input.value.toUpperCase();
            var table = document.querySelector(".table");
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) {
                var expenseName = rows[i].getElementsByTagName("td")[1];
                if (expenseName) {
                    var nameValue = expenseName.textContent || expenseName.innerText;
                    if (nameValue.toUpperCase().indexOf(filter) > -1) {
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
