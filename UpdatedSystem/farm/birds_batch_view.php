<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once 'frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Bird.php';
require_once '../classes/Product.php';

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

// Retrieve the user ID and batch ID from the session and query parameters
$user_id = $_SESSION["user_id"];
$batch_id = isset($_GET['batch_id']) ? $_GET['batch_id'] : '';

if (empty($batch_id)) {
    header("Location: birds_batch.php");
    exit();
}

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);

$database = new Database();
$db = $database->getConnection();

$bird = new Bird($db);
$pro = new Product($db);

$batch = $bird->getBatchDetails($batch_id, $user_id);
$healthStatus = $bird->getHealthStatus($batch_id);
$productDetails = $bird->getProductDetails($batch_id);

if ($batch) {
    $supplierName = Bird::getSupplier($batch['sup_id'], $db);

    // Calculate batch age in days
    $importDate = new DateTime($batch['date']);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($importDate);
    $batchAgeDays = $interval->days + (int) $batch['age'];

    // Initialize totals for product details
    $totalQuantity = 0;
    $totalValue = 0.0;

    $totalIllness = 0;
    $totalDeaths = 0;

    // Calculate total product details
    if ($productDetails) {
        foreach ($productDetails as $product) {
            $totalQuantity += $product['quantity'];
            $totalValue += (float) $product['total'];
        }
    }

    if ($healthStatus) {
        foreach ($healthStatus as $status) {
            $totalIllness += $status['no_illness'];
            $totalDeaths += $status['no_deaths'];
        }
    }
    $total_birds = $batch['quantity'];
    $production_birds = $bird->getProductionBirds($batch_id);
    $available_birds = $total_birds - ($production_birds + $totalIllness + $totalDeaths);

    // Calculate Gross Profit
    $grossProfit = $totalValue - (float) $batch['total_cost'];
    ?>

    <main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
        <div class="container my-5">
            <div class="row">
                <div class="card shadow-lg border-0">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title text-white mb-0"><strong>Batch Summary</strong></h5>
                        <a href="birds.php" class="btn btn-danger py-1 px-4">Back</a>
                    </div>
                    <div class="card-body" style="background-color: #EFFFFB;">

                        <div class="row">
                            <?php
                            // Displaying batch details
                            $details = [
                                'Batch' => htmlspecialchars($batch['batch']),
                                'Import Date' => htmlspecialchars($batch['date']),
                                'Age For Today' => htmlspecialchars($batchAgeDays) . " Days",
                                'Supplier' => htmlspecialchars($supplierName),
                                'Import Type' => htmlspecialchars($batch['bird_type']),
                                'Available Birds' => htmlspecialchars($available_birds),
                                'Unit Price' => "Rs. " . number_format((float) $batch['unit_price'], 2, '.', ','),
                                'Quantity' => htmlspecialchars($batch['quantity']),
                                'Total Cost' => "Rs. " . number_format((float) $batch['total_cost'], 2, '.', ','),
                                'Gross Profit' => "Rs. " . number_format((float) $grossProfit, 2, '.', ',')
                            ];
                            foreach ($details as $title => $value):
                                ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-title"><?php echo $title; ?></h6>
                                            <p class="card-text"><?php echo $value; ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <h4 class="mt-4">Health Status</h4>
                            <?php if ($healthStatus): ?>
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th><i class="fas fa-calendar-alt"></i> Date</th>
                                            <th>Illness</th>
                                            <th>Deaths</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($healthStatus as $status): ?>
                                            <tr class="table-row-hover">
                                                <td><?php echo htmlspecialchars($status['created_at']); ?></td>
                                                <td><?php echo htmlspecialchars($status['no_illness']); ?></td>
                                                <td><?php echo htmlspecialchars($status['no_deaths']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-dark">
                                        <tr>
                                            <td><strong>Total:</strong></td>
                                            <td><strong><?php echo $totalIllness; ?></strong></td>
                                            <td><strong><?php echo $totalDeaths; ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-warning">No health status records available.</div>
                            <?php endif; ?>

                            <h4 class="mt-4">Product Details</h4>
                            <?php if ($productDetails): ?>
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr class="text-center">
                                            <th><i class="fas fa-calendar-alt"></i> Date</th>
                                            <th>Product ID</th>
                                            <th>Used Birds</th>
                                            <th>Quantity</th>
                                            <th><i class="fas fa-dollar-sign"></i> Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productDetails as $product): ?>
                                            <tr class="text-center table-row-hover">
                                                <td><?php echo htmlspecialchars($product['created_at']); ?></td>
                                                <td><?php echo htmlspecialchars($pro->getProductName($product['product_id'])); ?></td>
                                                <td><?php echo htmlspecialchars($product['no_birds']); ?></td>
                                                <td><?php echo number_format((float) $product['quantity'], 2, '.', ',') . " " . htmlspecialchars($pro->getProductUnit($product['product_id'])); ?></td>
                                                <td>Rs. <?php echo number_format((float) $product['total'], 2, '.', ','); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-dark">
                                        <tr>
                                            <td colspan="2" class="text-center"><strong>Total:</strong></td>
                                            <td class="text-center"><?php echo $production_birds; ?></td>
                                            <td class="text-center"><strong><?php echo number_format($totalQuantity, 2, '.', ','); ?> Kg</strong></td>
                                            <td class="text-center"><strong>Rs. <?php echo number_format($totalValue, 2, '.', ','); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-warning">No product details available.</div>
                            <?php endif; ?>
                        </div>

                        <!-- Chart Section -->
                        <div class="mt-5 mb-5">
                            <h4>Health Trend Chart</h4>
                            <canvas id="healthTrendChart" width="400" height="200"></canvas>
                        </div>
                        

                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Prepare data for health trend chart (unchanged)
        const healthData = {
            labels: [<?php
                        foreach ($healthStatus as $status)
                            echo '"' . htmlspecialchars($status['created_at']) . '",';
                        ?>],
            datasets: [{
                    label: 'Illness',
                    data: [<?php foreach ($healthStatus as $status)
                            echo htmlspecialchars($status['no_illness']) . ',';
                        ?>],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                }, {
                    label: 'Deaths',
                    data: [<?php foreach ($healthStatus as $status)
                            echo htmlspecialchars($status['no_deaths']) . ',';
                        ?>],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                }
            ]
        };

        const healthTrendConfig = {
            type: 'line',
            data: healthData,
        };

        const healthTrendChart = new Chart(
            document.getElementById('healthTrendChart'),
            healthTrendConfig
        );

    </script>

<?php
} else {
    echo '<div class="container mt-5"><div class="alert alert-danger">Batch not found.</div></div>';
}
$frame->last_part();
?>
