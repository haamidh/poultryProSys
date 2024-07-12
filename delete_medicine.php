<?php

if(session_status()== PHP_SESSION_NONE){
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';
require_once 'crud.php';

if(!isset($_SESSION['user_id'])){
    header('Location: login.php?msg=Please Login before Proceeding');
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);



// class Delete_medicine implements crud {
//     public function delete($con, $user_id,$id){
        
//         $sql = "DELETE FROM medicines WHERE id = ? AND user_id = ?";
//         $stmt = $con->prepare($sql);
//         $stmt->bind_param("ii", $id, $user_id);
//         $stmt->execute();
//         $stmt->close();
        
//     }
// }