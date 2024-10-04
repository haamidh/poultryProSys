<?php
session_start();

// Clear chat messages if 'clear' is set
if (isset($_POST['clear'])) {
    unset($_SESSION['chat_messages']);
    header('Location: chatFAQ.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['clear'])) {
    $userMessage = $_POST['message'];

    // Simple response logic
    $responses = [
        "What is poultry pro?" => "Poultry Pro is a comprehensive platform that helps poultry farmers 
                                efficiently manage their operations and directly sell products to customers. 
                                It simplifies farm management tasks and connects farmers with the market, 
                                boosting both efficiency and sales.",

        "How can I access PoultryPro?" => "To access Poultry Pro:<br><br>
        1. Visit the Poultry Pro Website: Go to the official Poultry Pro website.<br>
        2. Sign Up or Log In: Create an account or log in if you already have one.<br>
        3. Explore Features: After logging in, you can access farm management tools, product listings, 
        and customer functionalities based on your role (farmer or customer).<br><br>
        For more detailed instructions or support, refer to the Poultry Pro website or contact their customer service.",
    ];

    // Convert user message to lowercase for comparison
    $userMessageLower = strtolower($userMessage);
    $botResponse = "Sorry, I didn't understand that.";

    // Look for a matching response
    foreach ($responses as $key => $response) {
        if (strpos(strtolower($key), $userMessageLower) !== false) {
            $botResponse = $response;
            break;
        }
    }

    // Store chat messages in session
    if (!isset($_SESSION['chat_messages'])) {
        $_SESSION['chat_messages'] = [];
    }

    $_SESSION['chat_messages'][] = ['sender' => 'user', 'message' => $userMessage];
    $_SESSION['chat_messages'][] = ['sender' => 'bot', 'message' => $botResponse];

    // Stay on the same page
    header('Location: chatFAQ.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat Bot - PoultryPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .navbar {
            background-color: #356854;
        }

        .navbar .nav-link {
            color: white !important;
            font-weight: bold;
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
            border-radius: 50%;
        }

        body {
            background-color: #f8f9fa;
        }

        .chat-container {
            max-width: 600px;
            margin: 50px auto;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .chat-messages {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f1f1f1;
        }

        .chat-message {
            margin-bottom: 10px;
            border-radius: 10px;
            padding: 10px;
        }

        .user-message {
            background-color: #e1ffc7;
            text-align: right;
            border: 1px solid #c6e0b4;
        }

        .bot-message {
            background-color: #f1f1f1;
            text-align: left;
            border: 1px solid #dcdcdc;
        }

        .predefined-messages {
            margin-bottom: 20px;
        }

        .predefined-message {
            cursor: pointer;
            color: #007bff;
            text-decoration: none;
            margin: 0 5px;
            padding: 5px 10px;
            border: 1px solid #007bff;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .predefined-message:hover {
            background-color: #007bff;
            color: #fff;
        }

        .clear-chat-btn {
            display: flex;
            justify-content: flex-end;
        }

        .footer {
            background-color: #356854;
            color: white;
            padding: 20px 0;
            margin-top: 100px;
            text-align: center;
        }

        .footer a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .footer img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .footer-icons {
            font-size: 28px;
            margin-top: 10px;
        }

        .footer-icons a {
            color: white;
            margin: 0 10px;
        }

        .footer-icons a:hover {
            color: #ddd;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand mx-5" href="index.html">
            <img src="images/logo-poultryPro2.jpeg" alt="PoultryPro Logo">
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
                    <a class="nav-link" href="contact_us.php">Contact</a>
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

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3 mt-5">
                <div class="predefined-messages mb-4">
                    <h5>Quick Messages</h5>
                    <a class="predefined-message d-block mb-2" onclick="setMessage('What is poultry pro?')">What is poultry pro?</a>
                    <a class="predefined-message d-block mb-2" onclick="setMessage('How can I access PoultryPro?')">How can I access PoultryPro?</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chat-container">
                    <h4 class="text-center mb-4">Live Chat Bot</h4>
                    <div class="chat-messages" id="chat-messages">
                        <?php
                        if (isset($_SESSION['chat_messages'])) {
                            foreach ($_SESSION['chat_messages'] as $msg) {
                                $messageClass = $msg['sender'] == 'user' ? 'user-message' : 'bot-message';

                                echo '<div class="chat-message ' . $messageClass . '">' . $msg['message'] . '</div>';
                            }
                        }
                        ?>
                    </div>
                    <form id="chat-form" action="chatFAQ.php" method="post" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="message" id="message" class="form-control" placeholder="Type your message..." required>
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                    <form action="chatFAQ.php" method="post" class="clear-chat-btn">
                        <button type="submit" name="clear" class="btn btn-warning">Clear Chat</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container text-center">
            <p>&copy; PoultryPro 2024 | Designed by Abdul Rahman Dulapandan</p>
            <div class="footer-icons">
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-instagram"></i></a>
                <a href="#"><i class="bi bi-twitter"></i></a>
            </div>
        </div>
    </footer>

    <script>
        function setMessage(text) {
            document.getElementById('message').value = text;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
