<?php
class CheckLogin
{
    public static function checkLoginAndRole($user_id, $role)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        //check user is logged in and check role
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $role) {
            header("Location: login.php");
            exit();
        }

        self::checkStatus($_SESSION['user_id'], $_SESSION['status']);
        
        $database = new Database();
        $db = $database->getConnection();

        //get the users
        $query = "SELECT * FROM user WHERE role = :role AND user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //check user is a admin or not
        if($_SESSION['role']=='admin'){
        self::checkAdmin($_SESSION['user_id'], $user['is_admin']);
        }

        //check for got a user
        if ($user) {
            return $user;
        } else {
            echo "<script type='text/javascript'>
            alert('No user found.');
            window.location.href = 'login.php';
            </script>";
            exit();
        }
    }

    public static function checkStatus($user_id, $status)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // check the status
        if ($status == 0) {
            header("Location: login.php?status=blocked");
            exit();
        }
    }
    
    public static function checkAdmin($user_id, $is_admin)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        //check admin or not
        if ($is_admin == 0) {
            
            header("Location: ../login.php?status=Unautherized login");
            exit();
        }
    }
}
?>
