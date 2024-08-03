<?php
require_once 'crud.php';

class BuyMedicine implements crud
{
    private $conn;
    private $table_name = "buy_medicine";

    private $buyMedicine_id;
    private $user_id;
    private $med_id;
    private $med_name;
    private $sup_id;
    private $sup_name;
    private $unit_price;
    private $quantity;
    private $total;
    private $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Getters
    public function getBuyMedicineId()
    {
        return $this->buyMedicine_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getMedId()
    {
        return $this->med_id;
    }

    public function getMedName()
    {
        return $this->med_name;
    }

    public function getSupId()
    {
        return $this->sup_id;
    }

    public function getSupName()
    {
        return $this->sup_name;
    }

    public function getUnitPrice()
    {
        return $this->unit_price;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    // Setters
    public function setBuyMedicineId($buyMedicine_id)
    {
        $this->buyMedicine_id = $buyMedicine_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setMedId($med_id)
    {
        $this->med_id = $med_id;
    }

    public function setMedName($med_name)
    {
        $this->med_name = $med_name;
    }

    public function setSupId($sup_id)
    {
        $this->sup_id = $sup_id;
    }

    public function setSupName($sup_name)
    {
        $this->sup_name = $sup_name;
    }

    public function setUnitPrice($unit_price)
    {
        $this->unit_price = $unit_price;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    // CRUD methods
    public function create($user_id)
    {
        // Check if the supplier ID exists in the supplier table
        $supQuery = "SELECT sup_id FROM supplier WHERE sup_id = :sup_id";
        $supStmt = $this->conn->prepare($supQuery);
        $supStmt->bindParam(':sup_id', $this->sup_id);
        $supStmt->execute();
        if ($supStmt->rowCount() == 0) {
            // Supplier ID does not exist
            echo "Error: Supplier ID does not exist. Sup_ID: " . $this->sup_id;
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " (user_id, med_id, med_name, sup_id, sup_name, unit_price, quantity, total, created_at) VALUES (:user_id, :med_id, :med_name, :sup_id, :sup_name, :unit_price, :quantity, :total, :created_at)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':med_id', $this->med_id);
        $stmt->bindParam(':med_name', $this->med_name);
        $stmt->bindParam(':sup_id', $this->sup_id);
        $stmt->bindParam(':sup_name', $this->sup_name);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':created_at', $this->created_at);

        // Debugging output for bound parameters
        echo "Inserting record with the following details: \n";
        echo "User ID: " . $this->user_id . "\n";
        echo "Med ID: " . $this->med_id . "\n";
        echo "Med Name: " . $this->med_name . "\n";
        echo "Sup ID: " . $this->sup_id . "\n";
        echo "Sup Name: " . $this->sup_name . "\n";
        echo "Unit Price: " . $this->unit_price . "\n";
        echo "Quantity: " . $this->quantity . "\n";
        echo "Total: " . $this->total . "\n";
        echo "Created At: " . $this->created_at . "\n";

        if ($stmt->execute()) {
            echo "Record inserted successfully.";
            return true;
        } else {
            echo "Error: Could not insert record. ";
            print_r($stmt->errorInfo());
            return false;
        }
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
        $query = "SELECT * FROM " . $this->table_name . " WHERE buyMedicine_id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->buyMedicine_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->buyMedicine_id = $row['buyMedicine_id'];
            $this->user_id = $row['user_id'];
            $this->med_id = $row['med_id'];
            $this->med_name = $row['med_name'];
            $this->sup_id = $row['sup_id'];
            $this->sup_name = $row['sup_name'];
            $this->unit_price = $row['unit_price'];
            $this->quantity = $row['quantity'];
            $this->total = $row['total'];
            $this->created_at = $row['created_at'];
        }

        return $row;
    }

    public function update($buyMedicine_id, $user_id)
    {
        $query = "UPDATE " . $this->table_name . " SET med_id = :med_id, med_name = :med_name, sup_id = :sup_id, sup_name = :sup_name, unit_price = :unit_price, quantity = :quantity, total = :total, created_at = :created_at WHERE buyMedicine_id = :buyMedicine_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':buyMedicine_id', $buyMedicine_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':med_id', $this->med_id);
        $stmt->bindParam(':med_name', $this->med_name);
        $stmt->bindParam(':sup_id', $this->sup_id);
        $stmt->bindParam(':sup_name', $this->sup_name);
        $stmt->bindParam(':unit_price', $this->unit_price);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':created_at', $this->created_at);

        return $stmt->execute();
    }

    public function delete($buyMedicine_id, $user_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE buyMedicine_id = :buyMedicine_id AND user_id= :user_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':buyMedicine_id', $buyMedicine_id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }
}
