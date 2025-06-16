<?php
if (isset($_POST["submit"])) {
    require 'PHPMailer/PHPMailerAutoload.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your email address';
    $mail->Password = 'your app password';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom($email, $name);
    $mail->addAddress('addAddress1@gamil.com', 'addName1');
    $mail->addAddress('addAddress2@gamil.com', 'addName2');
    $mail->addAddress('addAddress3@gamil.com', 'addName3');
    $mail->addAddress('addAddress4@gamil.com', 'addName4');

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;

    $statusMessage = '';
    if (!$mail->send()) {
        $statusMessage = '<div class="alert alert-warning d-flex align-items-center" role="alert">
                              <i class="bi bi-exclamation-triangle-fill"></i>&nbsp;Message could not be sent.
                          </div>';
    } else {
        $statusMessage = '<div class="alert alert-success d-flex align-items-center" role="alert">
                              <i class="bi bi-check-circle-fill"></i>&nbsp;Message has been sent.
                          </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoultryPro: Poultry Farm Management Platform</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="header.css">


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .contact-header {
            background-color: #356854;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .contact-info h4 {
            margin-top: 20px;
        }

        .contact-info a {
            color: #356854;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .contact-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .contact-form label {
            font-weight: bold;
        }

        .contact-form .btn {
            background-color: #356854;
            color: white;
            font-weight: bold;
        }

        .contact-form .btn:hover {
            background-color: #2c5443;
        }

        .map-container {
            margin-top: 30px;
        }

        .map-container iframe {
            border: 0;
            width: 100%;
            height: 450px;
        }

        .contact-section.with-overlay {
            position: relative;
            background-image: url('images/img3.jpg');
            background-size: 500px 600px;
        }

        .contact-section.with-overlay::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.4);
            z-index: 1;
        }

        .contact-info {
            position: relative;
            z-index: 2;
            margin: 60px 20px;
        }

        #row1 {
            margin: auto;
        }
    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <?php if (isset($statusMessage)) echo $statusMessage; ?>


    <div class="container contact-container">
        <div class="contact-header" style="margin-top: 30px;">
            <h2>Contact Us</h2>
            <p>We'd love to hear from you! Please reach out to us through any of the following methods.</p>
        </div>
        <div class="row" id="row1">
            <div class="col-md-6 contact-section with-overlay">
                <div class="contact-info">
                    <div>
                        <h4>WhatsApp Us</h4>
                        <a href="https://wa.me/+94768821356?text=I'm%20interested%20in%20your%20car%20for%20sale">
                            <i class="bi bi-whatsapp" style="font-size: 24px;"></i> +94 76 882 1356
                        </a>
                    </div>
                    <div>
                        <h4>FAQ Chat</h4>
                        <a href="chatFAQ.php">
                            <i class="bi bi-wechat" style="font-size: 24px;"></i> Start a chat
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 contact-section" style="margin-top: 15px;">
                <div class="contact-form">
                    <h4>Email Us</h4>
                    <a href="mailto:cst21057@std.uwu.ac.lk" style="color: #356854; text-decoration: none;">
                        <i class="bi bi-envelope-at-fill" style="font-size: 24px;"></i> support@poultrypro.com
                    </a>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                        <div class="form-group mb-3">
                            <label for="name">Full Name:</label>
                            <input class="form-control" type="text" name="name" id="name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email:</label>
                            <input class="form-control" type="email" name="email" id="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="subject">Subject:</label>
                            <input class="form-control" type="text" name="subject" id="subject" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="message">Message:</label>
                            <textarea class="form-control" name="message" id="message" rows="4" required></textarea>
                        </div>
                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-primary" name="submit" value="Send">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row map-container">
            <div class="col-md-12">
                <h4>Our Location</h4>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.2212940273894!2d81.07679557373318!3d6.98319141765334!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae4618a1a9fec37%3A0x1dd900702229654b!2sUva%20Wellassa%20University%20of%20Sri%20Lanka!5e0!3m2!1sen!2slk!4v1721728842713!5m2!1sen!2slk" loading="lazy"></iframe>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>

</html>