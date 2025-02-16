<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';
require_once '../classes/Feed.php';
require '../classes/Validation.php';

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

$feed = new Feed($con);
$feed->setUser_id($user_id);
$textErr = $notifyErr = "";

$errors = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_feed'])) {
        $feed_name = $_POST['feed_name'];
        $least_quantity = number_format($_POST['least_quantity'], 2);
        $description = $_POST['description'];

        $feed->setFeed_name($feed_name);
        $feed->setLeastQuantity($least_quantity);
        $feed->setDescription($description);

        // Validate Decimal
        if (!Validation::validateDecimalField($feed->getLeastQuantity(), $notifyErr)) {
            $errors = true;
        }

        // Validate name
        if (!Validation::validateTextField($feed->getFeed_name(), $textErr)) {
            $errors = true;
        }

        if (!$errors) {

            if ($feed->feedExists($user_id)) {
                $error_message = "This feed already exists";
            } else {

                if ($feed->create($user_id)) {
                    $success_message = "Feed added successfully.";
                } else {
                    $error_message = "Failed to add feed.";
                }
            }
        }
    }
}

$feeds = $feed->read($user_id);
?>

<style>
    .form-label {
        text-align: left;
        display: block;
        /* Ensures it behaves like a block-level element */
    }

    .card {

        border: none;
        border-radius: 10px;

    }
</style>

<main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-5 text-center">

            <!-- Add New Feed Section -->
            <div class="col-lg-5 col-md-10 col-12 mb-3 px-5">
                <div class="card shadow">
                    <div class="card-header p-3 text-center" style="background-color: #356854;">
                        <h5 class="card-title text-white mb-0">
                            <strong style="font-size: 24px;">New Feed</strong>
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

                        <!-- Feed Form -->
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Feed Name:</label>
                                <input type="text" class="form-control" id="feed_name" name="feed_name" required onkeyup="validateFeedName()">
                                <small id="nameError" class="text-danger"><?php echo $textErr ?></small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notification Threshold:</label>
                                <input type="text" class="form-control" id="least_quantity" name="least_quantity" required onkeyup="validateLeastQuantity()">
                                <small id="notifyError" class="text-danger"><?php echo $notifyErr ?></small>
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Description:</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-block" name="add_feed">Add Feed</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- Feed Details Section -->
            <div class="col-lg-7 col-md-10 col-12 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #3E497A;">
                        <h5 class="card-title text-white mb-0">
                            <strong style="font-size:25px;">Feed Details</strong>
                        </h5>
                        <div class="input-group" style="width: 250px;">
                            <input type="text" id="searchFeedInput" class="form-control" placeholder="Search feed..." onkeyup="searchFeed()">
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
                                        <th scope="col">Feed Name</th>
                                        <th scope="col">Description</th>
                                        <th scope="col" style="width:32%">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialnum = 0;
                                    foreach ($feeds as $feed) {
                                        $serialnum++;
                                    ?>
                                        <tr>
                                            <th><?php echo $serialnum; ?></th>
                                            <td><?php echo $feed['feed_name']; ?></td>
                                            <td><?php echo $feed['description']; ?></td>
                                            <td>
                                                <a href="buy_feed.php?feed_id=<?php echo $feed['feed_id']; ?>" class="btn btn-primary text-dark  py-1 px-2 "><i class="bi bi-plus-square-fill" style="font-size:18px;"></i></a>
                                                <a href="use_feed.php?feed_id=<?php echo $feed['feed_id']; ?>" class="btn btn-warning text-dark py-1 px-2 "><i class="bi bi-dash-square-fill" style="font-size:18px;"></i></a>
                                                <a href="edit_feed.php?feed_id=<?php echo $feed['feed_id']; ?>" class="btn btn-success text-light py-1 px-2">Edit</a>
                                                <button class="btn btn-danger text-light py-1 px-2" onclick="myFunction(<?php echo $feed['feed_id']; ?>)">Delete</button>
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
    function myFunction(feed_id) {
        if (confirm("Are you sure you want to delete this feed?")) {
            window.location.href = "delete_feed.php?feed_id=" + feed_id;
        }
    }

    function searchFeed() {
        var input = document.getElementById("searchFeedInput");
        var filter = input.value.toUpperCase();
        var table = document.querySelector(".table");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var feedName = rows[i].getElementsByTagName("td")[0];
            var description = rows[i].getElementsByTagName("td")[1];

            if (feedName || description) {
                var nameValue = feedName.textContent || feedName.innerText;
                var descriptionValue = description.textContent || description.innerText;

                if (
                    nameValue.toUpperCase().indexOf(filter) > -1 ||
                    descriptionValue.toUpperCase().indexOf(filter) > -1
                ) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }

    function validateFeedName() {
        const feedNameInput = document.getElementById('feed_name');
        const nameErr = document.getElementById('nameError'); // Updated ID to match HTML
        const feedNameValue = feedNameInput.value;

        const regex = /^[a-zA-Z][a-zA-Z0-9\s-_]{2,}$/;

        nameErr.textContent = "";
        feedNameInput.classList.remove("is-invalid");

        if (feedNameValue.length > 0 && !regex.test(feedNameValue)) {
            nameErr.textContent = "Feed name not valid (must start with a letter and be at least 3 characters).";
            feedNameInput.classList.add("is-invalid");

            const correctedValue = feedNameValue.replace(/^[^a-zA-Z]+/, '').replace(/[^a-zA-Z0-9\s-_]/g, '');

            feedNameInput.value = correctedValue;
        }
    }

    function validateLeastQuantity() {
        const quantityInput = document.getElementById('least_quantity');
        const quantityErr = document.getElementById('notifyError'); // Updated ID to match HTML
        const quantityValue = quantityInput.value;

        const regex = /^(0|[1-9]\d*)(\.\d{1,2})?$/; // Allows positive numbers with up to two decimal places

        quantityErr.textContent = "";
        quantityInput.classList.remove("is-invalid");

        if (quantityValue.length > 0 && !regex.test(quantityValue)) {
            quantityErr.textContent = "Quantity not valid (must be a positive number with up to two decimal places).";
            quantityInput.classList.add("is-invalid");
            quantityInput.value = quantityValue.replace(/[^0-9.]/g, ''); // Replace invalid characters
        }

        // Additional check to prevent starting with a decimal
        if (quantityInput.value.length > 0 && quantityInput.value[0] === '.') {
            quantityErr.textContent = "Quantity cannot start with a decimal point.";
            quantityInput.classList.add("is-invalid");
            quantityInput.value = "";
        }
    }
</script>