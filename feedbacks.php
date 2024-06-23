<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .navbar-nav .nav-item a {
            font-weight: bold;
        }

        .navbar {
            background-color: #356854;
        }

        .navbar .nav-link {
            color: white !important;
        }

        .navbar .nav-link:hover {
            color: #ddd !important;
        }

        .navbar .nav-item.active .nav-link {
            color: #fff !important;
            text-decoration: underline;
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .solid-hr {
            border: 5px solid white;
            border-radius: 5px;
        }

        .sign-in-btn {
            background-color: #B7BF4A !important;
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

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
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
    </style>

</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand mx-5" href="index.html">
            <img src="images/logo-poultryPro2.jpeg" alt="logo-poultryPro" style="border-radius: 50%;">
            PoultryPro
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item mx-4">
                    <a class="nav-link" href="index.html">About Us</a>
                </li>
                <li class="nav-item mx-4">
                    <a class="nav-link" href="#">Market Place</a>
                </li>
                <li class="nav-item mx-4">
                    <a class="nav-link" href="#">Contact</a>
                </li>
                <li class="nav-item mx-4">
                    <a class="nav-link" href="feedbacks.php">Review</a>
                </li>
                <li class="nav-item mx-4">
                    <a class="nav-link" href="login.php">Log In</a>
                </li>
                <li class="nav-item mx-4">
                    <a class="nav-link sign-in-btn" href="register.php">Sign Up</a>
                </li>
            </ul>
        </div>
    </nav>

    <?php
    // Start the session
    session_start();

    // Include database configuration and Feedback class
    include 'config.php';
    include 'Feedback.php';
    require_once 'checkLogin.php'; // This should handle checking if the user is logged in

    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();

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
            <div class='alert alert-danger'><i class="bi bi-exclamation-circle-fill"></i>&nbsp;You must be logged in to submit feedback.</div>";
    <?php
        }
    }

    // Fetch feedback for display
    $feedbacks = $feedback->readFeedback();
    ?>

    <div class="container">

        <div class="col-sm-4 float-left">


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
</body>

</html>