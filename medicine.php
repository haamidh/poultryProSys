<?php
if (session_status() == PHP_SESSION_NONE) session_start();

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please Login before Proceeding");
    exit();
}
$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

//assign variables based on user input
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $med_id = $_POST['med_id'];
    $med_name = $_POST['med_name'];
    $med_description = $_POST['med_description'];

    //calling add new medicine function
    addNewMedicine($con, $user_id, $med_id, $med_name, $med_description);
}

function getLastMedicineId($con, $user_id)
{
    $query = $con->prepare("SELECT med_id FROM medicine WHERE user_id=? ORDER BY med_id DESC LIMIT 1");
    $query->bindParam("i", $user_id);

    if (!$query) {
        die("Running Query failed : " . $con->error);
    }
    $row = $query->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return 'M0001';
    } else {
        $lastId = $row['med_id'];
        $numSuffix = intval(substr($lastId, 1));
        $updatedId = sprintf('%4d', $numSuffix + 1);
        return 'M' . $updatedId;
    }
}

function addNewMedicine($con, $user_id, $med_id, $med_name, $med_description)
{
    $query = $con->prepare('INSERT into medicine(user_id,med_id,med_name,description) VALUES(:user_id, :med_id, :med_name, :description)');
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':med_id', $med_id);
    $query->bindParam(':med_name', $med_name);
    $query->bindParam(':description', $med_description);
}

function getAllMedicines($con, $user_id)
{
    $query = $con->prepare('SELECT * FROM medicine WHERE user_id= :user_id');
    $query->bindParam(':user_id', $user_id);
    $query->execute();
    $row = $query->fetchAll(PDO::FETCH_ASSOC);
    return $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<!-- <body class="bg">
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
                    <input class="form-control" type="text" value="" name="user_id" id="user_id" readonly>
                </div>
                <div class="form-group">
                    <label for="med_id" class="card text-white bg-dark mb-3">Medicine ID</label>
                    <input class="form-control" type="text" value="" name="med_id" id="med_id" readonly>
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
                    $medicines = getAllMedicines($con, $user_id);
                    foreach ($medicines as $medicine) {
                        $serialnum++;
                        ?>
                        <tr>
                            <th><?php echo $serialnum; ?></th>
                            <td><?php echo $medicine['med_id']; ?></td>
                            <td><?php echo $medicine['med_name']; ?></td>
                            <td><?php echo $medicine['description']; ?></td>
                            <td><a href="editmedicine.php?id=<?php echo $medicine['med_id']; ?>" class="btn btn-primary">Edit</a></td>
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
</body> -->
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <div class="container contentArea">
        <div class="col-sm-4 float-left">
            <div class="card-header card text-white bg-success bg-gradient mb-3">
                <h2 class="display-6 text-center ">Medicine Details</h2>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group row">
                    <div class="mb-3 col-sm">
                        <div class="card-body ">
                            <div class="card mb-3 bg-success bg-gradient">
                                <div class="card-header text-white">Farm Name</div>
                            </div>
                            <input class="form-control" type="text" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>
                        </div>
                    </div>
                    <div class=" mb-3 col-sm">
                        <div class="card-body ">
                            <div class="card mb-3 bg-dark bg-gradient">
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
                    <div class="form-group mb-3 col-sm">
                        <div class="card mb-3 bg-dark bg-gradient">
                            <div class="card-header text-white">Description</div>
                        </div>
                        <textarea class="form-control" id="med_description" name="med_description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-success bg-gradient" name="add_medicine" value="Add Medicine">
                    </div>
            </form>

            <div class="display">
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
                        $medicines = getAllMedicines($con, $user_id);
                        foreach ($medicines as $medicine) {
                            $serialnum++;
                        ?>
                            <tr>
                                <th><?php echo $serialnum; ?></th>
                                <td><?php echo $medicine['med_id']; ?></td>
                                <td><?php echo $medicine['med_name']; ?></td>
                                <td><?php echo $medicine['description']; ?></td>
                                <td><a href="editmedicine.php?id=<?php echo $medicine['med_id']; ?>" class="btn btn-primary">Edit</a></td>
                                <td><a href="#" class="btn btn-danger">Delete</a></td>
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