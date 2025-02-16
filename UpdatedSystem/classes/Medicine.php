<?php

require_once 'crud.php';

class Medicine implements crud
{

    private $conn;
    private $table_name = "medicine";
    private $user_id;
    private $med_name;
    private $least_quantity;
    private $description;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Getters
    function getUser_id()
    {
        return $this->user_id;
    }

    function getMed_name()
    {
        return $this->med_name;
    }
    function getLeastQuantity()
    {
        return $this->least_quantity;
    }

    function getDescription()
    {
        return $this->description;
    }

    // Setters
    function setUser_id($user_id)
    {
        $this->user_id = $user_id;
    }

    function setMed_name($med_name)
    {
        $this->med_name = $med_name;
    }
    function setLeastQuantity($least_quantity)
    {
        $this->least_quantity = $least_quantity;
    }

    function setDescription($description)
    {
        $this->description = $description;
    }

    //Function to create medicine
    public function create($user_id)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, med_name,least_quantity, description) VALUES (:user_id, :med_name,:least_quantity, :description)";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->med_name = htmlspecialchars(strip_tags($this->med_name));
        $this->least_quantity = htmlspecialchars(strip_tags($this->least_quantity));
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':med_name', $this->med_name);
        $stmt->bindParam(':least_quantity', $this->least_quantity);
        $stmt->bindParam(':description', $this->description);

        return $stmt->execute();
    }

    //Function to get all from medicine
    public function read($user_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Function to get one from buy feed
    public function readOne($med_id)
    {
        $query = "SELECT med_name,least_quantity, description FROM " . $this->table_name . " WHERE med_id = :med_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':med_id', $med_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->med_name = $row['med_name'];
            $this->least_quantity = $row['least_quantity'];
            $this->description = $row['description'];
        }

        return $row;
    }

    public function update($feed_id)
    {
        $query = "UPDATE " . $this->table_name . " SET med_name = :med_name, least_quantity=:least_quantity, description = :description WHERE med_id = :med_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->med_name = htmlspecialchars(strip_tags($this->med_name));
        $this->least_quantity = htmlspecialchars(strip_tags($this->least_quantity));
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(':med_id', $feed_id);
        $stmt->bindParam(':med_name', $this->med_name);
        $stmt->bindParam(':least_quantity', $this->least_quantity);
        $stmt->bindParam(':description', $this->description);

        return $stmt->execute();
    }

    public function delete($med_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE med_id = :med_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':med_id', $med_id);

        return $stmt->execute();
    }

    //Function to get medname
    public function getMedName($med_id)
    {
        $query = "SELECT med_name FROM " . $this->table_name . " WHERE med_id = :med_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':med_id', $med_id);

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check result
            if ($result) {
                return $result['med_name'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getAllSuppliers($user_id)
    {
        $query = "SELECT sup_id,sup_name FROM supplier WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calculateStock($med_id, $usage)
    {
        $buyMedi = new BuyMedicine($this->conn);
        $purchases = $buyMedi->getStockSortedByDate($med_id);

        $remaining_stock = [];
        $total_stock_value = 0;
        $available_stock = 0;

        //get remaining stock array
        foreach ($purchases as $purchase) {
            $remaining_stock[] = [
                'buyMed_id' => $purchase['buyMedicine_id'],
                'unit_price' => $purchase['unit_price'],
                'quantity' => $purchase['quantity'],
                'remaining' => $purchase['quantity']
            ];
        }

        foreach ($usage as $used) {
            $remaining_quantity = $used['quantity'];
            foreach ($remaining_stock as &$stock) {
                if ($remaining_quantity <= 0) {
                    break;
                }

                if ($stock['remaining'] > 0) {
                    if ($stock['remaining'] >= $remaining_quantity) {
                        $stock['remaining'] -= $remaining_quantity;
                        $remaining_quantity = 0;
                    } else {
                        $remaining_quantity -= $stock['remaining'];
                        $stock['remaining'] = 0;
                    }
                }
            }
        }

        // Calculate total remaining stock
        for ($i = 0; $i < count($remaining_stock); $i++) {
            if ($remaining_stock[$i]['remaining'] > 0) {
                $total_stock_value += $remaining_stock[$i]['remaining'] * $remaining_stock[$i]['unit_price'];
                $available_stock += $remaining_stock[$i]['remaining'];
            }
        }


        return [
            'available_stock' => $available_stock,
            'stock_value' => $total_stock_value
        ];
    }

    public function medicineExists($user_id)
    {
        //remove spaces and make it case-insensitive
        $query = "SELECT med_id FROM " . $this->table_name . " 
              WHERE LOWER(REPLACE(med_name, ' ', '')) = LOWER(REPLACE(:med_name, ' ', '')) 
              AND user_id = :user_id 
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':med_name', $this->med_name);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
