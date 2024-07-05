<?php

class Incomes
{
    private $conn;
    private $user_id;
    private $from_date;
    private $to_date;

    public function __construct($db, $user_id, $from_date = '', $to_date = '')
    {
        $this->conn = $db;
        $this->user_id = $user_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function getAllData()
    {
        $query = "SELECT * FROM orders WHERE farmer_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND ordered_date BETWEEN :from_date AND :to_date";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        if ($this->from_date && $this->to_date) {
            $stmt->bindParam(":from_date", $this->from_date);
            $stmt->bindParam(":to_date", $this->to_date);
        }

        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $all_data = [];
        foreach ($orders as $order) {
            $order['type'] = 'order';
            $order['date'] = $order['ordered_date'];
            $order['detail'] = "Sold " . $order['product_id'];
            $order['received_from'] = $order['cus_id'];
            $order['amount'] = $order['total_amount'];
            $all_data[] = $order;
        }

        usort($all_data, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $all_data;
    }

    public function getTotalAmount()
    {
        $all_data = $this->getAllData();
        $total_amount = 0;
        foreach ($all_data as $data) {
            $total_amount += $data['amount'];
        }
        return $total_amount;
    }
}
?>
