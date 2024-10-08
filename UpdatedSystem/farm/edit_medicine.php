<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Medicine.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

// Make medicine class object
$medicine = new Medicine($con);

// Get the med_id from a GET request
$med_id = isset($_GET['med_id']) ? $_GET['med_id'] : '';

if ($med_id) {
    // Get med details
    $med_details = $medicine->readOne($med_id);
    if ($med_details) {
        $med_name = $med_details['med_name'];
        $least_quantity = $med_details['least_quantity'];
        $description = $med_details['description'];
    } else {
        echo "No medicine found with the provided ID.";
        exit();
    }
} else {
    echo "Med ID is required.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set medicine properties
    $medicine->setMed_name($_POST['med_name']);
    $medicine->setLeastQuantity(number_format($_POST['least_quantity'], 2));
    $medicine->setDescription($_POST['description']);

    // Update Medicine details
    if ($medicine->update($med_id)) {
        header("Location: medicine.php?msg=Medicine Updated Successfully");
        ob_end_flush();
        exit();
    } else {
        $error_message = "Failed to update medicine.";
    }
}
?>
<style>
    .form-label {
        text-align: left;
        display: block; /* Ensures it behaves like a block-level element */
    }

    .card {
        border: none;
        border-radius: 10px;
    }

    /* Centering the form */
    .form-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
</style>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 overflow-auto">
    <div class="container">
        <div class="row form-container">
            <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <div class="card shadow">
                    <div class="card-header p-3 text-center" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Edit Medicine</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #F5F5F5;">

                        <!-- Success/Error Messages -->
                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger text-center">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Medicine Edit Form -->
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?med_id=" . $med_id; ?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Medicine Name:</label>
                                <input class="form-control" type="text" name="med_name" id="med_name" value="<?php echo htmlspecialchars($med_name); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notification Threshold:</label>
                                <input type="text" class="form-control" id="least_quantity" name="least_quantity" value="<?php echo htmlspecialchars($least_quantity); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description:</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($description); ?></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" name="Update">Update</button>
                            </div>
                            <div class="d-grid gap-2 mt-3">
                                <a href="medicine.php" class="btn btn-danger">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$frame->last_part();
?>
