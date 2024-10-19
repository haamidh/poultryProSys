<?php

class CustomerFrame {

    public function __construct() {}

    public function first_part($customer) {
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Customer Dashboard</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
            <style>
                .nav-item {
                    font-weight: bold;
                }

                .nav-item:hover {
                    background-color: #16a085;
                }

                .dropdown-item {
                    font-weight: bold;
                }

                .dropdown-item:hover {
                    background-color: #16a085;
                    color: white;
                }
            </style>
        </head>

        <body>
            <nav class="navbar navbar-expand-lg" style="background-color: #1abc9c;">
                <div class="container-fluid p-0">
                    <div class="navbar-brand text-center mx-auto">
                        <h4 style="font-weight: bold; font-size: 20px;"><?php echo strtoupper(htmlspecialchars($customer['username'])); ?></h4>
                        <h6 style="font-weight: bold;"><?php echo strtoupper(htmlspecialchars($customer['address']) . " - " . htmlspecialchars($customer['city'])); ?></h6>
                    </div>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </nav>

            <div class="container-fluid p-0">
                <div class="row g-0">
                    <nav class="col-lg-2 col-md-3 col-sm-4">
                        <div class="navbar-collapse collapse show p-3 vh-100" id="navbarNavDropdown" style="background-color: #16a085;">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link text-white" href="dashboard.php">
                                        <i class="bi bi-microsoft"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white" href="orders.php">
                                        <i class="bi bi-cart-fill"></i> Orders
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white" href="profile.php">
                                        <i class="bi bi-person-fill"></i> Profile
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="../../logout.php" class="nav-link text-white">
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
