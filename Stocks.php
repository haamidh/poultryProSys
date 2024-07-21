<?php
class Stocks
{
    private $db;
    private $user_id;
    private $from_date;
    private $to_date;

    public function __construct($db, $user_id, $from_date = '', $to_date = '')
    {
        $this->db = $db;
        $this->user_id = $user_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function getBirdStockData()
    {
        $query = "SELECT * FROM birds WHERE user_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND date BETWEEN :from_date AND :to_date";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        if ($this->from_date && $this->to_date) {
            $stmt->bindParam(':from_date', $this->from_date);
            $stmt->bindParam(':to_date', $this->to_date);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$result) {
            $result['type'] = 'bird';
            $result['date'] = $result['date']; // Assuming 'date' column exists in 'birds' table
            $result['detail'] = "Stock of " . $result['bird_type'];
            $result['quantity'] = $result['quantity'];
            $result['amount'] = $result['total_cost'];
        }
        return $results;
    }

    public function getMedicineStockData()
    {
        $query = "SELECT * FROM buy_medicine WHERE user_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND created_at BETWEEN :from_date AND :to_date"; // Changed 'date' to 'created_at'
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        if ($this->from_date && $this->to_date) {
            $stmt->bindParam(':from_date', $this->from_date);
            $stmt->bindParam(':to_date', $this->to_date);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$result) {
            $result['type'] = 'medicine';
            $result['date'] = $result['created_at']; // Assuming 'created_at' column exists in 'buy_medicine' table
            $result['detail'] = "Stock of " . $result['med_name'];
            $result['quantity'] = $result['quantity'];
            $result['amount'] = $result['total'];
        }
        return $results;
    }

    public function getFeedStockData()
    {
        $query = "SELECT * FROM buy_feed WHERE user_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND created_at BETWEEN :from_date AND :to_date"; // Assuming 'created_at' column exists in 'buy_feed' table
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        if ($this->from_date && $this->to_date) {
            $stmt->bindParam(':from_date', $this->from_date);
            $stmt->bindParam(':to_date', $this->to_date);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$result) {
            $result['type'] = 'feed';
            $result['date'] = $result['created_at']; // Assuming 'created_at' column exists in 'buy_feed' table
            $result['detail'] = "Stock of " . $result['feed_name'];
            $result['quantity'] = $result['quantity'];
            $result['amount'] = $result['total'];
        }
        return $results;
    }

    public function getAllStockData()
    {
        $bird_stock_data = $this->getBirdStockData();
        $medicine_stock_data = $this->getMedicineStockData();
        $feed_stock_data = $this->getFeedStockData();

        $all_stock_data = array_merge($bird_stock_data, $medicine_stock_data, $feed_stock_data);

        // Sort data by date
        usort($all_stock_data, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $all_stock_data;
    }

    public function getAllStockDataCategorized()
    {
        return [
            'total_bird_stock' => array_sum(array_column($this->getBirdStockData(), 'quantity')),
            'total_medicine_stock' => array_sum(array_column($this->getMedicineStockData(), 'quantity')),
            'total_feed_stock' => array_sum(array_column($this->getFeedStockData(), 'quantity')),
            'total_bird_stock_amount' => array_sum(array_column($this->getBirdStockData(), 'amount')),
            'total_medicine_stock_amount' => array_sum(array_column($this->getMedicineStockData(), 'amount')),
            'total_feed_stock_amount' => array_sum(array_column($this->getFeedStockData(), 'amount')),
        ];
    }

    public function getTotalStockQuantity()
    {
        return array_sum(array_column($this->getAllStockData(), 'quantity'));
    }

    public function getTotalStockAmount()
    {
        return array_sum(array_column($this->getAllStockData(), 'amount'));
    }
}
