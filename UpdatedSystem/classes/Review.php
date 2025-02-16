<?php

class Review {

    private $conn;
    private $user_id;
    private $rating_data;
    private $user_review;

    public function __construct($db) {
        $this->conn = $db;
    }

    //Setters
    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setRatingData($rating_data) {
        $this->rating_data = $rating_data;
    }

    public function setUserReview($user_review) {
        $this->user_review = $user_review;
    }

    //Getters
    public function getUserId() {
        return $this->user_id;
    }

    public function getRatingData() {
        return $this->rating_data;
    }

    public function getUserReview() {
        return $this->user_review;
    }

    public function insertReview() {
        $sql = "INSERT INTO reviews (user_id, rating, comment, created_at) 
                VALUES (:user_id, :rating_data, :user_review, NOW())";
        $stmt = $this->conn->prepare($sql);

        $user_id = $this->getUserId();
        $rating_data = $this->getRatingData();
        $user_review = $this->getUserReview();

        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':rating_data', $rating_data);
        $stmt->bindParam(':user_review', $user_review);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function fetchReviews() {
        $sql = "SELECT AVG(rating) as average_rating, COUNT(*) as total_review, 
                   SUM(rating = 5) as 5_star_review, 
                   SUM(rating = 4) as 4_star_review, 
                   SUM(rating = 3) as 3_star_review, 
                   SUM(rating = 2) as 2_star_review, 
                   SUM(rating = 1) as 1_star_review 
            FROM reviews";

        $stmt = $this->conn->query($sql);
        if ($stmt) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $data['average_rating'] = isset($data['average_rating']) ? $data['average_rating'] : 0;
            $data['total_review'] = isset($data['total_review']) ? $data['total_review'] : 0;
            $data['5_star_review'] = isset($data['5_star_review']) ? $data['5_star_review'] : 0;
            $data['4_star_review'] = isset($data['4_star_review']) ? $data['4_star_review'] : 0;
            $data['3_star_review'] = isset($data['3_star_review']) ? $data['3_star_review'] : 0;
            $data['2_star_review'] = isset($data['2_star_review']) ? $data['2_star_review'] : 0;
            $data['1_star_review'] = isset($data['1_star_review']) ? $data['1_star_review'] : 0;
        } else {
            die("Query failed: " . $this->conn->errorInfo());
        }

        return $data;
    }

    public function fetchIndividualReviews() {
        $query = "SELECT * FROM reviews ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findUserName($user_id) {
        $query = "SELECT username FROM user WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $user_id);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['username'] ?? null;
    }

    public function deleteReview($review_id) {
        $query = "DELETE FROM reviews WHERE review_id = :review_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':review_id', $review_id);

        if ($stmt->execute()) {
            return true;
        } else {
            print_r($stmt->errorInfo());
            return false;
        }
    }

}

?>
