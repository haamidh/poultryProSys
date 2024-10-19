<?php

require_once 'Product.php';
$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

class Incomes {

    private $conn;
    private $user_id;
    private $from_date;
    private $to_date;
    private $product;

    // Pass the $product object through the constructor
    public function __construct($db, $user_id, $product, $from_date = '', $to_date = '') {
        $this->conn = $db;
        $this->user_id = $user_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->product = $product;
    }

    public function getOrderData() {
        $query = "SELECT * FROM orders WHERE farm_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND ordered_date BETWEEN :from_date AND :to_date";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        if ($this->from_date && $this->to_date) {
            $stmt->bindParam(':from_date', $this->from_date);
            $stmt->bindParam(':to_date', $this->to_date);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Use the $this->product to get the product name
        foreach ($results as &$result) {
            $result['type'] = 'order';
            $result['date'] = $result['ordered_date'];
            $result['detail'] = "Sold " . $this->product->getProductName($result['product_id']);
            $result['received_from'] = $result['cus_id'];
            $result['amount'] = $result['total'];
        }

        return $results;
    }

    public function getAllData() {
        $order_data = $this->getOrderData();
        $all_data = $order_data;

        // Sort data by date
        usort($all_data, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $all_data;
    }

    public function getTotalAmount() {
        $all_data = $this->getAllData();
        $total_amount = array_sum(array_column($all_data, 'amount'));
        return $total_amount;
    }

    public function getDailyIncome() {
        // Convert `to_date` to include one extra day
        $adjusted_to_date = date('Y-m-d', strtotime($this->to_date . ' +1 day'));

        // Calculate the date 30 days before the adjusted `to_date`
        $start_date = date('Y-m-d', strtotime($adjusted_to_date . ' -30 days'));

        $query = "SELECT DATE(ordered_date) AS day, SUM(total) AS total
              FROM orders
              WHERE farm_id = :user_id 
              AND status = :status 
              AND ordered_date >= :start_date 
              AND ordered_date < :to_date
              GROUP BY DATE(ordered_date)
              ORDER BY DATE(ordered_date)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindValue(':status', 1);
        $stmt->bindParam(':start_date', $start_date); // Use the calculated start date
        $stmt->bindParam(':to_date', $adjusted_to_date); // Use the adjusted `to_date`
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyIncome() {
        $query = "SELECT DATE_FORMAT(ordered_date, '%Y-%m') AS month, SUM(total) AS total
              FROM orders
              WHERE farm_id = :user_id
              AND ordered_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
              GROUP BY DATE_FORMAT(ordered_date, '%Y-%m')
              ORDER BY DATE_FORMAT(ordered_date, '%Y-%m')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
