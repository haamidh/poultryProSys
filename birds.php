<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'frame.php';
require_once 'checkLogin.php';
require_once 'Bird.php';


// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: login.php");
    exit();
}


// Retrieve the id from the URL
$user_id = $_SESSION["user_id"];

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);
?>

<div class="contentArea">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>BIRDS</h3>
                    </div>
                    <div class="card-body">
                        <!-- Add the submit button -->
                        <button class="btn btn-primary">
                            <a href="add_birds.php" class="text-light">Add Batch</a>
                        </button>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">BatchID</th>
                                    <th scope="col">SupplierID</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Unit Price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $database = new Database();
                                $db = $database->getConnection();

                                $bird = new Bird($db);
                                $birds = $bird->read($farm['user_id']); // Pass the user_id here

                                if (!$birds) {
                                    $birds = [];
                                }

                                $uid = 1;

                                foreach ($birds as $row) {
                                ?>
                                    <tr>
                                        <td><?php echo $uid ?></td>
                                        <td><?php echo $row['batch_id'] ?></td>
                                        <td><?php echo $row['sup_id'] ?></td>
                                        <td><?php echo $row['bird_type'] ?></td>
                                        <td><?php echo $row['unit_price'] ?></td>
                                        <td><?php echo $row['quantity'] ?></td>
                                        <td><?php echo $row['total_cost'] ?></td>
                                        <td><?php echo $row['date'] ?></td>
                                        <td>
                                            <button class="btn btn-success">
                                                <a href="update_birds.php?edit=<?php echo $row['batch_id']; ?>" class="text-light">Edit</a>
                                            </button>

                                            &nbsp;
                                            <button class="btn btn-danger">
                                                <a href="delete_birds.php?delete=<?php echo htmlspecialchars($row['batch_id']); ?>" class="text-light">Delete</a>
                                            </button>
                                        </td>
                                    </tr>
                                <?php
                                    $uid++;
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

<?php
$frame->last_part();
?>