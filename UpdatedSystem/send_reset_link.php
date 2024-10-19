<?php

// send_reset_link.php
require 'classes/config.php';
require 'PHPMailer/PHPMailerAutoload.php';
session_start(); // Start session to handle messages
// Database connection
$database = new Database();
$conn = $database->getConnection();

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $errors = false;

    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format.";
        header("Location: forget_password.php");
        exit();
    }

    // Check if email exists in the user table
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a secure token and set expiration
        $token = bin2hex(random_bytes(50));
        $expires = date("U") + 1800; // Token valid for 30 minutes
        // Update token in database
        $stmt = $conn->prepare("UPDATE user SET reset_token = :token, token_expires = :expires WHERE email = :email");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires', $expires);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Create password reset link
        $resetLink = "http://localhost/poultryProSys/UpdatedSystem/process_reset_password.php?token=$token";

        // Send email with PHPMailer
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'arahmandulapandan@gmail.com'; // Use your Gmail account
        $mail->Password = 'peqc pndj viho pslc';   // Use an app password generated from Google
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('arahmandulapandan@gmail.com', 'Poultry Pro');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Password Reset Request";
        $mail->Body = "<p>Click the following link to reset your password:</p>
                       <p><a href=\"$resetLink\">$resetLink</a></p>
                       <p>This link will expire in 30 minutes.</p>";

        // Attempt to send the email
        if ($mail->send()) {
            $_SESSION['success_message'] = "A password reset link has been sent to your email.";
        } else {
            $_SESSION['error_message'] = "Failed to send email. Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['error_message'] = "No user found with that email.";
    }

    // Redirect to forgot password page with appropriate message
    header("Location: forget_password.php");
    exit();
} else {
    $_SESSION['error_message'] = "Email is required.";
    header("Location: forget_password.php");
    exit();
}
?>
