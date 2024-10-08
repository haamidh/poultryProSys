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
    <div class="container">
        <div class="row my-5 mx-3 text-center">

            <div class="col-lg-12 col-md-12 col-12 mb-3 my-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title p-2 text-white mb-0"><strong style="font-size:25px;">REVIEWS</strong></h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                    <tr style="text-align:center;">
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Rate</th>
                                        <th scope="col" style="width:40%">Comment</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialnum = 0;
                                    foreach ($reviews as $review) {
                                        $serialnum++;
                                        ?>
                                        <tr>
                                            <th><?php echo $serialnum; ?></th>
                                            <td style="text-align: left;"><?php echo $review['user_id']; ?></td>
                                            <td><?php echo $review['rating']; ?></td>
                                            <td style="text-align: left;"><?php echo $review['comment']; ?></td> <!-- Corrected variable name -->
                                            <td><?php echo $review['created_at']; ?></td>
                                            <td style="text-align:center;">
                                                <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $review['review_id']; ?>)">Delete</button>
                                            </td>

                                        </tr>
                                    <?php } ?>
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
$adminframe->last_part();
?>

<script>
    function myFunction(review_id) {
        if (confirm("Are you sure you want to delete this review?")) {
            window.location.href = "delete_review.php?review_id=" + review_id; // Corrected parameter
        }
    }
</script>

