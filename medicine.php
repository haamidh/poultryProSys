<?php
require_once 'config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_GET['user_id'] ?? '';

$database = new Database();
$con = $database->getConnection();

// Function to get the last medicine ID
function getLastMedicineId($con)
{
    $query = "SELECT med_id FROM medicine ORDER BY med_id DESC LIMIT 1";
    $result = $con->query($query);
    if (!$result) {
        die("Query failed: " . $con->errorInfo());
    }

    $row = $result->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return 'M0001';
    } else {
        $lastId = $row['med_id'];
        $extractedNum = intval(substr($lastId, 1));
        $updatedId = sprintf('%04d', $extractedNum + 1);
        return 'M' . $updatedId;
    }
}

// Function to add a new medicine
function addMedicine($con, $user_id, $med_id, $med_name, $med_description)
{
    $sql = "INSERT INTO medicine (user_id, med_id, med_name, description) VALUES (:user_id, :med_id, :med_name, :med_description)";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':med_id', $med_id);
    $stmt->bindParam(':med_name', $med_name);
    $stmt->bindParam(':med_description', $med_description);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Function to retrieve all medicines
function getAllMedicines($con)
{
    $query2 = "SELECT * FROM medicine";
    $result2 = $con->query($query2);
    if (!$result2) {
        die("Query failed: " . $con->errorInfo());
    }

    return $result2;
}

// Perform actions based on form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $med_id = $_POST['med_id'];
    $med_name = $_POST['med_name'];
    $med_description = $_POST['med_description'];

    if ($con == false) {
        die("Error Establishing Connection" . $con->errorInfo());
    }

    if (addMedicine($con, $user_id, $med_id, $med_name, $med_description)) {
        header('Location: medicine.php?msg=Data Updated Successfully&user_id=' . $user_id);
        exit();
    } else {
        echo "Record not added";
    }
}

$result2 = getAllMedicines($con);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Medicine Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body class="bg-dark">
    <div class="container">
        <?php if (isset($_GET["msg"])) : ?>
            <div class="card-header card text-white bg-dark mb-3">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?php echo $_GET["msg"]; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-sm-4 float-left">
            <div class="card-header card text-white bg-dark mb-3">
                <h2 class="display-6 text-center">Medicine Details</h2>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="user_id" class="card text-white bg-dark mb-3">USER ID</label>
                    <input class="form-control" type="text" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>
                </div>
                <div class="form-group">
                    <label for="med_id" class="card text-white bg-dark mb-3">Medicine ID</label>
                    <input class="form-control" type="text" value="<?php echo getLastMedicineId($con); ?>" name="med_id" id="med_id" readonly>
                </div>
                <div class="form-group">
                    <label for="med_name" class="card text-white bg-dark mb-3">Medicine Name</label>
                    <input class="form-control" type="text" name="med_name" id="med_name" required>
                </div>
                <div class="form-group">
                    <label for="med_description" class="card text-white bg-dark mb-3">Description</label>
                    <textarea class="form-control" id="med_description" name="med_description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" name="add_medicine" value="Add Medicine">
                </div>
            </form>
        </div>

        <div class="card mt-5">
            <table class="table">
                <thead class="thead-dark">
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
                    while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
                        $serialnum++;
                        ?>
                        <tr>
                            <th><?php echo $serialnum; ?></th>
                            <td><?php echo $row2['med_id']; ?></td>
                            <td><?php echo $row2['med_name']; ?></td>
                            <td><?php echo $row2['description']; ?></td>
                            <td><a href="editmedicine.php?id=<?php echo $row2['med_id']; ?>" class="btn btn-primary">Edit</a></td>
                            <td><a href="#" class="btn btn-danger">Delete</a></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</body>
</html>

<?php
$con = null;
?>
