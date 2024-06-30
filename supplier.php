<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();

}
require_once 'config.php';
require_once 'checkLogin.php';
require_once 'frame.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$database=new Database();
$con=$database->getConnection();
$farm=CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

if($_SERVER['REQUEST_METHOD']=='POST'){
    
    $sup_name=$_POST['sup_name'];
    $address=$_POST['address'];
    $city=$_POST['city'];
    $mobile=$_POST['mobile'];
    $email=$_POST['email'];
    $sup_id=$_POST['sup_id'];
    $sup_id=getLastSupplierId($con,$user_id);
if($con==false){
    die("Error Establishing Connection :".$con->errorInfo());
}
addNewSupplier($con, $user_id, $sup_id, $sup_name ,$address, $city,$mobile,$email );

}

function getLastSupplierId($con,$user_id){
    $query=$con->prepare("SELECT sup_id From supplier WHERE user_id=? ORDER BY sup_id DESC LIMIT 1");
   if(!$query){
    die("Running Query failed: " . $con->errorInfo()[2]);
   }
   $query->bindParam(1, $user_id, PDO::PARAM_INT);
   $query->execute();
   $row = $query->fetch(PDO::FETCH_ASSOC);

   if (!$row) {
    return 'S0001';
} else {
    $lastId = $row['sup_id'];
    $numSuffix = intval(substr($lastId, 1));
    $updatedId = sprintf('%04d', $numSuffix + 1);
    return 'S' . $updatedId;
}
}

function addNewSupplier($con, $user_id,$sup_id,$sup_name,$address, $city,$mobile,$email){
    $query=$con->prepare('INSERT INTO supplier(user_id,sup_id,sup_name,address,city,mobile,email)VALUES(:user_id,:sup_id,:sup_name,:address,:city,:mobile,:email)');
    $query->bindParam(':user_id', $user_id);
    $query->bindParam(':sup_id', $sup_id);
    $query->bindParam(':sup_name', $sup_name);
    $query->bindParam(':address', $address);
    $query->bindParam(':city', $city);
    $query->bindParam(':mobile', $mobile);
    $query->bindParam(':email', $email);

    return $query->execute();  
    
  

}
function getAllSupplier($con,$user_id) {


    $query=$con->prepare('SELECT * FROM supplier WHERE user_id=:user_id');
    $query->bindParam(':user_id', $user_id);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
} 
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>supplier form</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="container contentArea">
          <div class="col float-left">
            <div class="card-header card text-white bg-success bg-gradient mb-3">
                <h2 class="display-6 text-center">Supplier Details</h2>
            </div>
        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="col-md-6">
                <label for="user_id" class="form-label">User Id:</label>
                <input type="text" class="form-control"  value="<?php echo $user_id;?>"  name="user_id" id="user_id"readonly>
              </div>
             <div class="col-md-6">
                <label for="sup_id" class="form-label">Supplier id:</label>
                <input type="text" class="form-control" value="<?php echo getLastSupplierId($con, $user_id); ?>" id="sup_id" name="sup_id">
              </div>  
              <div class="col-md-12">
              <label for="sup_name" class="form-label">Supplier Name:</label>
              <input type="text" class="form-control" id="sup_name" name="sup_name" required>
            </div>
            <div class="col-md-12">
                <label for="address" class="form-label">Address:</label>
                <input type="text" class="form-control" id="address"placeholder="1234 Main St" name="address" required>
              </div>

              <div class="col-md-6">
                <label for="city" class="form-label">City:</label>
                <input type="text" class="form-control" id="city" name="city" required>
              </div>

            <div class="col-6">
                <label for="mobile" class="form-label">Mobile:</label>
                <input type="text" class="form-control" id="mobile"  name="mobile" required>
              </div>

              <div class="col-md-6">
                <label for="email" class="form-label">Email:</label>
                <input type="text" class="form-control" id="email" name="email" required>
              </div>
             <div class="col-12">
              <button type="submit" class="btn btn-primary" name="add_supplier" value="Add supplier">Submit</button>
            </div>
          </form> 
          </div>
          </div>
          <div>
          <div class="container containerArea">
            <div class="conatiner float-left">
              <div class="col">
                <br>
                <table class="table table-striped">
                  <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Supplier ID</th>
                        <th scope="col">Supplier Name</th>
                        <th scope="col">mobile</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                  <?php
                  $serialnum = 0;
                  $suppliers = getAllSupplier($con, $user_id);
                  foreach ($suppliers as $supplier) {
                      $serialnum++;
                  ?>
                      <tr>
                          <th><?php echo $serialnum; ?></th>
                          <td><?php echo $supplier['sup_id']; ?></td>
                          <td><?php echo $supplier['sup_name']; ?></td>
                          <td><?php echo $supplier['mobile']; ?></td>
                         <td><a href="edit_supplier.php?id=<?php echo $supplier['sup_id']; ?>" class="btn btn-primary">Edit</a></td>
                          <td><a href="delete_supplier.php" class="btn btn-danger">Delete</a></td>
                      </tr>
                  <?php
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
  </div>  
  </div>  


     <?php 
     $frame->last_part();
     ?>     
          


    </body>
</html>