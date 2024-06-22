<?php
class CheckLogin
{

    public static function checkLoginAndRole($user_id, $role)
    {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }


        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $role) {
            header("Location: login.php");
            exit();
        }


        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT * FROM user WHERE role = :role AND user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($user) {
            return $user;

            exit();
        } else {
?>
            <script type="text/javascript">
                alert("No user found");
                window.location.href = 'login.php';
            </script>
<?php
            exit();
        }
    }
}
