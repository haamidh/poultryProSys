<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'crud.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?msg=Please Login before Proceeding');
    exit();
}

$user_id = $_SESSION['user_id'];

$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');



class DeleteFeed implements crud
{
    public function delete($user_id, $med_id)
    {
        global $con;
        $feed_id = isset($_GET['feed_id']) ? $_GET['feed_id'] : '';
        $query = $con->prepare('DELETE FROM feed WHERE user_id = :user_id AND feed_id = :feed_id');
        $query->bindParam(':user_id', $user_id);
        $query->bindParam(':feed_id', $feed_id);
        $query->execute();
    }
    public function create($user_id)
    {
        throw new Exception("Cannot be used here");
    }
    public function read($user_id)
    {
        throw new Exception("Cannot be used here");
    }
    public function update($user_id, $id)
    {
        throw new Exception("Cannot be used here");
    }
    public function readOne()
    {
        throw new Exception("Cannot be used here");
    }
}
if (isset($_GET['feed_id'])) {
    $feed_id = $_GET['feed_id'];
    $deleter = new DeleteFeed();
    $deleter->delete($user_id, $feed_id);
    header('Location:feed.php?msg=feed Deleted Successfully');
    exit();
}