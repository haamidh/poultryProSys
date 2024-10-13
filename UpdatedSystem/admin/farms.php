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

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$users = $user->getAllFarms();

$user_id = $_SESSION['user_id'];
$admin = CheckLogin::checkLoginAndRole($user_id, 'admin');

$adminframe = new AdminFrame();
$adminframe->first_part($admin);
?>

<!-- Custom Styling -->
<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Arial', sans-serif;
    }

    .contentArea {
        padding: 20px;
        min-height: 100vh;
    }

    .card {
        border: none;
        border-radius: 10px;
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

    .card-header h5 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }

    .table-responsive {
        padding: 20px;
    }

    .table {
        text-align: center;
        margin-top: 10px;
    }

    .table th {
        background-color: #343a40;
        color: white;
        font-weight: bold;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    .btn {
        font-size: 14px;
        padding: 5px 12px;
        border-radius: 5px;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .no-data {
        text-align: center;
        font-size: 18px;
        color: #888;
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
    }

    .search-bar i {
        position: absolute;
        background-color: white;
        border-left: 0;
    }

</style>

<!-- Page Content -->
<div class="contentArea">
    <div class="container">
        <div class="card mt-5">
            <div class="card-header">
                <h5><strong>Farms Management</strong></h5>
                <div class="search-bar">
                    <input type="text" id="searchMedInput" placeholder="Search farm..." onkeyup="searchFarm()">
                    <i class="bi bi-search"></i>
                </div>
            </div>

            <div class="card-body table-responsive">
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
                                                <a href="unblock_user.php?unblock=<?php echo urlencode($user['user_id']); ?>&role=farm" class="text-light">Unblock</a>
                                            </button>
                                        <?php } else { ?>
                                            <button class="btn btn-danger">
                                                <a href="block_user.php?block=<?php echo urlencode($user['user_id']); ?>&role=farm" class="text-light">Block</a>
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
                                <td colspan="8" class="no-data">No Farms found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$adminframe->last_part();
?>

<script>
    function searchFarm() {
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
