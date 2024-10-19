<?php
class CheckLogin
{
    public static function checkLoginAndRole($user_id, $role)
    {
        // Ensure session is started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verify if user is logged in and role matches
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $role) {
            header("Location: login.php");
            exit();
        }

        // CheckStatus and checkAdmin
        self::checkStatus($_SESSION['user_id'], $_SESSION['status']);
        
        // Verify user existence in database and role
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT * FROM user WHERE role = :role AND user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($_SESSION['role']=='admin'){
        self::checkAdmin($_SESSION['user_id'], $user['is_admin']);
        }

        if ($user) {
            return $user;
        } else {
            // Redirect to login with an error message
            echo "<script type='text/javascript'>
            alert('No user found.');
            window.location.href = 'login.php';
            </script>";
            exit();
        }
    }

    public static function checkStatus($user_id, $status)
    {
        // Ensure session is started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verify the status
        if ($status == 0) {
            // User is blocked
            header("Location: login.php?status=blocked");
            exit();
        }
    }
    
    public static function checkAdmin($user_id, $is_admin)
    {
        // Ensure session is started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verify the admin
        if ($is_admin == 0) {
            
            header("Location: ../login.php?status=Unautherized login");
            exit();
        }
    }
}
?>
