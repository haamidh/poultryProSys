<!DOCTYPE html>
<html>

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

                margin-left: 250px;
                margin-right: 250px;
            }

            .solid-hr1 {
                border: 5px solid black;
                border-radius: 5px;

                margin-left: 20%;
                margin-right: 20%;
                margin-top: 10%;
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
                margin: 0 5px;
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
                font-size: 30px;
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
        <?php include 'includes/header.php'; ?>

        <div class="contentArea"
             style=" background-image: url('images/img1.png'); background-repeat: no-repeat; background-size: cover">
            <div class="overlay"></div>
            <div class="text-container">
                <h1 class="underlined"
                    style="font-size: 60px;font-family: 'Times New Roman', Times, serif;font-weight: bold;">PoultryPro</h1>
                <hr class="solid-hr">
                <h1 style="font-size: 35px;">"Our mission is to develop a comprehensive poultry farm management platform
                    that empowers farmers to seamlessly manage their farms, while also connecting them directly with
                    customers to sell poultry products."</h1>
            </div>
        </div>
        <hr class="solid-hr1">


        <!--        <div class="row2" style="display: flex; justify-content: space-around; align-items: center;">
        
                    <div class="col1" style="flex: 1; text-align: center; padding: 20px; margin-left: 80px;">-->
        <div class="row my-5 mx-5 pt-4 text-center align-items-center">

            <div class="col-lg-7 col-md-12 col-12">
                <h1 style="font-family: 'Times New Roman', Times, serif; font-weight: bold;font-size: 42px;">Our
                    Background</h1>
                &nbsp;
                <h5
                    style="font-size: 28px; font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;color: #356854;">
                    The poultry farming industry is crucial to the global food supply chain,
                    yet farmers face challenges with inefficient manual record keeping and limited
                    market reach. Motivated by poultry farmers' struggles, we aimed to solve these
                    problems and positively impact farmers and customers.
                </h5>

            </div>
            <!--                <div class="col2" style="flex: 1; text-align: center;  padding: 20px;">-->
            <div class="col-lg-5 col-md-12 col-12">
                <img src="images/img3.jpg" alt="Image for our bacground" style="width: 300px; height: 400px;">
            </div>
        </div>



        <hr class="solid-hr1">

        <!--        <div style="padding-top: 100px;">
        
                    <div class="row2" style="display: flex; justify-content: space-around; align-items: center;">
        
                        <div class="col2" style="flex: 1; text-align: center;  padding: 10px;">-->
        <div class="row my-5 mx-5 pt-4 text-center align-items-center">

            <div class="col-lg-5 col-md-12 col-12">
                <img src="images/img2.jpg" alt="Image for farms" style="width: 350px; height: 350px;">
            </div>

            <div class="col-lg-7 col-md-12 col-12">
                <h1 style="font-family: 'Times New Roman', Times, serif; font-weight: bold;font-size: 42px;">For Farms
                </h1>
                &nbsp;
                <h5
                    style="font-size: 28px; font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;color: #356854;">
                    Farmers can securely register, log in and access a
                    dashboard with key farm metrics. The system includes detailed management of poultry inventory,
                    medicine and feed tracking, and product listings with descriptions and prices. It also handles
                    order processing, payment, and sales reports.
                </h5>

            </div>

        </div>
        <!--</div>-->

        <hr class="solid-hr1">

        <!--        <div style="padding-top: 100px;">
        
                    <div class="row2" style="display: flex; justify-content: space-around; align-items: center;">
        
                        <div class="col1" style="flex: 1; text-align: center; padding: 20px; margin-left: 80px;">-->
        <div class="row my-5 mx-5 pt-4 text-center align-items-center">

            <div class="col-lg-7 col-md-12 col-12">
                <h1 style="font-family: 'Times New Roman', Times, serif; font-weight: bold;font-size: 42px;">For
                    Customers</h1>
                &nbsp;
                <h5
                    style="font-size: 28px; font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;color: #356854;">
                    For customers, <span>PoultryPro</span> provides secure registration and login, personal
                    information and delivery address management, and browsing of categorized poultry
                    products with detailed descriptions and prices. Customers can place orders, make
                    secure payments, and share reviews and feedback.
                </h5>

            </div>
            <!--            <div class="col2" style="flex: 1; text-align: center;  padding: 20px;">-->
            <div class="col-lg-5 col-md-12 col-12">
                <img src="images/img4.jpg" alt="Image for our customer"
                     style="width: 300px; height: 400px;">
            </div>
        </div>
        <!--        </div>-->

        <hr class="solid-hr1">

        <!--        <div style="padding-top: 100px;">
        
                    <div class="row2" style="display: flex; justify-content: space-around; align-items: center;">
        
                        <div class="col1" style="flex: 1; text-align: center; padding: 20px; margin-left: 80px;">-->
        <div class="row my-5 mx-5 pt-4 text-center align-items-center">
            <h1 style="font-family: 'Times New Roman', Times, serif; font-weight: bold;font-size: 42px;">From
                Administration Side</h1>
            &nbsp;
            <h5
                style="font-size: 28px; font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;color: #356854;">
                We respond to monitoring activities, manage user accounts, protect user information,<br> and
                ensure smooth and efficient system operation. Additionally, we communicate with and
                assist users facing challenges when using the system, ensuring they receive <br>prompt support and
                guidance.
            </h5>

        </div>
        <!--            </div>
                </div>-->


        <hr class="solid-hr1">

        <!--        <div style="padding-top: 100px;">
                    <div class="row2" style="display: flex; justify-content: space-around; align-items: center; margin-top: 30px;">-->
        <div class="row my-5 mx-5 pt-4 text-center align-items-center">
            <h1
                style="font-family: 'Times New Roman', Times, serif; font-weight: bold;font-size: 42px;">
                Our Supervisor</h1>
            <div class="row my-5 pt-4 text-center align-items-center">
                <div class="col-lg-3 col-md-12 col-12"></div>

                <div class="col-lg-3 col-md-12 col-12">

                    <img src="images/arunasir.jpg" style="width: auto; height: auto; border-radius: 50%;">

                </div>
                <div class="col-lg-3 col-md-12 col-12 pt-4">

                    <h3 style="font-weight: bold;">Mr. Aruna Sanjeewa</h3>
                    <h5>Lecturer<br>Faculty of Applied Sciences<br>Uva Wellassa University<br><a
                            href="aruna.s@uwu.ac.lk">aruna.s@uwu.ac.lk</a></h5>
                </div>
                <div class="col-lg-3 col-md-12 col-12"></div>
            </div>

        </div>


        <hr class="solid-hr1">

        <!--    <div style="padding-top: 100px;">-->
        <div class="row my-5 mx-5 pt-4 text-center align-items-center">
            <h1
                style="font-family: 'Times New Roman', Times, serif; font-weight: bold;font-size: 42px; text-align: center;">
                Our Developing Team</h1>


            <!--<div class="col" style="flex: 1; text-align: center;  padding: 20px;">-->
            <div class="col-lg-1 col-md-12 col-12 pt-4"></div>
            <div class="col-lg-2 col-md-12 col-12 pt-4">
                <img src="images/abdul.jpeg" style="width: 150px; height: 150px; border-radius: 50%;">
                &nbsp;
                <h5 style="font-weight: bold;">A.R. Dulapandan</h5>
                <h6>UWU/CST/21/048<br>Computer Science and Technology<br>Uva Wellassa University</h6>


            </div>

            <!--<div class="col" style="flex: 1; text-align: center;  padding: 20px;">-->
            <div class="col-lg-2 col-md-12 col-12 pt-4">
                <img src="images/tharindhu.jpeg" style="width: 150px; height: 150px; border-radius: 50%;">
                <h5 style="font-weight: bold;">H.T.D. De Zoysa</h5>
                <h6>UWU/CST/21/049<br>Computer Science and Technology<br>Uva Wellassa University</h6>
            </div>

            <!--<div class="col" style="flex: 1; text-align: center;  padding: 20px;">-->
            <div class="col-lg-2 col-md-12 col-12 pt-4">
                <img src="images/hamidh.jpg" style="width: 150px; height: 150px; border-radius: 50%;">
                <h5 style="font-weight: bold;">A.A.A. Haamidh</h5>
                <h6>UWU/CST/21/057<br>Computer Science and Technology<br>Uva Wellassa University<br></h6>
            </div>


            <!--<div class="col" style="flex: 1; text-align: center;  padding: 20px;">-->
            <div class="col-lg-2 col-md-12 col-12 pt-4">
                <img src="images/dulakshmi.jpg" style="width: 150px; height: 150px; border-radius: 50%;">
                <h5 style="font-weight: bold;">V.D.S. Premachandra</h5>
                <h6>UWU/CST/21/072<br>Computer Science and Technology<br>Uva Wellassa University</h6>
            </div>

            <!--<div class="col" style="flex: 1; text-align: center;  padding: 20px;">-->
            <div class="col-lg-2 col-md-12 col-12 pt-4">
                <img src="images/jayathi.jpg" style="width: 150px; height: 150px; border-radius: 50%;">
                <h5 style="font-weight: bold;">B.S.J. Jayoda</h5>
                <h6>UWU/CST/21/094<br>Computer Science and Technology<br>Uva Wellassa University</h6>
            </div>
            <div class="col-lg-1 col-md-12 col-12 pt-4"></div>
        </div>


        <?php include 'includes/footer.php'; ?>

    </body>

</html>