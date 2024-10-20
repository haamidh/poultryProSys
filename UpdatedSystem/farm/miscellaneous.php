<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';
require_once '../classes/miscellaneous.php';
require '../classes/Validation.php';

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

$mis = new miscellaneous($con);
$mis->setUser_id($user_id);

$textErr = "";
$errors = false;

/*if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name=$_POST["category_name"];
    $category_description=$_POST["category_description"];

    $mis->setCategory_name($category_name);
    $mis->setCategory_description($category_description);
   

}
    */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST["category_name"];
    $category_description = $_POST["category_description"];
    
    $mis->setCategory_name($category_name);
    $mis->setCategory_description($category_description);

    if (!Validation::validateTextField($category_name, $textErr)) {
        $errors = true;
    }

    if (!$errors){
        // Call the create method to insert the new category
        if ($mis->miscellaneousExists($user_id)) {
            $error_message = "This expense already exists";
        } else {
            if ($mis->create($user_id)) {
                $success_message = "Expense added successfully.";
            } else {
                $error_message = "Failed to add the expense.";
            }
        }
    }
    
}




$miscellaneous = $mis->read($user_id);



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

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .text-danger {
            color: red;
        }

        .d-grid {
            display: grid;
            grid-template-columns: 1fr;
        }

        .d-grid .gap-2 {
            margin-top: 20px;
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

                <!-- Expense Category Form Section -->
                <div class="col-lg-5 col-md-10 col-12 mb-3">
                    <div class="card shadow">
                        <div class="card-header" style="background-color: #356854;">
                            <h5 class="card-title text-white mb-0">
                                <strong style="font-size: 24px;">Add Expense Category</strong>
                            </h5>
                        </div>
                        <div class="card-body" style="background-color: #F5F5F5;">
                            
                            <!-- Success and Error Messages -->
                            <?php if (isset($success_message)): ?>
                                <div class="alert alert-success text-center">
                                    <?php echo $success_message; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger text-center">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>

                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                                <div class="mb-3">
                                    <label for="category_name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="category_name"
                                           placeholder="e.g: transportation" name="category_name" onkeyup="validateCategoryName()" required>
                                    <small id="nameError" class="text-danger"><?php echo $textErr ?></small>
                                </div>

                                <div class="mb-3">
                                    <label for="category_description" class="form-label">Category Description</label>
                                    <input type="text" class="form-control" id="category_description"
                                           name="category_description" placeholder="Enter detailed description" required>
                                </div>

                                <div class="d-grid gap-2">
                                    <input type="submit" class="btn" value="Add Expense Category">
                                </div>
                            </form>

                            <!-- Link to Add Expenses -->
                            <div class="d-grid gap-2 mt-3">
                                <a href="MisExpenses.php" class="btn btn-success">Add Expenses</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        function validateCategoryName() {
            const categoryNameInput = document.getElementById('category_name');
            const nameErr = document.getElementById('nameError');
            const categoryNameValue = categoryNameInput.value;

            const regex = /^[a-zA-Z][a-zA-Z0-9\s-_]{2,}$/;

            nameErr.textContent = "";
            categoryNameInput.classList.remove("is-invalid");

            if (categoryNameValue.length > 0 && !regex.test(categoryNameValue)) {
                nameErr.textContent = "Category name must start with a letter and be at least 3 characters long.";
                categoryNameInput.classList.add("is-invalid");

                const correctedValue = categoryNameValue.replace(/^[^a-zA-Z]+/, '').replace(/[^a-zA-Z0-9\s-_]/g, '');

                categoryNameInput.value = correctedValue;
            }
        }
    </script>

</body>

</html>
