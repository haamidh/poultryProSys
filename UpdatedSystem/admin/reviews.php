<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';
require_once '../classes/Review.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Database connection
$database = new Database();
$db = $database->getConnection();

$review = new Review($db);

// Call the readFeedback method to get feedbacks
$reviews = $review->fetchIndividualReviews();

// Check admin access using CheckLogin class
$user_id = $_SESSION['user_id'];
$admin = CheckLogin::checkLoginAndRole($user_id, 'admin');

// Create AdminFrame object
$adminframe = new AdminFrame();
$adminframe->first_part($admin);
?>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container my-5">
        <div class="row text-center">
            <div class="col-lg-12 col-md-12 col-12 mb-3">
                <div class="card border-light shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="text-white mb-0" style="font-size: 28px; font-weight: 600;">Reviews</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Rate</th>
                                        <th scope="col" style="width:40%">Comment</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialnum = 0;
                                    foreach ($reviews as $review) {
                                        $serialnum++;
                                        ?>
                                        <tr>
                                            <th class="text-center"><?php echo $serialnum; ?></th>
                                            <td class="text-left"><?php echo htmlspecialchars($review['user_id']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($review['rating']); ?></td>
                                            <td class="text-left"><?php echo htmlspecialchars($review['comment']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars(date("d M Y", strtotime($review['created_at']))); ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $review['review_id']; ?>)">Delete</button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php if (empty($reviews)) { ?>
                                <div class="text-center my-3">
                                    <strong>No reviews available.</strong>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$adminframe->last_part();
?>

<script>
    function myFunction(review_id) {
        if (confirm("Are you sure you want to delete this review?")) {
            window.location.href = "delete_review.php?review_id=" + review_id;
        }
    }
</script>

<style>
    /* Custom styles */
    body {
        background: linear-gradient(135deg, #e2e2e2, #ffffff);
        font-family: 'Poppins', sans-serif;
    }

    .card {
        border-radius: 10px;
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .btn {
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }

    .btn-danger:hover {
        background-color: #c82333; /* Darker red for hover effect */
    }

    .table th, .table td {
        vertical-align: middle;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .table thead {
            display: none;
        }
        .table, .table tbody, .table tr, .table td {
            display: block;
            width: 100%;
        }
        .table tr {
            margin-bottom: 15px;
        }
        .table td {
            text-align: left;
            position: relative;
            padding-left: 50%;
        }
        .table td::before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            width: 45%;
            padding-left: 10px;
            font-weight: bold;
            text-align: left;
        }
    }
</style>
