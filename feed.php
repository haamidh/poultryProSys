<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $feed_id = getLastFeedId($con, $user_id);
    $feed_name = $_POST['feed_name'];

    if ($con == false) {
        die("Error Establishing Connection: " . $con->errorInfo());
    }

    if (addNewFeed($con, $user_id, $feed_id, $feed_name)) {
        header('Location: medicine.php?msg=Data Updated Successfully&user_id=' . $user_id);
        ob_end_flush(); // Flush the buffer
        exit();
    } else {
        echo "Record not added";
    }
    
}
function getLastFeedId($con, $user_id){
    $query = $con->prepare("SELECT feed_id FROM feed WHERE user_id=? ORDER BY feed_id DESC LIMIT 1");

    if (!$query) {
        die("Running Query failed: " . $con->errorInfo()[2]);
    }

    $query->bindParam(1, $user_id, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return 'F0001';
    } else {
        $lastId = $row['feed_id'];
        $numSuffix = intval(substr($lastId, 1));
        $updatedId = sprintf('%04d', $numSuffix + 1);
        return 'F' . $updatedId;
    }
}
function addNewFeed($con, $user_id, $feed_id, $feed_name){
    $query = $con->prepare('INSERT INTO feed (user_id, feed_id, feed_name,) VALUES (:user_id, :feed_id, :feed_name,)');
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':feed_id', $feed_id);
    $query->bindParam(':feed_name', $feed_name);

    return $query->execute();
}
function getAllFeed($con, $user_id)
{
    $query = $con->prepare('SELECT * FROM feed WHERE user_id = :user_id');
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
          <div class="container contentArea">
          <div class="col float-left">
            
          <div class="card-header card text-white bg-success bg-gradient mb-3 col-lg-8">
                <h2 class="display-9 text-center">Feed Details</h2>
            </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
        <div class = "form-group row">
            <div class ="mb-3 col-8">
          <div class="input-group mb-3" >         
               <input type="text" class="form-control" value="<?php echo $user_id; ?>"  name="user_id" id="user_id" placeholder="User Id" readonly >
          </div>
          <div class="input-group mb-3" >
               <input type="text" class="form-control" value="<?php echo getLastFeedId($con, $user_id); ?>" name="user_id" id="user_id"  placeholder="Feed Id" readonly >
          </div>
          <div class="input-group mb-5" >              
               <input type="text" class="form-control" name="feed_name" id="formFeedName" placeholder="Feed Name" required>
          </div>
          <div class="d-grid mb-3">
              <input type="submit" class="btn btn-success" name="add_feed" value="Add Feed">
          </div>
          <div class="d-grid mb-3">
              <button type="button" class="btn btn-success">Buy feed</button>
          </div>
          <div class="d-grid mb-3">
              <button type="button" class="btn btn-success">Use feed</button>
          </div>
          </div>
    </div>
    </form>
    
    </div>
    </div>

   <div>
   
        <div class="container float-left">
        <div class="col">
                <br>
                    
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Feed ID</th>
                        <th scope="col">Feed Name</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $serialnum = 0;
                    $feeds = getAllFeed($con, $user_id);
                    foreach ($feeds as $feed) {
                        $serialnum++;
                    ?>
                        <tr>
                            <th><?php echo $serialnum; ?></th>
                            <td><?php echo $medicine['feed_id']; ?></td>
                            <td><?php echo $medicine['feed_name']; ?></td>
                            <td><a href="edit_feed.php?id=<?php echo $feed['feed_id']; ?>" class="btn btn-primary">Edit</a></td>
                            <td><a href="delete_feed.php" class="btn btn-danger">Delete</a></td>
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
