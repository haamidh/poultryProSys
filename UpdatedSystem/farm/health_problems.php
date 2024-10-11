<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once 'Frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Bird.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

// Retrieve the id from the session
$user_id = $_SESSION["user_id"];

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

// Fetch health status data
$database = new Database();
$db = $database->getConnection();

// Prepare and execute the query to fetch health status
$query = "SELECT * FROM health_status WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$healthStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);

$frame = new Frame();
$frame->first_part($farm);

$batchs = new Bird($db);
?>
<style>
    .card {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin: 20px 0;
    }
    .card-header {
        background-color: #3E497A;
    }
    .card-title {
        font-weight: bold; 
        font-size: 25px;
    }
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }
    .btn {
        transition: background-color 0.3s, color 0.3s;
    }
    .btn-outline-light:hover {
        background-color: #f8f9fa;
        color: #3E497A;
    }
    .btn-danger:hover {
        background-color: #dc3545;
        color: white;
    }
    .btn-success:hover {
        background-color: #28a745;
        color: white;
    }
</style>
<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 justify-content-center">
            <div class="col-lg-10 col-md-12 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title text-white mb-0">Health Details</h5>
                        <a href="birds_health.php" class="btn btn-outline-light"><i class="bi bi-calendar-heart-fill"></i> Add Health Problems</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Batch ID</th>
                                        <th>No Illness</th>
                                        <th>No Deaths</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Option</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php
$uid = 1;

// Check if there are any records
if (!empty($healthStatus)) {
    foreach ($healthStatus as $row) {
        $batch = $batchs->getBatch($row['batch_id']);
        ?>
                                    
                                            <tr>
                                                <td><?php echo $uid; ?></td>
                                                <td><?php echo htmlspecialchars($batch);?></td>
                                                <td><?php echo htmlspecialchars($row['no_illness']); ?></td>
                                                <td><?php echo htmlspecialchars($row['no_deaths']); ?></td>
                                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                                <td>
                                                    <a href="update_birds_health.php?status_id=<?php echo htmlspecialchars($row['status_id']); ?>" class="btn btn-success text-light py-1 px-2">Edit</a>
                                                    <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $row['status_id']; ?>)">Delete</button>
                                                </td>
                                            </tr>
        <?php
        $uid++;
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>No records found.</td></tr>";
}
?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$frame->last_part();
?>

<script>
    function myFunction(status_id) {
        if (confirm("Are you sure you want to delete this batch?")) {
            window.location.href = "delete_status.php?status_id=" + status_id;
        }
    }
</script>
