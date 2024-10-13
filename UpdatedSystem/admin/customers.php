<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once '../classes/User.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$users = $user->getAllCustomers();

$admin = CheckLogin::checkLoginAndRole($user_id, 'admin');

$adminframe = new AdminFrame();
$adminframe->first_part($admin);
?>

<!-- Custom Styling -->
<style>
    .contentArea {
        background-color: #f8f9fa;
        padding: 20px;
        min-height: 100vh;
    }

    .card {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 10px;
    }

    .card-header {
        background-color: #3E497A;
        color: white;
        border-radius: 10px 10px 0 0;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        margin: 0;
        font-size: 25px;
        font-weight: bold;
    }

    .table {
        margin-top: 20px;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    .table th, .table td {
        vertical-align: middle;
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
    }

    .btn-success, .btn-danger {
        padding: 8px 12px;
        font-size: 14px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .btn a {
        color: white;
        text-decoration: none;
    }

    .btn:hover {
        opacity: 0.9;
    }

    .search-bar {
        width: 250px;
        position: relative;
    }

    .search-bar input {
        border-radius: 10px;
        padding-left: 15px;
        padding-right: 35px;
        height: 38px;
        border: 1px solid #ccc;
        width: 100%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .search-bar i {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        pointer-events: none;
    }

    .no-data {
        text-align: center;
        font-size: 18px;
        color: #888;
    }
</style>

<div class="contentArea">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><strong>Customers Management</strong></h5>
                        <div class="search-bar">
                            <input type="text" id="searchMedInput" placeholder="Search customer..." onkeyup="searchCustomer()">
                            <i class="bi bi-search"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">City</th>
                                    <th scope="col">Mobile</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Registered</th>
                                    <th scope="col">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($users) {
                                    $uid = 1;
                                    foreach ($users as $user) {
                                        ?>
                                        <tr>
                                            <td><?php echo $uid; ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['address']); ?></td>
                                            <td><?php echo htmlspecialchars($user['city']); ?></td>
                                            <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars(date("d M Y", strtotime($user['CREATED_AT']))); ?></td>
                                            <td>
                                                <?php if ($user['status'] == 0) { ?>
                                                    <button class="btn btn-success">
                                                        <a href="unblock_user.php?unblock=<?php echo urlencode($user['user_id']); ?>&role=customer" class="text-light">Unblock</a>
                                                    </button>
                                                <?php } else { ?>
                                                    <button class="btn btn-danger">
                                                        <a href="block_user.php?block=<?php echo urlencode($user['user_id']); ?>&role=customer" class="text-light">Block</a>
                                                    </button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $uid++;
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="8" class="no-data">No Customers found</td>
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
    </div>
</div>

<?php
$adminframe->last_part();
?>

<script>
    function searchCustomer() {
        var input = document.getElementById("searchMedInput");
        var filter = input.value.toUpperCase();
        var table = document.querySelector(".table");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var name = rows[i].getElementsByTagName("td")[1];
            var address = rows[i].getElementsByTagName("td")[2];

            if (name || address) {
                var nameValue = name.textContent || name.innerText;
                var addressValue = address.textContent || address.innerText;

                if (
                        nameValue.toUpperCase().indexOf(filter) > -1 ||
                        addressValue.toUpperCase().indexOf(filter) > -1
                        ) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }
</script>
