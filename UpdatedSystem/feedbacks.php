<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration and Feedback class
include 'classes/config.php';
include 'classes/Feedback.php';
require_once 'classes/checkLogin.php'; // This should handle checking if the user is logged in

// Initialize database connection
$database = new Database();
$db = $database->getConnection();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>

        .contentArea h1 {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 1.0);
        }

        .text-container {
            position: relative;
            z-index: 2;
            padding: 150px 70px;
        }

        #label {
            background-color: #40826D !important;
        }

        #card-header {
            background-color: #40826D !important;
            color: white;
        }

        table thead.custom-thead {
            background-color: #40826D !important;
            color: white;
        }

        .custom-thead th {
            background-color: #40826D !important;
            color: white !important;
        }

    
     

        .contentArea {
            position: relative;
            text-align: center;
            color: white;

        }

        .contentArea h1 {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 1.0);
        }

  

        .text-container {
            position: relative;
            z-index: 2;
            padding: 150px 70px;
        }

    </style>

</head>

<body class="bg-light">
    <?php include 'includes/header.php'; ?>

    <?php


    // Initialize Feedback class
    $feedback = new Feedback($db);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_SESSION['user_id'])) {
            $feedback->setUserId($_SESSION['user_id']);
            $feedback->setUsername($_SESSION['username']);
            $feedback->setRating($_POST['rating']);
            $feedback->setComment($_POST['comment']);
            $feedback->setCreatedAt(date('Y-m-d'));

            if ($feedback->createFeedback()) { ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill"></i>&nbsp;Feedback submitted successfully.
                </div>
            <?php
            } else { ?>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>&nbsp;Failed to submit feedback.
                </div>
            <?php
            }
        } else { ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>&nbsp;You must be logged in to submit feedback.
            </div>;
    <?php
        }
    }

    // Fetch feedback for display
    $feedbacks = $feedback->readFeedback();
    ?>

    <div class="container" style="margin-bottom: 200px;">

        <div class="col-sm-4 float-left" style="margin-right: 50px;">


            <div class="card-header card text-white bg-dark mb-3" id="card-header">
                <h2 class="display-6 text-center" style="font-weight: bold;">Feedback</h2>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <div class="form-group">
                        <input class="form-control" type="hidden" value="<?php echo $_SESSION['user_id']; ?>" name="user_id" id="user_id" readonly>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="hidden" value="<?php echo $_SESSION['username']; ?>" name="username" id="username" readonly>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="rating" class="card text-white bg-dark mb-3" id="label">Rating (1-5):</label>
                    <input class="form-control" type="number" name="rating" id="rating" min="1" max="5" required>
                </div>
                <div class="form-group">
                    <label for="comment" class="card text-white bg-dark mb-3" id="label">Comment:</label>
                    <textarea class="form-control" name="comment" id="comment" rows="4" cols="43"></textarea>
                </div>
                <div class="form-group" style="text-align:center;">
                    <input type="submit" class="btn btn-primary" name="submit" value="Submit">
                </div>
            </form>
        </div>

        <div class="col">

            <div class="card mt-5">
                <table class="table">
                    <thead class="custom-thead">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Rating</th>
                            <th scope="col">Comment</th>
                            <th scope="col">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $feedback) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['username']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['comment']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($feedbacks)) : ?>
                            <tr>
                                <td colspan="5">No feedback found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
    <br>
    <br>

    <footer class="footer">
        <div style="align-items: center; text-align: center;">
            <a href="index.html" style="color: white;">About Us</a>&nbsp;&nbsp;
            <a href="#" style="color: white;">Market Place</a>&nbsp;&nbsp;
            <a href="contact_us.php" style="color: white;">Contact Us</a>&nbsp;&nbsp;
            <a href="feedbacks.php" style="color: white;">Review</a>&nbsp;&nbsp;
            <a href="login.php" style="color: white;">Log In</a>&nbsp;&nbsp;
            <a href="register.php" style="color: white;">Sign Up</a>
        </div>
        <div style="align-items: center; text-align: center;">
            <hr style="border: 1px solid white; border-radius: 5px;  margin-left: 50px; margin-right: 50px;">
        </div>
        <div style="display: flex; align-items: center;">
            <div style="flex: 1; text-align: center;">
                <p>&copy; 2024 PoultryPro. All Rights Reserved.</p>
            </div>
            <div style="display: flex; align-items: center;">
                <a class="navbar-brand mx-5" href="index.html">
                    <img src="images/logo-poultryPro2.jpeg" alt="logo-poultryPro" style="border-radius: 50%;">
                    PoultryPro
                </a>
            </div>
            <div style="flex: 1; text-align: center; font-size: 28px;">
                <a href="login.php" style="color: white;"><i class="bi bi-instagram"></i></a>&nbsp;
                <a href="https://www.facebook.com/abdulrahman.dulapandan?mibextid=JRoKGi"><i class="bi bi-facebook"></i></a>
                &nbsp;
                <a href="https://wa.me/+94768821356?text=I'm%20interested%20in%20your%20car%20for%20sale"><i class="bi bi-whatsapp"></i></a>
                &nbsp;



            </div>

        </div>
    </footer>

</body>

</html>