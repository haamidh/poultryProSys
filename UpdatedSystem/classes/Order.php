<?php

class Order {

    private $conn;
    private $table_name = "orders";
    // Properties
    private $order_id;
    private $cus_id;
    private $farm_id;
    private $product_id;
    private $quantity;
    private $unit_price;
    private $total;
    private $status;
    private $ordered_date;
    private $created_at;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Setter methods
    public function setOrder_id($order_id) {
        $this->order_id = $order_id;
    }

    public function setCus_id($cus_id) {
        $this->cus_id = $cus_id;
    }

    public function setFarm_id($farm_id) {
        $this->farm_id = $farm_id;
    }

    public function setProduct_id($product_id) {
        $this->product_id = $product_id;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function setUnit_price($unit_price) {
        $this->unit_price = $unit_price;
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setOrdered_date($ordered_date) {
        $this->ordered_date = $ordered_date;
    }

    public function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

    // Getter methods
    public function getOrder_id() {
        return $this->order_id;
    }

    public function getCus_id() {
        return $this->cus_id;
    }

    public function getFarm_id() {
        return $this->farm_id;
    }

    public function getProduct_id() {
        return $this->product_id;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getUnit_price() {
        return $this->unit_price;
    }

    public function getTotal() {
        return $this->total;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getOrdered_date() {
        return $this->ordered_date;
    }

    public function getCreated_at() {
        return $this->created_at;
    }

    // Method to create a new order
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (cus_id, farm_id, product_id, quantity, unit_price, total, status) 
                  VALUES (:cus_id, :farm_id, :product_id, :quantity, :unit_price, :total, :status)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        
        $stmt->bindParam(':cus_id', $this->cus_id);
        $stmt->bindParam(':farm_id', $this->farm_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':status', $this->status);
        

        // Execute the query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Method to read all orders
    public function read($farm_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE farm_id = :farm_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':farm_id', $farm_id);
        $stmt->execute();
        $all_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sort data by date
        usort($all_orders, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $all_orders;
    }

    // Method to read a single order
    public function readOne($order_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to update an order
    public function update($order_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET cus_id = :cus_id, farm_id = :farm_id, product_id = :product_id, 
                      quantity = :quantity, unit_price = :unit_price, total = :total, 
                      status = :status, ordered_date = :ordered_date 
                  WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':cus_id', $this->cus_id);
        $stmt->bindParam(':farm_id', $this->farm_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':ordered_date', $this->ordered_date);

        // Execute the query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Method to delete an order
    public function delete($order_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);

        // Execute the query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function todayOrders($date, $farm_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE ordered_date = :ordered_date AND farm_id=:farm_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordered_date', $date);
        $stmt->bindParam(':farm_id', $farm_id);

        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['count'];
        }
        return 0; // return 0 if the query fails
    }

    public function getCustomer($cus_id) {
        // Prepare the SQL query
        $query = "SELECT username, address,city FROM user WHERE user_id = :cus_id";
        $stmt = $this->conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':cus_id', $cus_id);

        // Execute the query
        if ($stmt->execute()) {
            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if a result was returned
            if ($result) {
                return $result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getProduct($product_id) {
        // Prepare the SQL query
        $query = "SELECT product_name FROM products WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':product_id', $product_id);

        // Execute the query
        if ($stmt->execute()) {
            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if a result was returned
            if ($result) {
                return $result['product_name'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getAllServiceFees($from_date, $to_date) {
        // SQL query that handles both date filters being optional
        $query = "SELECT order_id, service_fee, payment_date 
              FROM order_payments 
              WHERE (:from_date IS NULL OR payment_date >= :from_date)
              AND (:to_date IS NULL OR payment_date <= :to_date)
              ORDER BY payment_date DESC"; // Specify the column for ordering

        $stmt = $this->conn->prepare($query);

        // Handle NULL dates when not provided
        $from_date = !empty($from_date) ? $from_date : null;
        $to_date = !empty($to_date) ? $to_date : null;

        // Bind date parameters, using NULL if not provided
        $stmt->bindParam(':from_date', $from_date);
        $stmt->bindParam(':to_date', $to_date);

        // Execute the query
        $stmt->execute();

        // Fetch results
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getDailyServiceFees($to_date) {

        $start_date = date('Y-m-d', strtotime($to_date . ' -30 days'));

        // SQL query to calculate service fees per day
        $query = "SELECT DATE(payment_date) AS day, SUM(service_fee) AS total_service_fees
              FROM order_payments
              WHERE payment_date >= :from_date 
              AND payment_date < :to_date
              GROUP BY DATE(payment_date)
              ORDER BY DATE(payment_date)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':from_date', $start_date);
        $stmt->bindParam(':to_date', $to_date);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyServiceFees($to_date) {
        // SQL query to calculate service fees per month
        $query = "SELECT DATE_FORMAT(payment_date, '%Y-%m') AS month, SUM(service_fee) AS total_service_fees
              FROM order_payments
              WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
              GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
              ORDER BY DATE_FORMAT(payment_date, '%Y-%m')";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
