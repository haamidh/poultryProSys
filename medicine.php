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

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    
    <div class="container contentArea">
        
        <div class="col float-left">
            <div class="card-header card text-white bg-success bg-gradient mb-3">
                <h2 class="display-6 text-center">Medicine Details</h2>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group row">
                    <div class="mb-3 col-sm">
                        <div class="card-body">
                            <div class="card mb-3 bg-success bg-gradient">
                                <div class="card-header text-white">Farm Name</div>
                            </div>
                            <input class="form-control" type="text" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>
                        </div>
                    </div>
                    <div class="mb-3 col-sm">
                        <div class="card-body">
                            <div class="card mb-3 bg-success bg-gradient">
                                <div class="card-header text-white">Medicine ID</div>
                            </div>
                            <input class="form-control" type="text" value="<?php echo getLastMedicineId($con, $user_id); ?>" name="med_id" id="med_id" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="card mb-3 bg-dark bg-gradient">
                            <div class="card-header text-white">Medicine Name</div>
                        </div>
                        <input class="form-control" type="text" name="med_name" id="med_name" required>
                    </div>
                    <div class="form-group mb-3 col-sm"><br>
                        <div class="card mb-3 bg-dark bg-gradient">
                            <div class="card-header text-white">Description</div>
                        </div>
                        <textarea class="form-control" id="med_description" name="med_description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-success bg-gradient" name="add_medicine" value="Add Medicine">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-sm">
        <div class="container float-left ">
            <div class="col">
                <br>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Medicine ID</th>
                        <th scope="col">Medicine Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
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
</body>

</html>