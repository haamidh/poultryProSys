<?php

class Supplier {

    private $conn;
    private $table_name = "supplier";
    private $sup_id;    
    private $user_id;
    private $sup_name;
    private $address;
    private $mobile;
    private $city;
    private $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    //Setter
    public function setSup_id($id) {
        $this->sup_id = $id;
    }

    public function setSup_name($name) {
        $this->sup_name = $name;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function setMobile($mobile) {
        $this->mobile = $mobile;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function setEmail($email) {
        $this->email = $email;
    }
    
    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    
    //Getters
    public function getSup_id() {
        return $this->sup_id;
    }

    public function getSup_name() {
        return $this->sup_name;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getCity() {
        return $this->city;
    }

    public function getEmail() {
        return $this->email;
    }
    
    function getUser_id() {
        return $this->user_id;
    }

    
    //Function to create supplier
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (sup_name, user_id, address, mobile, city, email) VALUES (:sup_name, :user_id, :address, :mobile, :city, :email)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':sup_name', $this->sup_name);        
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':mobile', $this->mobile);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':email', $this->email);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update($sup_id) {
        $query = "UPDATE " . $this->table_name . " SET 
                  sup_name = :sup_name, 
                  address = :address, 
                  mobile = :mobile, 
                  city = :city, 
                  email = :email
                  WHERE sup_id = :sup_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':sup_name', $this->sup_name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':mobile', $this->mobile);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':sup_id', $sup_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($sup_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE sup_id = :sup_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sup_id', $sup_id);
        return $stmt->execute();
    }

    //Function to get one from supplier
    public function readOne($sup_id) {
        $query = "SELECT sup_name, address, mobile, city, email FROM " . $this->table_name . " WHERE sup_id = :sup_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sup_id', $sup_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Function to read all suppliers
    public function readAll($user_id) {
        $query = "SELECT sup_id, sup_name, address, mobile, city, email FROM " . $this->table_name . " WHERE user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function supplierExists($user_id) {
        //to remove spaces and make it case-insensitive
        $query = "SELECT sup_id FROM " . $this->table_name . " 
              WHERE LOWER(REPLACE(sup_name, ' ', '')) = LOWER(REPLACE(:sup_name, ' ', '')) 
              AND user_id = :user_id 
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sup_name', $this->sup_name);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function supplierEmailExists($user_id) {

        $query = "SELECT sup_id FROM " . $this->table_name . " WHERE email = :email AND user_id = :user_id  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

}

?>
