<?php
if (session_status() == PHP_SESSION_NONE) session_start();

ob_start(); // Start output buffering

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';
require_once 'delete_medicine.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please Login before Proceeding");
    ob_end_flush(); // Flush the buffer
    exit();
}

$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);
$deleteMedicine = new DeleteMedicine();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $med_name = $_POST['med_name'];
    $med_description = $_POST['med_description'];
    $med_id = getLastMedicineId($con, $user_id);

    if ($con == false) {
        die("Error Establishing Connection: " . $con->errorInfo());
    }

    if (addNewMedicine($con, $user_id, $med_id, $med_name, $med_description)) {
        header('Location: medicine.php?msg=Data Updated Successfully&user_id=' . $user_id);
        ob_end_flush(); // Flush the buffer
        exit();
    } else {
        echo "Record not added";
    }
}


function getLastMedicineId($con, $user_id)
{
    $query = $con->prepare("SELECT med_id FROM medicine WHERE user_id=? ORDER BY med_id DESC LIMIT 1");

    if (!$query) {
        die("Running Query failed: " . $con->errorInfo()[2]);
    }

    $query->bindParam(1, $user_id, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return 'M0001';
    } else {
        $lastId = $row['med_id'];
        $numSuffix = intval(substr($lastId, 1));
        $updatedId = sprintf('%04d', $numSuffix + 1);
        return 'M' . $updatedId;
    }
}

function addNewMedicine($con, $user_id, $med_id, $med_name, $med_description)
{
    $query = $con->prepare('INSERT INTO medicine (user_id, med_id, med_name, description) VALUES (:user_id, :med_id, :med_name, :description)');
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':med_id', $med_id);
    $query->bindParam(':med_name', $med_name);
    $query->bindParam(':description', $med_description);

    return $query->execute();
}

function getAllMedicines($con, $user_id)
{
    $query = $con->prepare('SELECT * FROM medicine WHERE user_id = :user_id');
    $query->bindParam(':user_id', $user_id);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container contentArea" style="margin-left: -30px;margin-right:10px">

    <div class="row2">
        <div class="col4 mx-5 my-4" style="text-align: left; width:500px;">

            <div class="card-header card text-white" style="background-color: #40826D;">
                <h2 class="display-6 text-center" style="font-size: 30px; font-weight:500;">Medicine Details</h2>
            </div>
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                <input type="hidden" class="form-control" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>

                <div class="col-md-12">
                    <label for="med_id" class="form-label">Medicine ID:</label>
                    <input class="form-control" type="text" value="<?php echo getLastMedicineId($con, $user_id); ?>" name="med_id" id="med_id" readonly>
                </div>

                <div class="col-md-12">
                    <label for="med_name" class="form-label">Medicine Name:</label>
                    <input class="form-control" type="text" name="med_name" id="med_name" required>
                </div>

                <div class="col-md-12">
                    <label for="med_description" class="form-label">Description:</label>
                    <textarea class="form-control" id="med_description" name="med_description" rows="3" required></textarea>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button type="submit" class="btn btn-primary" name="add_medicine">Add Medicine</button>
                </div>
            </form>
        </div>

        <div class="col5" style="margin-right: 10px;">
            <div class="col-md-12" style="text-align: center;margin-bottom:10px">
                <div class="card-header card text-white" style="background-color: #40826D;margin-top:150px;margin-left:20px">

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <button type="submit" class="btn btn-warning" name="buy_medicine" style="margin-right: 20px;">Buy Medicine</button>
                        <button type="submit" class="btn btn-info" name="use_medicine">Use Medicine</button>
                    </form>
                </div>

            </div>

        </div>
    </div>




    <div class="row2">

        <div class="col5" style="margin-right: 10px;">
            <br>
            <table class="table table-striped">
                <thead class="table">

                    <tr>
                        <th scope="col" style="background-color: #40826D;">#</th>
                        <th scope="col" style="background-color: #40826D;">MedID</th>
                        <th scope="col" style="background-color: #40826D;">Medicine Name</th>
                        <th scope="col" style="background-color: #40826D;">Description</th>
                        <th scope="col" colspan="2" style="background-color: #40826D;">Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $serialnum = 0;
                    $medicines = getAllMedicines($con, $user_id);
                    foreach ($medicines as $medicine) {
                        $serialnum++;
                    ?>
                        <tr>
                            <th><?php echo $serialnum; ?></th>
                            <td><?php echo $medicine['med_id']; ?></td>
                            <td><?php echo $medicine['med_name']; ?></td>
                            <td><?php echo $medicine['description']; ?></td>
                            <td><a href="edit_medicine.php?med_id=<?php echo $medicine['med_id']; ?>" class="btn btn-primary">Edit</a></td>
                            <td><a href="delete_medicine.php?med_id=<?php echo $medicine['med_id']; ?>" class="btn btn-danger">Delete</a></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$frame->last_part();
?>