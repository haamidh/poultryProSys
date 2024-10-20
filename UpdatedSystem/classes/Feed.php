<?php

require_once 'crud.php';

class Feed implements crud
{

    private $conn;
    private $table_name = "feed";
    private $user_id;
    private $feed_name;
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

    function getFeed_name()
    {
        return $this->feed_name;
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

    function setFeed_name($feed_name)
    {
        $this->feed_name = $feed_name;
    }
    function setLeastQuantity($least_quantity)
    {
        $this->least_quantity = $least_quantity;
    }

    function setDescription($description)
    {
        $this->description = $description;
    }

    //Function to create new feed
    public function create($user_id)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, feed_name,least_quantity, description) VALUES (:user_id, :feed_name,:least_quantity, :description)";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->feed_name = htmlspecialchars(strip_tags($this->feed_name));
        $this->least_quantity = htmlspecialchars(strip_tags($this->least_quantity));
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':feed_name', $this->feed_name);
        $stmt->bindParam(':least_quantity', $this->least_quantity);
        $stmt->bindParam(':description', $this->description);

        return $stmt->execute();
    }

    //Function to get all from feed
    public function read($user_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Function to get one raw from feed
    public function readOne($feed_id)
    {
        $query = "SELECT feed_name,least_quantity, description FROM " . $this->table_name . " WHERE feed_id = :feed_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':feed_id', $feed_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->feed_name = $row['feed_name'];
            $this->least_quantity = $row['least_quantity'];
            $this->description = $row['description'];
        }

        return $row;
    }

    public function update($feed_id)
    {
        $query = "UPDATE " . $this->table_name . " SET feed_name = :feed_name,least_quantity=:least_quantity, description = :description WHERE feed_id = :feed_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->feed_name = htmlspecialchars(strip_tags($this->feed_name));
        $this->least_quantity = htmlspecialchars(strip_tags($this->least_quantity));
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(':feed_id', $feed_id);
        $stmt->bindParam(':feed_name', $this->feed_name);
        $stmt->bindParam(':least_quantity', $this->least_quantity);
        $stmt->bindParam(':description', $this->description);

        return $stmt->execute();
    }

    public function delete($feed_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE feed_id = :feed_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':feed_id', $feed_id);

        return $stmt->execute();
    }

    // Get the name of a feed by ID
    public function getFeedName($feed_id)
    {
        $query = "SELECT feed_name FROM " . $this->table_name . " WHERE feed_id = :feed_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':feed_id', $feed_id);

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check result
            if ($result) {
                return $result['feed_name'];
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

    public function calculateStock($feed_id, $usage)
    {
        $buyFeed = new BuyFeed($this->conn);
        $purchases = $buyFeed->getStockSortedByDate($feed_id);

        $remaining_stock = [];
        $total_stock_value = 0;
        $available_stock = 0;

        // get remaining stock
        foreach ($purchases as $purchase) {
            $remaining_stock[] = [
                'buyfeed_id' => $purchase['buyfeed_id'],
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

        //Calculate total remaining stock
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

    public function feedExists($user_id)
    {
        //remove spaces and make it case-insensitive
        $query = "SELECT feed_id FROM " . $this->table_name . " 
              WHERE LOWER(REPLACE(feed_name, ' ', '')) = LOWER(REPLACE(:feed_name, ' ', '')) 
              AND user_id = :user_id 
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':feed_name', $this->feed_name);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
