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

try {
    // Fetch all batches for the dropdown
    $query = "SELECT batch_id, bird_type, quantity FROM birds WHERE user_id = :user_id";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch initial health status data for all batches
    $query = "SELECT SUM(b.quantity) as total_birds, SUM(h.no_illness) as total_illness, SUM(h.no_deaths) as total_deaths
              FROM birds b LEFT JOIN health_status h ON b.batch_id = h.batch_id
              WHERE b.user_id = :user_id";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $allBatches = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalGoodHealth = $allBatches['total_birds'] - ($allBatches['total_illness'] + $allBatches['total_deaths']);
    $totalIllness = $allBatches['total_illness'];
    $totalDeaths = $allBatches['total_deaths'];

    if (isset($_GET['batch_id'])) {
        $selected_batch_id = $_GET['batch_id'];
        $query = "SELECT b.quantity as total_birds, COALESCE(SUM(h.no_illness), 0) as total_illness, COALESCE(SUM(h.no_deaths), 0) as total_deaths
                  FROM birds b LEFT JOIN health_status h ON b.batch_id = h.batch_id
                  WHERE b.batch_id = :batch_id AND b.user_id = :user_id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':batch_id', $selected_batch_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $selectedBatch = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalGoodHealth = $selectedBatch['total_birds'] - ($selectedBatch['total_illness'] + $selectedBatch['total_deaths']);
        $totalIllness = $selectedBatch['total_illness'];
        $totalDeaths = $selectedBatch['total_deaths'];
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $batch_id = $_POST['batch_id'];
    $no_illness = $_POST['no_illness'];
    $no_deaths = $_POST['no_deaths'];
    $description = $_POST['description'];

    try {
        $query = $con->prepare('INSERT INTO health_status (user_id, batch_id, no_illness, no_deaths, description) VALUES (:user_id, :batch_id, :no_illness, :no_deaths, :description)');
        $query->bindParam(':user_id', $user_id);
        $query->bindParam(':batch_id', $batch_id);
        $query->bindParam(':no_illness', $no_illness);
        $query->bindParam(':no_deaths', $no_deaths);
        $query->bindParam(':description', $description);

        if ($query->execute()) {
            $success_message = "Bird health status added successfully.";
        } else {
            $error_message = "Failed to add bird health status.";
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Birds Health Details</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>

<body>
    <div class="container contentArea" style="margin-left: -30px; margin-right: 10px">
        <div class="row">
            <div class="col-md-6 mx-5 my-4" style="text-align: left; width: 350px;">
                <div class="card-header card text-white" style="background-color: #40826D;">
                    <h2 class="display-6 text-center" style="font-size: 30px; font-weight: 500;">Birds Health Details</h2>
                </div>

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

                <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <input type="hidden" class="form-control" value="<?php echo $user_id; ?>" name="user_id" id="user_id" readonly>

                    <div class="col-md-12">
                        <label for="batch_id" class="form-label">Batch ID:</label>
                        <select class="form-control" name="batch_id" id="batch_id" required onchange="location = this.value;">
                            <option value="">Select Batch</option>
                            <?php foreach ($batches as $batch) : ?>
                                <option value="?batch_id=<?php echo $batch['batch_id']; ?>" <?php if (isset($selected_batch_id) && $selected_batch_id == $batch['batch_id']) echo 'selected'; ?>><?php echo $batch['batch_id']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label for="no_illness" class="form-label">Illness:</label>
                        <input class="form-control" type="text" name="no_illness" id="no_illness" required>
                    </div>

                    <div class="col-md-12">
                        <label for="no_deaths" class="form-label">Deaths:</label>
                        <input class="form-control" type="text" name="no_deaths" id="no_deaths" required>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">Description:</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>

                    <div class="col-md-12" style="text-align: center;">
                        <button type="submit" class="btn btn-primary" name="add_status">Add</button>
                    </div>
                </form>
            </div>

            <div class="col-md-6" style="margin-right: 10px;">
                <canvas id="myChart" style="width:80%; max-width:400px; height:400px;"></canvas>
                <script>
                    var initialData = {
                        labels: ["Good Health", "Illness", "Death"],
                        datasets: [{
                            backgroundColor: ["#178a27", "#35a6d1", "#bd2117"],
                            data: [<?php echo $totalGoodHealth; ?>, <?php echo $totalIllness; ?>, <?php echo $totalDeaths; ?>]
                        }]
                    };

                    var chartOptions = {
                        title: {
                            display: true,
                            text: "Batch Health Status"
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    };

                    var myChart = new Chart("myChart", {
                        type: "pie",
                        data: initialData,
                        options: chartOptions
                    });
                </script>
            </div>
        </div>
    </div>
    <?php $frame->last_part(); ?>
</body>

</html>
