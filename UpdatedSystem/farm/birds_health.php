<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Validation.php';

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

$batchErr = $num1Err = $num2Err= "";
$errors = false;

try {
    // Fetch all batches for the dropdown
    $query = "SELECT batch_id, batch, bird_type, quantity FROM birds WHERE user_id = :user_id";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalGoodHealth = 0;
    $totalIllness = 0;
    $totalDeaths = 0;

    if (isset($_GET['batch_id'])) {
        $selected_batch_id = filter_input(INPUT_GET, 'batch_id', FILTER_VALIDATE_INT);

        if ($selected_batch_id !== false) {
            // Fetch the selected batch's health status
            $query = "SELECT 
                        b.quantity AS total_birds, 
                        COALESCE(SUM(h.no_illness), 0) AS total_illness, 
                        COALESCE(SUM(h.no_deaths), 0) AS total_deaths
                      FROM 
                        birds b 
                      LEFT JOIN 
                        health_status h 
                      ON 
                        b.batch_id = h.batch_id 
                      WHERE 
                        b.batch_id = :batch_id
                      GROUP BY 
                        b.quantity";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':batch_id', $selected_batch_id);
            $stmt->execute();
            $selectedBatch = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($selectedBatch) {
                $totalGoodHealth = $selectedBatch['total_birds'] - ($selectedBatch['total_illness'] + $selectedBatch['total_deaths']);
                $totalIllness = $selectedBatch['total_illness'];
                $totalDeaths = $selectedBatch['total_deaths'];
            }
        } else {
            $error_message = "Invalid batch ID.";
        }
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $error_message = "An unexpected error occurred. Please try again later.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $batch_id = $_POST['batch_id'];
    $no_illness = $_POST['no_illness'];
    $no_deaths = $_POST['no_deaths'];
    $description = $_POST['description'];


    if (empty($batch_id)) {
        $batchErr = "*Please select batch";
        $errors = true;
    }

    if (!Validation::validateNumberField($no_illness, $num1Err)) {
        $num1Err = "*Number of ill birds must be a valid number";
        $errors = true;
    }

    if (!Validation::validateNumberField($no_deaths, $num2Err)) {
        $num2Err = "*Number of dead birds must be a valid number";
        $errors = true;
    }


    if (!$errors) {

        try {
            $query = $con->prepare('INSERT INTO health_status (user_id, batch_id, no_illness, no_deaths, description) VALUES (:user_id, :batch_id, :no_illness, :no_deaths, :description)');
            $query->bindParam(':user_id', $user_id);
            $query->bindParam(':batch_id', $batch_id);
            $query->bindParam(':no_illness', $no_illness);
            $query->bindParam(':no_deaths', $no_deaths);
            $query->bindParam(':description', $description);

            if ($query->execute()) {
                echo "<script>window.location.href = 'birds_health.php?batch_id=$batch_id';</script>";
                exit();
            } else {
                $error_message = "Failed to add bird health status.";
            }
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            $error_message = "An unexpected error occurred. Please try again later.";
        }
    }
}
?>
<main class="col-lg-10 col-md-9 col-sm-12 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-4 mx-2 py-4">

            <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <div class="card shadow-lg">
                    <div class="card-header text-center" style="background-color: #6f42c1;">
                        <h5 class="card-title text-white"><strong>Birds Health Details</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #f8f9fa;">

                        <?php if (isset($success_message)) : ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form class="row g-2" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

                            <div class="col-12">
                                <label for="batch_id" class="form-label">Batch:</label>
                                <select class="form-select" name="batch_id" id="batch_id" required onchange="location = 'birds_health.php?batch_id=' + encodeURIComponent(this.value);">
                                    <option value="" disabled selected>Select Batch</option>
                                    <?php foreach ($batches as $batch) : ?>
                                        <option value="<?php echo htmlspecialchars($batch['batch_id']); ?>"
                                            <?php if (isset($selected_batch_id) && $selected_batch_id == $batch['batch_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($batch['batch']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-danger"><?php echo $batchErr ?></small>
                            </div>

                            <div class="col-12">
                                <label for="no_illness" class="form-label">No of ill birds:</label>
                                <input class="form-control" type="number" name="no_illness" id="no_illness" required>
                                <small class="text-danger"><?php echo $num1Err ?></small>
                            </div>

                            <div class="col-12">
                                <label for="no_deaths" class="form-label">No of dead birds:</label>
                                <input class="form-control" type="number" name="no_deaths" id="no_deaths" required>
                                <small class="text-danger"><?php echo $num2Err ?></small>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description:</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>

                            <div class="col-12 mb-1">
                                <input type="submit" class="btn btn-primary w-100" name="submit" value="Add">
                            </div>

                            <div class="col-12 mb-3">
                                <a href="health_problems.php" class="btn btn-danger w-100">Back</a>
                            </div>


                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <canvas id="birdHealthChart" style="width:100%;max-width:600px;"></canvas>
            </div>

        </div>

    </div>
</main>

<script>
    var totalGoodHealth = <?php echo $totalGoodHealth; ?>;
    var totalIllness = <?php echo $totalIllness; ?>;
    var totalDeaths = <?php echo $totalDeaths; ?>;

    var ctx = document.getElementById('birdHealthChart').getContext('2d');

    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Healthy', 'Ill', 'Dead'],
            datasets: [{
                label: 'Bird Health Status',
                data: [totalGoodHealth, totalIllness, totalDeaths],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });
</script>