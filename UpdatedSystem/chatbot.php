<?php
session_start();

// Clear chat if 'clear' is set
if (isset($_POST['clear'])) {
    unset($_SESSION['messages']);
    header('Location: chatbot.php');
    exit();
}

// Mock chatbot response
function getChatbotResponse($userMessage) {
    // Simulating some basic chatbot responses
    if (stripos($userMessage, 'hello') !== false) {
        return "Hello! How can I assist you today?";
    } elseif (stripos($userMessage, 'poultry') !== false) {
        return "Poultry Pro helps poultry farmers manage their farms more efficiently.";
    } else {
        return "I'm sorry, I don't have an answer for that. Please contact support for more assistance.";
    }
}

// Store user messages and responses in session
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $userMessage = htmlspecialchars($_POST['message']);
    $botMessage = getChatbotResponse($userMessage);

    if (!isset($_SESSION['messages'])) {
        $_SESSION['messages'] = [];
    }

    // Add both user and bot messages to session
    $_SESSION['messages'][] = ['user' => $userMessage, 'bot' => $botMessage];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with PoultryPro</title>
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

        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f1f1f1;
        }

        .message.user {
            background-color: #d5f5e3;
        }

        .message.bot {
            background-color: #f8c471;
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
            <div class="col-md-8">
                <div class="chat-container">
                    <h4 class="text-center mb-4">Chat with PoultryPro</h4>
                    <?php
                    if (isset($_SESSION['messages'])) {
                        foreach ($_SESSION['messages'] as $message) {
                            echo '<div class="message user">' . $message['user'] . '</div>';
                            echo '<div class="message bot">' . $message['bot'] . '</div>';
                        }
                    }
                    ?>
                    <form action="chatbot.php" method="post">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="message" placeholder="Type your message..." required>
                            <button class="btn btn-primary" type="submit">Send</button>
                        </div>
                    </form>
                    <form action="chatbot.php" method="post" class="clear-chat-btn">
                        <input type="hidden" name="clear" value="true">
                        <button class="btn btn-danger" type="submit">Clear Chat</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div style="align-items: center; text-align: center;">
            <a href="index.html" style="color: white;">About Us</a>&nbsp;&nbsp;
            <a href="#" style="color: white;">Market Place</a>&nbsp;&nbsp;
            <a href="contact_us.php" style="color: white;">Contact</a>&nbsp;&nbsp;
            <a href="feedbacks.php" style="color: white;">Review</a>&nbsp;&nbsp;
            <a href="login.php" style="color: white;">Log In</a>&nbsp;&nbsp;
            <a href="register.php" style="color: white;">Sign Up</a>
        </div>
        <div style="align-items: center; text-align: center;">
            <hr style="border: 1px solid white; border-radius: 5px; margin-left: 50px; margin-right: 50px;">
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
                <a href="https://www.facebook.com/abdulrahman.dulapandan?mibextid=JRoKGi"><i class="bi bi-facebook"></i></a>&nbsp;
                <a href="https://wa.me/+94768821356?text=I'm%20interested%20in%20your%20car%20for%20sale"><i class="bi bi-whatsapp"></i></a>
            </div>
        </div>
    </footer>
</body>

</html>
