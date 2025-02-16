<?php

class miscellaneous {
    
    private $conn;
    private $table_name = "miscellaneous_category";
    private $user_id;   
    private $category_id;
    private $category_name;
    private $category_description;
    
    function __construct($conn) {
        $this->conn = $conn;
        
    }
    function getUser_id() {
        return $this->user_id;
    }

    function getCategory_id() {
        return $this->category_id;
    }

    function getCategory_name() {
        return $this->category_name;
    }

    function getCategory_description() {
        return $this->category_description;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    function setCategory_id($category_id) {
        $this->category_id = $category_id;
    }

    function setCategory_name($category_name) {
        $this->category_name = $category_name;
    }

    function setCategory_description($category_description) {
        $this->category_description = $category_description;
    }

    //Function to get all from miscellaneous
   public function create($user_id) {
$query="INSERT INTO " . $this->table_name . " (user_id, category_name,category_description) VALUES (:user_id, :category_name, :category_description)";
$stmt = $this->conn->prepare($query);

// Sanitize input
$this->category_name = htmlspecialchars(strip_tags($this->category_name));
$this->category_description = htmlspecialchars(strip_tags($this->category_description));

$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':category_name', $this->category_name);
$stmt->bindParam(':category_description', $this->category_description);

return $stmt->execute();
}

//Function to get all from miscellaneous
public function read($user_id)
{
    $query = "SELECT * FROM " . $this->table_name . " WHERE user_id= :user_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function miscellaneousExists($user_id)
{
    //to remove spaces and make it case-insensitive
    $query = "SELECT category_id FROM " . $this->table_name . " 
          WHERE LOWER(REPLACE(category_name, ' ', '')) = LOWER(REPLACE(:category_name, ' ', '')) 
          AND user_id = :user_id 
          LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':category_name', $this->category_name);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}

public function readExpense($user_id)
{
    $query = "SELECT * FROM miscellaneous WHERE user_id= :user_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
   }


