<?php

session_start();

require 'classes/Review.php';
require 'classes/config.php';

// Create a database connection
$database = new Database();
$db = $database->getConnection();

// Create a new Review object
$review = new Review($db);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_review'], $_POST['rating_data'])) {

    if (isset($_SESSION['user_id'])) {
        // Set the values using setters
        $review->setUserId($_SESSION['user_id']);
        $review->setUserReview(trim($_POST['user_review']));
        $review->setRatingData((int) $_POST['rating_data']);

        // Insert the review using the object
        if ($review->insertReview()) {
            // Success message
            echo "<script>alert('Review submitted successfully.');</script>";
            echo "<script>setTimeout(function() {
                window.location.href = 'review.php';
            }, 1000);</script>";
        } else {
            // Failure message
            echo "<script>alert('Failed to submit your review. Please try again later.');</script>";
        }
    } else {
        // Not logged in message
        echo "<script>alert('You must be logged in to submitreview.');</script>";
        echo "<script>setTimeout(function() {
                window.location.href = 'review.php';
            }, 1000);</script>";
    }
}
?>
