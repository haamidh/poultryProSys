    <?php

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
    private $date;
   
    
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

    function getdate() {
        return $this->date;
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
//public function setdate($date) {
   // $this->date = $date;  // Correct the property name to match the database field
//}

    function setdate($date) {
        $this->date = $date;
    }

    function getUser_id() {
        return $this->user_id;
    }

    function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    // public function insert() {
    //     try {
    //         $query = "INSERT INTO " . $this->table_name . " 
    //         (user_id, category_id, expense_name, handled_by, expense_amount, misc_description, payment_method, created_at)
    //         VALUES (:user_id, :category_id, :expense_name, :handled_by, :expense_amount, :misc_description, :payment_method, :created_at)";

    //         $stmt = $this->conn->prepare($query);

    //         $stmt->bindParam(':user_id', $this->user_id);
    //         $stmt->bindParam(':category_id', $this->category_id);
    //         $stmt->bindParam(':expense_name', $this->expense_name);
    //         $stmt->bindParam(':handled_by', $this->handled_by);
    //         $stmt->bindParam(':expense_amount', $this->expense_amount);
    //         $stmt->bindParam(':misc_description', $this->misc_description);
    //         $stmt->bindParam(':payment_method', $this->payment_method);
    //         $stmt->bindParam(':created_at', $this->created_at);

    //         if ($stmt->execute()) {
    //             return true;
    //         } else {
    //             throw new Exception("Error inserting record");
    //         }
    //     } catch (Exception $e) {
    //         error_log("Insert Error: " . $e->getMessage()); // Log error for debugging
    //         return false;
    //     }
    // }

    // // Update an existing miscellaneous expense
    // public function update($id) {
    //     try {
    //         $query = "UPDATE " . $this->table_name . " 
    //         SET category_id = :category_id, expense_name = :expense_name, handled_by = :handled_by, 
    //         expense_amount = :expense_amount, misc_description = :misc_description, payment_method = :payment_method, created_at = :created_at 
    //         WHERE id = :id AND user_id = :user_id";

    //         $stmt = $this->conn->prepare($query);

    //         // Bind values
    //         $stmt->bindParam(':category_id', $this->category_id);
    //         $stmt->bindParam(':expense_name', $this->expense_name);
    //         $stmt->bindParam(':handled_by', $this->handled_by);
    //         $stmt->bindParam(':expense_amount', $this->expense_amount);
    //         $stmt->bindParam(':misc_description', $this->misc_description);
    //         $stmt->bindParam(':payment_method', $this->payment_method);
    //         $stmt->bindParam(':created_at', $this->created_at);
    //         $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    //         $stmt->bindParam(':user_id', $this->user_id);

    //         if ($stmt->execute()) {
    //             return true;
    //         } else {
    //             throw new Exception("Error updating record");
    //         }
    //     } catch (Exception $e) {
    //         error_log("Update Error: " . $e->getMessage()); // Log error for debugging
    //         return false;
    //     }
    // }

    // // Delete a miscellaneous expense
    // public function delete($id) {
    //     try {
    //         $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
    //         $stmt = $this->conn->prepare($query);

    //         $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    //         $stmt->bindParam(':user_id', $this->user_id);

    //         if ($stmt->execute()) {
    //             return true;
    //         } else {
    //             throw new Exception("Error deleting record");
    //         }
    //     } catch (Exception $e) {
    //         error_log("Delete Error: " . $e->getMessage()); // Log error for debugging
    //         return false;
    //     }
    // }

    // Retrieve expenses for a specific user
    
    //Function to get all from miscellenous expenses
    public function read($user_id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $clean_user_id = htmlspecialchars(strip_tags($user_id)); // Sanitize first
            $stmt->bindParam(':user_id', $clean_user_id, PDO::PARAM_INT); // Then bind the sanitized variable
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Read Error: " . $e->getMessage()); // Log error for debugging
            return [];
        }
    }       
    
    
    
    public function create($user_id) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, category_id, expense_name, handled_by, expense_amount, misc_description, payment_method, date)
                  VALUES (:user_id, :category_id, :expense_name, :handled_by, :expense_amount, :misc_description, :payment_method, :date)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input 
        $this->Category_id     = htmlspecialchars(strip_tags($this->category_id));
        $this->expense_name     = htmlspecialchars(strip_tags($this->expense_name));
        $this->handled_by       = htmlspecialchars(strip_tags($this->handled_by));
        $this->expense_amount   = htmlspecialchars(strip_tags($this->expense_amount));
        $this->misc_description = htmlspecialchars(strip_tags($this->misc_description));
        $this->payment_method   = htmlspecialchars(strip_tags($this->payment_method));
        $this->date             = htmlspecialchars(strip_tags($this->date));
        
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindParam(':expense_name', $this->expense_name);
        $stmt->bindParam(':handled_by', $this->handled_by);
        $stmt->bindParam(':expense_amount', $this->expense_amount);
        $stmt->bindParam(':misc_description', $this->misc_description);
        $stmt->bindParam(':payment_method', $this->payment_method);
        $stmt->bindParam(':date', $this->date);  
        
        return $stmt->execute();
    }
    
    

    public function miscellaneousExpensesExists($user_id)
{
    //to remove spaces and make it case-insensitive
    $query = "SELECT expense_id FROM " . $this->table_name . " 
          WHERE LOWER(REPLACE(expense_name, ' ', '')) = LOWER(REPLACE(:expense_name, ' ', '')) 
          AND user_id = :user_id 
          LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':expense_name', $this->expense_name);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    return $stmt->rowCount() > 0;


}

public function delete($category_id) {
    $query = "DELETE FROM " . $this->table_name . " WHERE category_id= :category_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':category_id', $category_id);
    return $stmt->execute();
}
   }

?>

