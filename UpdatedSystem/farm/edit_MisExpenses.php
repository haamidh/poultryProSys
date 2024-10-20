<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/MisExpenses.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

// Instantiate the MisExpenses class
$misEx = new MisExpenses($con);

// Get category ID from GET request
$expense_id = isset($_GET['expense_id']) ? $_GET['expense_id'] : '';

if ($expense_id) {
    // Get expense details
    $expense_details = $misEx->readOneExpense($expense_id);
    if ($expense_details) {
        // Set values for form fields
        $expense_id = $expense_details['expense_id'];
        $expense_name = $expense_details['expense_name'];
        $handled_by = $expense_details['handled_by'];
        $expense_amount = $expense_details['expense_amount'];
        $misc_description = $expense_details['misc_description'];
        $payment_method = $expense_details['payment_method'];
        $date = $expense_details['date'];
    } else {
        echo "No category found with the provided ID.";
        exit();
    }
} else {
    echo "Category ID is required.";
    exit();
}

// Fetch categories for the select dropdown
function fetchCategories($con)
{
    $query = $con->prepare('SELECT * FROM miscellaneous_category');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

$categories = fetchCategories($con);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set expense properties
    $misEx->setCategory_id($_POST['category_id']);
    $misEx->setExpense_name($_POST['expense_name']);
    $misEx->setHandled_by($_POST['handled_by']);
    $misEx->setExpense_amount($_POST['expense_amount']);
    $misEx->setMisc_description($_POST['misc_description']);
    $misEx->setPayment_method($_POST['payment_method']);
    $misEx->setDate($_POST['date']);

    // Update the expense
    if ($misEx->update($expense_id)) {
        header("Location: MisExpenses.php?msg=Category Updated Successfully");
        ob_end_flush();
        exit();
    } else {
        echo "<p>Failed to update expenses</p>";
    }
}
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 text-center justify-content-center">
            <div class="col-lg-6 col-md-10 col-12 mb-3 my-5 px-5">
                <div class="card">
                    <div class="card-header p-3 text-center" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Edit Expenses</strong></h5>
                    </div>
                    <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?category_id=" . $category_id; ?>" method="POST">
                        <div class="row p-2">
                            <div class="col">
                                <div class="row">
                                    
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row p-2">
                            <div class="col">
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label">Expense Name:</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="text" name="expense_name" value="<?php echo htmlspecialchars($expense_name); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row p-2">
                            <div class="col">
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label">Handled By:</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" name="handled_by" rows="3" required><?php echo htmlspecialchars($handled_by); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row p-2">
                            <div class="col">
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label">Expense Amount:</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="text" name="expense_amount" value="<?php echo htmlspecialchars($expense_amount); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row p-2">
                            <div class="col">
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label">Misc Description:</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="text" name="misc_description" value="<?php echo htmlspecialchars($misc_description); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row p-2">
                            <div class="col">
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label">Payment Method:</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="text" name="payment_method" value="<?php echo htmlspecialchars($payment_method); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row p-2">
                            <div class="col">
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label">Date:</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row px-3" style="text-align:center;">
                            <button type="submit" class="btn btn-primary" name="Update">Update</button>
                        </div>
                        <div class="row px-3 mt-2" style="text-align:center;">
                            <a href="MisExpenses.php" class="btn btn-danger">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$frame->last_part();
?>
