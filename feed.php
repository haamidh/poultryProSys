<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_feed'])) {
        $feed_name = $_POST['feed_name'];
        $feed_id = getLastFeedId($con, $user_id);

        if (addNewFeed($con, $user_id, $feed_id, $feed_name)) {
            // Redirect after successful operation
            $addmessage = "Record added";
        } else {
            echo "Record not added";
        }
    } elseif (isset($_POST['buy_feed'])) {
        // Handle "Buy Feed" logic here
    } elseif (isset($_POST['use_feed'])) {
        // Handle "Use Feed" logic here
    }
}

function getLastFeedId($con, $user_id)
{
    $query = $con->prepare("SELECT feed_id FROM feed WHERE user_id=? ORDER BY feed_id DESC LIMIT 1");
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

function addNewFeed($con, $user_id, $feed_id, $feed_name)
{
    $query = $con->prepare('INSERT INTO feed (user_id, feed_id, feed_name) VALUES (:user_id, :feed_id, :feed_name)');
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

<div class="container contentArea" style="margin-left: -30px;margin-right:10px">
    <?php if (isset($addmessage)) {
        echo $addmessage;
    }
    ?>
    <div class="row2">
        <div class="col4 mx-5 my-4" style="text-align: left; width:300px;">
            <div class="col-md-12" style="text-align: center;margin-bottom:10px">
                <div class="card-header card text-white" style="background-color: #40826D;">

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <button type="submit" class="btn btn-warning" name="buy_feed" style="margin-right: 20px;">Buy Feed</button>
                        <button type="submit" class="btn btn-info" name="use_feed">Use Feed</button>
                    </form>
                </div>

            </div>
            <div class="card-header card text-white" style="background-color: #40826D;">
                <h2 class="display-6 text-center" style="font-size: 30px; font-weight:500;">Feed Details</h2>
            </div>
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                <input type="hidden" class="form-control" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>

                <div class="col-md-12">
                    <label for="feed_id" class="form-label">Feed ID:</label>
                    <input type="text" class="form-control" value="<?php echo getLastFeedId($con, $user_id); ?>" id="feed_id" name="feed_id" readonly>
                </div>

                <div class="col-md-12">
                    <label for="feed_name" class="form-label">Feed Name:</label>
                    <input type="text" class="form-control" id="feed_name" name="feed_name" required>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button type="submit" class="btn btn-primary" name="add_feed">Add Feed</button>
                </div>
            </form>
        </div>

        <div class="col5" style="margin-right: 10px;">
            <br>
            <table class="table table-striped" style="width: 550px;">
                <thead class="table">
                    <tr>
                        <th scope="col" style="background-color: #40826D;">#</th>
                        <th scope="col" style="background-color: #40826D;">Feed ID</th>
                        <th scope="col" style="background-color: #40826D;">Feed Name</th>
                        <th scope="col" style="background-color: #40826D;">Option</th>
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
                            <td><?php echo $feed['feed_id']; ?></td>
                            <td><?php echo $feed['feed_name']; ?></td>
                            <td><a href="edit_feed.php?id=<?php echo $feed['feed_id']; ?>" class="btn btn-primary">Edit</a>
                                &nbsp;&nbsp;
                                <a href="delete_feed.php?id=<?php echo $feed['feed_id']; ?>" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$frame->last_part();
?>