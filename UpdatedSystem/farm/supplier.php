<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';
require_once '../classes/Supplier.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

$supplier = new Supplier($con);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_supplier'])) {
        $sup_name = $_POST['sup_name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $mobile = $_POST['mobile'];  // Changed from 'mobile' to 'contact'
        $email = $_POST['email'];

        $supplier->setSup_name($sup_name);
        $supplier->setAddress($address);
        $supplier->setCity($city);
        $supplier->setMobile($mobile);  // Changed from 'setMobile' to 'setContact'
        $supplier->setEmail($email);

        if ($supplier->supplierExists($user_id)) {
            $error_message = "This supplier already exists";
        } else if ($supplier->supplierEmailExists($user_id)) {
            $error_message = "This supplier email already exists";
        } else {

            if ($supplier->create()) {
                $success_message = "Supplier added successfully.";
            } else {
                $error_message = "Failed to add supplier.";
            }
        }
    }
}

$query = "SELECT city FROM city";
$stmt = $con->prepare($query);
$stmt->execute();
$cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Read all suppliers
$suppliers = $supplier->readAll();
?>
<style>
    .form-label {
        text-align: left;
        display: block; /* Ensures it behaves like a block-level element */
    }

</style>
<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 text-center">

            <!-- Add New Supplier Section -->
            <div class="col-lg-5 col-md-10 col-12 mb-3 px-5">
                <div class="card shadow">
                    <div class="card-header p-3 text-center" style="background-color: #356854;">
                        <h5 class="card-title text-white mb-0">
                            <strong style="font-size: 24px;">New Supplier</strong>
                        </h5>
                    </div>
                    <div class="card-body" style="background-color: #F5F5F5;">

                        <!-- Success and Error Messages -->
                        <?php if (isset($success_message)) : ?>
                            <div class="alert alert-success text-center">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger text-center">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Supplier Form -->
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Supplier Name:</label>
                                <input type="text" class="form-control" id="sup_name" name="sup_name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address:</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">City:</label>
                                <select class="form-select" name="city" id="city" required>
                                    <option value="" disabled selected>Select city</option>
                                    <?php foreach ($cities as $city) : ?>
                                        <option value="<?php echo htmlspecialchars($city['city']); ?>">
                                            <?php echo htmlspecialchars($city['city']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contact:</label>
                                <input type="text" class="form-control" id="contact" name="mobile" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-block" name="add_supplier">Add Supplier</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- Supplier Details Section -->
            <div class="col-lg-7 col-md-10 col-12 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title text-white mb-0">
                            <strong style="font-size:25px;">Supplier Details</strong>
                        </h5>
                        <div class="input-group" style="width: 250px;">
                            <input type="text" id="searchSupplierInput" class="form-control" placeholder="Search supplier..." onkeyup="searchSupplier()">
                            <span class="input-group-text">
                                <i class="bi bi-search" style="color: #3E497A;"></i>
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-striped table-bordered text-center">
                                <thead style="background-color: #3E497A; color: white;">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Supplier Name</th>
                                        <th scope="col">Contact</th>
                                        <th scope="col">Email</th>
                                        <th scope="col" style="width:32%">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialnum = 0;
                                    foreach ($suppliers as $supplier) {
                                        $serialnum++;
                                        ?>
                                        <tr>
                                            <th><?php echo $serialnum; ?></th>
                                            <td><?php echo $supplier['sup_name']; ?></td>
                                            <td><?php echo $supplier['mobile']; ?></td>
                                            <td><?php echo $supplier['email']; ?></td>
                                            <td>
                                                <a href="edit_supplier.php?sup_id=<?php echo $supplier['sup_id']; ?>" class="btn btn-success text-light py-1 px-2">Edit</a>
                                                <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $supplier['sup_id']; ?>)">Delete</button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- Confirmation for Deletion -->
<script>
    function myFunction(sup_id) {
        if (confirm("Are you sure you want to delete this supplier?")) {
            window.location.href = "delete_supplier.php?sup_id=" + sup_id;
        }
    }

    function searchSupplier() {
        var input = document.getElementById("searchSupplierInput");
        var filter = input.value.toUpperCase();
        var table = document.querySelector(".table");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var supplierName = rows[i].getElementsByTagName("td")[0];
            var contact = rows[i].getElementsByTagName("td")[1];
            var email = rows[i].getElementsByTagName("td")[2];

            if (supplierName || contact || email) {
                var nameValue = supplierName.textContent || supplierName.innerText;
                var contactValue = contact.textContent || contact.innerText;
                var emailValue = email.textContent || email.innerText;

                if (
                        nameValue.toUpperCase().indexOf(filter) > -1 ||
                        contactValue.toUpperCase().indexOf(filter) > -1 ||
                        emailValue.toUpperCase().indexOf(filter) > -1
                        ) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }
</script>
