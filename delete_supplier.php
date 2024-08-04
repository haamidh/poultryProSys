<?php

if(session_status()== PHP_SESSION_NONE){
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'crud.php';


if(!isset($_SESSION['user_id'])){
    header('Location: login.php?msg=Please Login before Proceeding');
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');



class DeleteSupplier implements crud {
    public function delete($user_id,$sup_id){
        global $con;
        $sup_id = $_GET['sup_id'];
        $query = $con->prepare('DELETE FROM supplier WHERE user_id = :user_id AND sup_id = :sup_id');
        $query->bindParam(':user_id', $user_id);
        $query->bindParam(':sup_id', $sup_id);
        $query->execute();
    }
    public function create($user_id){
        throw new Exception("Cannot be used here");
    }
    public function read($user_id){
        throw new Exception("Cannot be used here");
    }
    public function update($user_id, $id){
        throw new Exception("Cannot be used here");
    }
    public function readOne(){
        throw new Exception("Cannot be used here");
    }

}
if(isset($_GET['sup_id'])){
    $sup_id = $_GET['sup_id'];
    $deleter = new DeleteSupplier();
    $deleter->delete($user_id, $sup_id);
    header('Location:supplier.php?msg=Supplier Deleted Successfully');
    exit();
}