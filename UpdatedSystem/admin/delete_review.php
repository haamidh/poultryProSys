<?php

session_start();

include '../classes/config.php';
include '../classes/Review.php';

if (isset($_GET['review_id'])) {
    $review_id = $_GET['review_id'];

    $database = new Database();
    $db = $database->getConnection();

    $review = new Review($db);

    // Call the deleteReview method to delete the feedback
    if ($review->deleteReview($review_id)) {
        header("Location: reviews.php");
        exit();
    } else {
        echo "Failed to delete feedback.";
    }
} else {
    echo "Invalid request.";
}
?>
