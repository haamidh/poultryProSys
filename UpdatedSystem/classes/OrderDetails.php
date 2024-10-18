<?php
class OrderDetails {

    private $conn;
    private $table_name = "order_details";
    // Properties
    private $order_num;
    private $first_name;
    private $last_name;
    private $email;
    private $phone_number;
    private $address;
    private $city;


    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    public function getOrder_num() {
        return $this->order_num;
    }

    public function getFirst_name() {
        return $this->first_name;
    }

    public function getLast_name() {
        return $this->last_name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPhone_number() {
        return $this->phone_number;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getCity() {
        return $this->city;
    }

    public function setOrder_num($order_num){
        $this->order_num = $order_num;
    }

    public function setFirst_name($first_name){
        $this->first_name = $first_name;
    }
    public function setLast_name($last_name){
        $this->last_name = $last_name;
    }
    public function setEmail($email){
        $this->email = $email;
    }
    public function setPhone_number($phone_number){
        $this->order_num = $phone_number;
    }
    public function setAddress($address){
        $this->address = $address;
    }
    public function setCity($city){
        $this->city = $city;
    }

    public function create(){
        $query = "INSERT INTO " . $this->table_name . " 
                  (order_num, first_name, last_name, email, phone_number, address, city) 
                  VALUES (:order_num, :first_name, :last_name, :email, :phone_number, :address, :city)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        
        $stmt->bindParam(':order_num', $this->order_num);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone_number', $this->phone_number);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':city', $this->city);
        

        // Execute the query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


}

?>