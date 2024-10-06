<?php
// Include the Review class
require 'classes/Review.php';
require 'classes/config.php';


$database = new Database();
$db = $database->getConnection();
$review = new Review($db);

// Fetch reviews and ratings data
$data = $review->fetchReviews();
$review_data = $review->fetchIndividualReviews();


// Calculate percentages
$total_reviews = $data['total_review'] > 0 ? $data['total_review'] : 1; // Prevent division by zero
$data['5_star_percent'] = ($data['5_star_review'] / $total_reviews) * 100;
$data['4_star_percent'] = ($data['4_star_review'] / $total_reviews) * 100;
$data['3_star_percent'] = ($data['3_star_review'] / $total_reviews) * 100;
$data['2_star_percent'] = ($data['2_star_review'] / $total_reviews) * 100;
$data['1_star_percent'] = ($data['1_star_review'] / $total_reviews) * 100;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rating System</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="footer.css">
        <link rel="stylesheet" href="header.css">
        <style>
            .star-light { color: #e0e0e0; }
            .text-warning { color: #FFC107 !important; }
            .progress-label-left { float: left; }
            .progress { clear: both; }
            .review { margin-top: 20px; }
        </style>
    </head>
    <body>
        <?php include 'includes/header.php'; ?>
        <div class="container mt-5">
            <div class="card">
                <div class="card-header text-center" style="background-color: #40826D;color: white;"><h4>Reviews</h4></div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-sm-4 text-center">
                            <h2 class="text-warning mb-4">
                                <b><span id="average_rating"><?= round($data['average_rating'], 1) ?></span> / 5</b>
                            </h2>
                            <div class="mb-3" style="font-size:24px;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= round($data['average_rating']) ? '-fill' : '' ?> star-fill text-warning mr-1"></i>
                                <?php endfor; ?>
                            </div>
                            <h3><span id="total_review"><?= $data['total_review'] ?></span> Reviews</h3>
                        </div>
                        <div class="col-sm-4">
                            <!-- Progress Bars for Ratings -->
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <p>
                                <div class="progress-label-left"><b><?= $i ?></b> <i class="bi bi-star-fill text-warning"></i></div>

                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="<?= round($data["{$i}_star_percent"], 2) ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= round($data["{$i}_star_percent"], 2) ?>%;"></div>
                                </div>
                                </p>
                            <?php endfor; ?>
                        </div>
                        <div class="col-sm-4 text-center">
                            <h3 class="mt-4 mb-3">Write Review Here</h3>
                            <button type="button" name="add_review" id="add_review" class="btn btn-primary">Review</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="review_modal" class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Submit Review</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="review_form" action="submit_review.php" method="POST">
                                <h4 class="text-center mt-2 mb-4">
                                    <i class="bi bi-star star-light submit_star mr-1" data-rating="1"></i>
                                    <i class="bi bi-star star-light submit_star mr-1" data-rating="2"></i>
                                    <i class="bi bi-star star-light submit_star mr-1" data-rating="3"></i>
                                    <i class="bi bi-star star-light submit_star mr-1" data-rating="4"></i>
                                    <i class="bi bi-star star-light submit_star mr-1" data-rating="5"></i>
                                </h4>
                                <div class="form-group">
                                    <textarea class="form-control" id="user_review" name="user_review" rows="5" placeholder="Write your review..."></textarea>
                                    <input type="hidden" name="rating_data" id="rating_data" value="0">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" id="submit_review">Submit Review</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="review">
                <h3>Reviews</h3>
                <div id="review_list">
                    <?php foreach ($review_data as $r_data): ?>
                        <div class="border p-3 mb-3">
                            <p><strong><?= $review->findUserName($r_data['user_id']) ?></strong> <span class="text-muted">on <?= date('F j, Y', strtotime($r_data['created_at'])) ?></span></p>
                            <p><?= $r_data['comment'] ?></p>
                            <p class="text-warning">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= $r_data['rating'] ? '-fill' : '' ?> star-fill"></i>
                                <?php endfor; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#add_review').click(function () {
                    $('#review_modal').modal('show');
                });

                $('.submit_star').click(function () {
                    var rating = $(this).data('rating');
                    $('.submit_star').removeClass('text-warning');
                    for (var i = 1; i <= rating; i++) {
                        $('#review_modal .submit_star[data-rating="' + i + '"]').addClass('text-warning');
                    }
                    $('#rating_data').val(rating);
                });
            });
        </script>
    </body>
</html>

