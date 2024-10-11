<?php
ob_start();
if (session_status() == PHP_SESSION_NONE)
    session_start();

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Bird.php'; // Include the Bird class
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

// Ensure the status ID is provided
if (!isset($_GET['status_id'])) {
    echo "<p>Invalid status ID</p>";
    exit();
}

$status_id = $_GET['status_id']; // No need to check isset($status_id) again
// Use the Bird class to fetch the health details
$bird = new Bird($con);
$healthDetails = $bird->getHealthDetails($status_id);

if (!$healthDetails) {
    echo "<p>No health status found with the provided status ID.</p>";
    exit();
}

$healthDetails = $healthDetails[0];
// Fetch all batches for the select dropdown
$query = "SELECT batch_id, batch FROM birds WHERE user_id = :user_id";
$stmt = $con->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$batches = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch batches

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the form data
    $batch_id = $_POST['batch_id'];
    $no_illness = $_POST['no_illness'];
    $no_deaths = $_POST['no_deaths'];
    $description = $_POST['description'];

    // Update the health status in the database
    try {
        $query = "UPDATE health_status 
                  SET batch_id = :batch_id, no_illness = :no_illness, no_deaths = :no_deaths, description = :description 
                  WHERE status_id = :status_id";

        $stmt = $con->prepare($query);
        $stmt->bindParam(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->bindParam(':no_illness', $no_illness, PDO::PARAM_INT);
        $stmt->bindParam(':no_deaths', $no_deaths, PDO::PARAM_INT);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redirect after successful update
            header("Location: health_problems.php");
            ob_end_flush();
            exit();
        } else {
            $error_message = "Failed to update bird health status.";
        }
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        $error_message = "An unexpected error occurred. Please try again later.";
    }
}
?>

<main class="col-lg-10 col-md-9 col-sm-12 p-0 vh-100 overflow-auto">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="row justify-content-center align-items-center w-100 mt-3">
            <div class="col-lg-5 col-md-8 col-12 mb-3">
                <div class="card shadow-lg" style="max-width: 500px; margin: auto;">
                    <div class="card-header p-3 text-center" style="background-color: #6f42c1;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Update Birds Health Details</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #f8f9fa;">

                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form class="row g-2" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?status_id=' . urlencode($status_id)); ?>" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

                            <div class="col-12 mb-2">
                                <label for="batch_id" class="form-label">Batch:</label>
                                <select class="form-select" name="batch_id" id="batch_id" required>
                                    <option value="" disabled>Select Batch</option>
                                    <?php foreach ($batches as $batch) : ?>
                                        <option value="<?php echo htmlspecialchars($batch['batch_id']); ?>"
                                                <?php if (isset($healthDetails['batch_id']) && $healthDetails['batch_id'] == $batch['batch_id']) echo 'selected'; ?>>
                                                    <?php echo htmlspecialchars($batch['batch']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="no_illness" class="form-label">No of ill birds:</label>
                                <input class="form-control" type="number" name="no_illness" id="no_illness" required value="<?php echo htmlspecialchars($healthDetails['no_illness']); ?>">
                            </div>

                            <div class="col-12">
                                <label for="no_deaths" class="form-label">No of dead birds:</label>
                                <input class="form-control" type="number" name="no_deaths" id="no_deaths" required value="<?php echo htmlspecialchars($healthDetails['no_deaths']); ?>">
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description:</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($healthDetails['description']); ?></textarea>
                            </div>

                            <div class="col-12">
                                <input type="submit" class="btn btn-primary w-100" name="submit" value="Update">
                            </div>

                            <div class="col-12 mb-3">
                                <a href="health_problems.php" class="btn btn-danger w-100">Back</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$frame->last_part();
?>
