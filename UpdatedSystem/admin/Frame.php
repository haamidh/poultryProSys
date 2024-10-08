<?php

class AdminFrame {

    public function __construct() {
        
    }

    public function first_part($admin) {
        ?>

        <!DOCTYPE html>
        <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Admin-PoultryPro</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
                <link rel="stylesheet" href="../style.css">
            </head>


            <body>
                <nav class="navbar navbar-expand-lg " style="background-color: #D0D4CA;">
                    <div class="container-fluid p-0">
                        <div class="navbar-brand text-center mx-auto">
                            <h4 style="font-weight: bold; font-size: 20px;">ADMIN - PoultryPro</h4>
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                </nav>

                <div class="container-fluid p-0">
                    <div class="row g-0">
                        <nav class="col-lg-2 col-md-3 col-sm-4 ">
                            <div class="navbar-collapse collapse show p-3 vh-100" id="navbarNavDropdown" style="background-color: #40826D;">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="dashboard.php">
                                            <i class="bi bi-window-dash"></i> Dashboard
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="farms.php">
                                            <i class="bi bi-twitter"></i> FARMS
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="customers.php">
                                            <i class="bi bi-wallet-fill"></i> CUSTOMERS
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="">
                                            <i class="bi bi-backpack4-fill"></i> INCOMES
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="reviews.php">
                                            <i class="bi bi-capsule-pill"></i> REVIEWS
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="../logout.php" class="nav-link text-white">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </nav>

                        <?php
                    }

                    public function last_part() {
                        ?>

                    </div>
                </div>
            </body>

        </html>
        <?php
    }

}
?>