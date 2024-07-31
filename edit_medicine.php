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

<div class="container contentArea" style="margin-left: -30px;margin-right:10px">

    <div class="row2" >
        <div class="col4 mx-5 my-4" style="text-align: left; width:500px;">

            <div class="card-header card text-white" style="background-color: #40826D;">
                <h2 class="display-6 text-center" style="font-size: 30px; font-weight:500;">Medicine Details</h2>
            </div>
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                <input type="hidden" class="form-control" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>

                <div class="col-md-12">
                    <label for="feed_id" class="form-label">Medicine ID:</label>
                    <input class="form-control" type="text" value="<?php echo htmlspecialchars($med_id); ?>" name="med_id" id="med_id" readonly>
                </div>

                <div class="col-md-12">
                    <label for="feed_name" class="form-label">Medicine Name:</label>
                    <input class="form-control" type="text" name="med_name" id="med_name" value="<?php echo htmlspecialchars($med_name); ?>" required>
                </div>

                <div class="col-md-12">
                    <label for="feed_name" class="form-label">Description:</label>
                    <textarea class="form-control" id="med_description" name="med_description" rows="3" required><?php echo htmlspecialchars($med_description); ?></textarea>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button type="submit" class="btn btn-primary" name="Update">Update Medicine</button>
                </div>
            </form>
        </div>


    </div>

</div>
<?php
$frame->last_part();
ob_end_flush();
?>