<?php
class Expenses {
    private $db;
    private $user_id;
    private $from_date;
    private $to_date;

    public function __construct($db, $user_id, $from_date = '', $to_date = '') {
        $this->db = $db;
        $this->user_id = $user_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    public function getBirdData() {
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
            $result['date'] = $result['date'];
            $result['detail'] = "Import " . $result['bird_type'];
            $result['paid_to'] = $result['sup_name'];
            $result['amount'] = $result['total_cost'];
        }
        return $results;
    }

    public function getMedicineData() {
        $query = "SELECT * FROM buy_medicine WHERE user_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND created_at BETWEEN :from_date AND :to_date";
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
            $result['date'] = $result['created_at'];
            $result['detail'] = "Import " . $result['med_name'];
            $result['paid_to'] = $result['sup_name'];
            $result['amount'] = $result['total'];
        }
        return $results;
    }

    public function getFeedData() {
        $query = "SELECT * FROM buy_feed WHERE user_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND created_at BETWEEN :from_date AND :to_date";
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
            $result['date'] = $result['created_at'];
            $result['detail'] = "Import " . $result['feed_name'];
            $result['paid_to'] = $result['sup_name'];
            $result['amount'] = $result['total'];
        }
        return $results;
    }

    public function getMiscData() {
        $query = "SELECT * FROM miscellaneous WHERE user_id = :user_id";
        if ($this->from_date && $this->to_date) {
            $query .= " AND created_at BETWEEN :from_date AND :to_date";
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
            $result['type'] = 'miscellaneous';
            $result['date'] = $result['created_at'];
            $result['detail'] = "Import " . $result['misc_name'];
            $result['paid_to'] = $result['sup_name'];
            $result['amount'] = $result['total'];
        }
        return $results;
    }

    public function getAllData() {
        $all_data = array_merge(
            $this->getBirdData(),
            $this->getMedicineData(),
            $this->getFeedData(),
            $this->getMiscData()
        );

        usort($all_data, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $all_data;
    }

    public function getTotalAmount() {
        $total_amount = 0;
        $all_data = $this->getAllData();

        foreach ($all_data as $data) {
            $total_amount += $data['amount'];
        }

        return $total_amount;
    }
}
?>
