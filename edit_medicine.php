<?php
ob_start(); // Start output buffering
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';

// Check whether user is logged in with the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farm') {
    header("Location: login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION['user_id'];
$med_id = $_GET['med_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $med_name = $_POST['med_name'];
    $med_description = $_POST['med_description'];
    $med_id = $_POST['med_id'];

    if (updateMedicine($con, $user_id, $med_id, $med_name, $med_description)) {
        header("Location: medicine.php?msg=Medicine Updated Successfully");
        exit();
    } else {
        echo "<p>Failed to update medicine.</p>";
    }
}

function updateMedicine($con, $user_id, $med_id, $med_name, $med_description)
{
    $query = $con->prepare('UPDATE medicine SET med_name = :med_name, description = :med_description WHERE user_id = :user_id AND med_id = :med_id');
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':med_id', $med_id);
    $query->bindParam(':med_name', $med_name);
    $query->bindParam(':med_description', $med_description);

    return $query->execute();
}

function getMedicineDetails($con, $user_id, $med_id)
{
    $query = $con->prepare('SELECT med_name, description FROM medicine WHERE user_id = :user_id AND med_id = :med_id');
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':med_id', $med_id);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

$medicine = getMedicineDetails($con, $user_id, $med_id);
$med_name = $medicine['med_name'];
$med_description = $medicine['description'];

?>

<div class="container contentArea">
    <div class="col float-left">
        <div class="card-header card text-white bg-success bg-gradient mb-3">
            <h2 class="display-6 text-center">Medicine Details</h2>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?med_id=" . $med_id); ?>" method="post">
            <div class="form-group row">
                <div class="mb-3 col-sm">
                    <div class="card-body">
                        <div class="card mb-3 bg-success bg-gradient">
                            <div class="card-header text-white">Farm Name</div>
                        </div>
                        <input class="form-control" type="text" value="<?php echo htmlspecialchars($user_id); ?>" name="user_id" id="user_id" readonly>
                    </div>
                </div>
                <div class="mb-3 col-sm">
                    <div class="card-body">
                        <div class="card mb-3 bg-success bg-gradient">
                            <div class="card-header text-white">Medicine ID</div>
                        </div>
                        <input class="form-control" type="text" value="<?php echo htmlspecialchars($med_id); ?>" name="med_id" id="med_id" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <div class="card mb-3 bg-dark bg-gradient">
                        <div class="card-header text-white">Medicine Name</div>
                    </div>
                    <input class="form-control" type="text" name="med_name" id="med_name" value="<?php echo htmlspecialchars($med_name); ?>" required>
                </div>
                <div class="form-group mb-3 col-sm"><br>
                    <div class="card mb-3 bg-dark bg-gradient">
                        <div class="card-header text-white">Description</div>
                    </div>
                    <textarea class="form-control" id="med_description" name="med_description" rows="3" required><?php echo htmlspecialchars($med_description); ?></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-success bg-gradient" name="Update" value="Update">
                </div>
            </div>
        </form>
    </div>
</div>
<div>

</div>
</div>

<?php
$frame->last_part();
ob_end_flush(); // End output buffering and flush the output
?>
