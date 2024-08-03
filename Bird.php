<?php
require_once 'crud.php';

class Bird implements crud
{
    private $conn;
    private $table_name = "birds";

    private $user_id;
    private $batch_id;
    private $sup_id;
    private $sup_name;
    private $bird_type;
    private $unit_price;
    private $quantity;
    private $total_cost;
    private $date;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //getters

    public function getUserId()
    {
        return $this->user_id;
    }



    public function getBatchId()
    {
        return $this->batch_id;
    }

    public function getSupId()
    {
        return $this->sup_id;
    }

    public function getSupName()
    {
        return $this->sup_name;
    }

    public function getBirdType()
    {
        return $this->bird_type;
    }

    public function getUnitPrice()
    {
        return $this->unit_price;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getTotalCost()
    {
        return $this->total_cost;
    }

    public function getDate()
    {
        return $this->date;
    }


    //setters

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setBatchId($batch_id)
    {
        $this->batch_id = $batch_id;
    }

    public function setSupId($sup_id)
    {
        $this->sup_id = $sup_id;
    }

    public function setSupName($sup_name)
    {
        $this->sup_name = $sup_name;
    }

    public function setBirdType($bird_type)
    {
        $this->bird_type = $bird_type;
    }

    public function setUnitPrice($unit_price)
    {
        $this->unit_price = $unit_price;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function setTotalCost($total_cost)
    {
        $this->total_cost = $total_cost;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }


    public function getNextBatchId($user_id)
    {
        $query = "SELECT MAX(batch_id) as max_batch_id FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['max_batch_id']) {
            $max_batch_id = $row['max_batch_id'];
            $number = intval(substr($max_batch_id, 2)) + 1;
        } else {
            $number = 1; // Start from 1 if no batch ID exists for the user
        }

        return 'B ' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function create($user_id)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, batch_id, sup_id, sup_name, bird_type, unit_price, quantity, total_cost, date) VALUES (:user_id, :batch_id, :sup_id, :sup_name, :bird_type, :unit_price, :quantity, :total_cost, :date)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':batch_id', $this->batch_id);
        $stmt->bindParam(':sup_id', $this->sup_id);
        $stmt->bindParam(':sup_name', $this->sup_name);
        $stmt->bindParam(':bird_type', $this->bird_type);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':total_cost', $this->total_cost);
        $stmt->bindParam(':date', $this->date);

        return $stmt->execute();
    }

    public function read($user_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function readOne()
    {
        $query = "SELECT batch_id, sup_id, sup_name, bird_type, unit_price, quantity, total_cost, date 
                  FROM " . $this->table_name . " 
                  WHERE batch_id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->batch_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->batch_id = $row['batch_id'];
            $this->sup_id = $row['sup_id'];
            $this->sup_name = $row['sup_name'];
            $this->bird_type = $row['bird_type'];
            $this->unit_price = $row['unit_price'];
            $this->quantity = $row['quantity'];
            $this->total_cost = $row['total_cost'];
            $this->date = $row['date'];
        }

        return $row;
    }


    // Method to update a batch of birds
    public function update($batch_id, $user_id)
    {
        $query = "UPDATE " . $this->table_name . " SET sup_id = :sup_id, sup_name = :sup_name, bird_type = :bird_type, unit_price = :unit_price, quantity = :quantity, total_cost = :total_cost, date = :date WHERE batch_id = :batch_id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->batch_id = htmlspecialchars(strip_tags($this->batch_id));
        $this->sup_id = htmlspecialchars(strip_tags($this->sup_id));
        $this->sup_name = htmlspecialchars(strip_tags($this->sup_name));
        $this->bird_type = htmlspecialchars(strip_tags($this->bird_type));
        $this->unit_price = htmlspecialchars(strip_tags($this->unit_price));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->total_cost = htmlspecialchars(strip_tags($this->total_cost));
        $this->date = htmlspecialchars(strip_tags($this->date));

        // Bind new values
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':batch_id', $this->batch_id);
        $stmt->bindParam(':sup_id', $this->sup_id);
        $stmt->bindParam(':sup_name', $this->sup_name);
        $stmt->bindParam(':bird_type', $this->bird_type);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':total_cost', $this->total_cost);
        $stmt->bindParam(':date', $this->date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }



    public function delete($batch_id, $user_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE batch_id = :batch_id AND user_id= :user_id";
        $stmt = $this->conn->prepare($query);

        // Bind the batch_id parameter
        $stmt->bindParam(':batch_id', $batch_id);
        $stmt->bindParam(':user_id', $user_id);

        // Execute the query
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
