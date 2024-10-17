    <?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of miscellaneous
 *
 * @author user
 */
class MisExpenses {
    private $conn;
    private $table_name = "miscellaneous";
    private $user_id;
    private $category_id;
    private $expense_name;
    private $handled_by;
    private $expense_amount;
    private $misc_description;
    private $payment_method;
    private $created_at;
   
    
    function __construct($conn) {
        $this->conn = $conn;
    }
   
    
    function getCategory_id() {
        return $this->category_id;
    }

    function getExpense_name() {
        return $this->expense_name;
    }

    function getHandled_by() {
        return $this->handled_by;
    }

    function getExpense_amount() {
        return $this->expense_amount;
    }

    function getMisc_description() {
        return $this->misc_description;
    }

    function getPayment_method() {
        return $this->payment_method;
    }

    function getCreated_at() {
        return $this->created_at;
    }

    function setCategory_id($category_id) {
        $this->category_id = $category_id;
    }

    function setExpense_name($expense_name) {
        $this->expense_name = $expense_name;
    }

    function setHandled_by($handled_by) {
        $this->handled_by = $handled_by;
    }

    function setExpense_amount($expense_amount) {
        $this->expense_amount = $expense_amount;
    }

    function setMisc_description($misc_description) {
        $this->misc_description = $misc_description;
    }

    function setPayment_method($payment_method) {
        $this->payment_method = $payment_method;
    }

    function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

    function getUser_id() {
        return $this->user_id;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }


    public function read($user_id)
{
    $query = "SELECT * FROM " . $this->table_name . " WHERE user_id= :user_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
