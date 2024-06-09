<?php
session_start();
require 'config.php';
require 'User.php';

$database = new Database();
$db = $database->getConnection();

$user=new User($db);
echo "Update these soon";

?>

<!DOCTYPE html>
<html>
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PoultryPro:Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" ></script>
        <style>
            .navbar-nav .nav-item a {
                font-weight: bold;
            }
        </style>
    </head>
    <body class="bg-dark">
        <div class="container">
    <div class="row">
  <div class="col-sm-6 mb-3 mb-sm-0">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Medicine</h5>
        <p class="card-text">Visit the medicines page to add, delete and add medicines</p>
        <a href="medicine.php?user_id=<?php echo $_SESSION['user_id'] ?>" class="btn btn-primary">Visit Medicines Page</a>
      </div>
    </div>
  </div>
  </div>
  </div>
    </body>
</html>