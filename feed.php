<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please Login before Proceeding");
    exit();
}
$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
</head>
    <body class="bg-light">
     <div class="container">
        <div class="raw mt-1 ">
        <div class="col-lg-4 bg-light m-auto">
        <h1 class="text-center pt-2  ">Feed Details</h1>
        <form> 
          <div class="input-group mb-3" >
                <lable> User ID</lable>          
               <input type="text" class="form-control" id="formUserID" placeholder="User Id" >
          </div>
          <div class="input-group mb-3" >
               <input type="text" class="form-control" id="formFeedID" placeholder="Feed Id" >
          </div>
          <div class="input-group mb-5" >              
               <input type="text" class="form-control" id="formFeedName" placeholder="Feed Name">
          </div>
          <div class="d-grid mb-3">
              <button type="button" class="btn btn-success">Add feed</button>
          </div>
          <div class="d-grid mb-3">
              <button type="button" class="btn btn-success">Buy feed</button>
          </div>
          <div class="d-grid mb-3">
              <button type="button" class="btn btn-success">Use feed</button>
          </div>
      
    </form>
    </div>
</div>
<div>
    </body>
</html>
