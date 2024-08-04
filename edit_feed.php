<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';

if (!isset($_SESSION['user_id'])) {
    header("location: login.php?msg=Please Login before Proceeding");
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
    $feed_id = $_POST['feed_id'];
    $feed_name = $_POST['feed_name'];
    
    if (updateFeed($con, $user_id, $feed_id, $feed_name)) {
        header("Location: feed.php?msg=Feed Updated Successfully");
        ob_end_flush();
        exit();
    } else {
        echo "<p>Failed to update feed.</p>";
    }

}

function updateFeed($con, $user_id, $feed_id, $feed_name)
{
    $query = $con->prepare("UPDATE feed SET feed_name = :feed_name WHERE user_id=:user_id AND feed_id=:feed_id ");
    $query->bindParam(":user_id", $user_id);
    $query->bindParam(":feed_id", $feed_id);
    $query->bindParam(":feed_name", $feed_name);
    return $query->execute();
}

function getFeedDetails($con, $user_id, $feed_id)
{
    $query = $con->prepare('SELECT feed_name FROM feed WHERE user_id = :user_id AND feed_id = :feed_id');
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':feed_id', $feed_id);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

// Assuming you get the feed_id from a GET request or elsewhere
$feed_id = isset($_GET['feed_id']) ? $_GET['feed_id'] : '';

if ($feed_id) {
    $feed = getFeedDetails($con, $user_id, $feed_id);
    if ($feed) {
        $feed_name = $feed['feed_name'];
    } else {
        echo "No feed found with the provided ID.";
        exit();
    }
} else {
    echo "Feed ID is required.";
    exit();
}
?>

<div class="container contentArea" style="margin-left: -30px;margin-right:10px">
    <div class="row2">
        <div class="col4 mx-5 my-4" style="text-align: left; width:500px;">
            <div class="card-header card text-white" style="background-color: #40826D;">
                <h2 class="display-6 text-center" style="font-size: 30px; font-weight:500;">Feed Details</h2>
            </div>
            <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <input type="hidden" class="form-control" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>
                <div class="col-md-12">
                    <label for="feed_id" class="form-label">Feed ID:</label>
                    <input class="form-control" type="text" value="<?php echo htmlspecialchars($feed_id); ?>" name="feed_id" id="feed_id" readonly>
                </div>
                <div class="col-md-12">
                    <label for="feed_name" class="form-label">Feed Name:</label>
                    <input class="form-control" type="text" name="feed_name" id="feed_name" value="<?php echo htmlspecialchars($feed_name); ?>" required>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button type="submit" class="btn btn-primary" name="Update">Update Feed</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$frame->last_part();
ob_end_flush();
?>