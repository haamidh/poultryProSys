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



class DeleteMedicine implements crud {
    public function delete($user_id,$med_id){
        global $con;
        $med_id = $_GET['med_id'];
        $query = $con->prepare('DELETE FROM medicine WHERE user_id = :user_id AND med_id = :med_id');
        $query->bindParam(':user_id', $user_id);
        $query->bindParam(':med_id', $med_id);
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
if(isset($_GET['med_id'])){
    $med_id = $_GET['med_id'];
    $deleter = new DeleteMedicine();
    $deleter->delete($user_id, $med_id);
    header('Location:medicine.php?msg=Medicine Deleted Successfully');
    exit();
}