<?php

class Order {

    private $conn;
    private $table_name = "orders";
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
    private $order_num;

    public function __construct($db) {
        $this->conn = $db;
    }

    //Setters
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

    public function setOrder_num($order_num){
        $this->order_num = $order_num;
    }

    //Getters
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

    public function getOrder_num(){
        return $this->order_num;
    }

    //function to create a new order
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (order_num, cus_id, farm_id, product_id, quantity, unit_price, total, status) 
                  VALUES (:order_num, :cus_id, :farm_id, :product_id, :quantity, :unit_price, :total, :status)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':order_num',$this->order_num);
        $stmt->bindParam(':cus_id', $this->cus_id);
        $stmt->bindParam(':farm_id', $this->farm_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':status', $this->status);
        

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    //Function to get all from orders
    public function read($farm_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE farm_id = :farm_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':farm_id', $farm_id);
        $stmt->execute();
        $all_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sort by date
        usort($all_orders, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $all_orders;
    }

    //Function to get one from orders
    public function readOne($order_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($order_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET cus_id = :cus_id, farm_id = :farm_id, product_id = :product_id, 
                      quantity = :quantity, unit_price = :unit_price, total = :total, 
                      status = :status, ordered_date = :ordered_date 
                  WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':cus_id', $this->cus_id);
        $stmt->bindParam(':farm_id', $this->farm_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':ordered_date', $this->ordered_date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($order_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    //function to get today oders
    public function todayOrders($date, $farm_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE ordered_date = :ordered_date AND farm_id=:farm_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordered_date', $date);
        $stmt->bindParam(':farm_id', $farm_id);

        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['count'];
        }
        return 0; 
    }

    public function getCustomer($cus_id) {
        $query = "SELECT username, address,city FROM user WHERE user_id = :cus_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':cus_id', $cus_id);

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check result
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
        $query = "SELECT product_name FROM products WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':product_id', $product_id);

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

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
        $query = "SELECT order_id, service_fee, payment_date 
              FROM order_payments 
              WHERE (:from_date IS NULL OR payment_date >= :from_date)
              AND (:to_date IS NULL OR payment_date <= :to_date)
              ORDER BY payment_date DESC"; 

        $stmt = $this->conn->prepare($query);

        $from_date = !empty($from_date) ? $from_date : null;
        $to_date = !empty($to_date) ? $to_date : null;

        $stmt->bindParam(':from_date', $from_date);
        $stmt->bindParam(':to_date', $to_date);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getDailyServiceFees($to_date) {

        $start_date = date('Y-m-d', strtotime($to_date . ' -30 days'));

        //service fees per day
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
        // service fees per month
        $query = "SELECT DATE_FORMAT(payment_date, '%Y-%m') AS month, SUM(service_fee) AS total_service_fees
              FROM order_payments
              WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
              GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
              ORDER BY DATE_FORMAT(payment_date, '%Y-%m')";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    function generateOrderNum($con, $farm_id)
    {
        //get the last order number for the specified farm
        $query = $con->prepare("SELECT order_num FROM " . $this->table_name . " WHERE farm_id = ? ORDER BY order_num DESC LIMIT 1");
    
        //Check query failed
        if (!$query) {
            die("Running Query failed: " . $con->errorInfo()[2]);
        }
    
        $query->bindParam(1, $farm_id, PDO::PARAM_INT);
        $query->execute();
        
        $row = $query->fetch(PDO::FETCH_ASSOC);
    
        //If no previous order number, return a new one
        if (!$row) {
            return $farm_id . '0001';
        } else {
            $lastId = $row['order_num'];
    
            $numSuffix = intval(substr($lastId, -4));
    
            $updatedId = sprintf('%04d', $numSuffix + 1);
    
            return $farm_id . $updatedId;
        }
    }

    function getCustomerOrders($user_id){
        $query = "SELECT * FROM " . $this->table_name . " WHERE cus_id = :cus_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cus_id', $user_id);
        $stmt->execute();
        $all_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sort by date
        usort($all_orders, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $all_orders;
    }

    function addNewManualOrder($user_id){
        
    }
    
}

?>
