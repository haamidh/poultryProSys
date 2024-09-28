<?php

class Frame
{

    public function __construct()
    {
    }

    public function first_part($farm)
    {
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Farm-PoultryPro</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
            <link rel="stylesheet" href="style.css">
        </head>

        <body>
            <div class="header pt-1 navbar-expand-lg navbar-dark">
                <div class="d-flex justify-content-between align-items-center w-100" style="padding-left: 250px;">
                    <div class="mx-auto text-center">
                        <a class="navbar-brand" href="#" style="font-weight: bold; font-size: 20px;">
                            <?php echo strtoupper(htmlspecialchars($farm['username'])); ?>
                        </a><br>
                        <a style="font-weight: bold;">
                            <?php echo strtoupper(htmlspecialchars($farm['address']) . " - " . htmlspecialchars($farm['city'])); ?>
                        </a>
                    </div>
                    <div class="navbar-footer">
                        <a href="logout.php" class="nav-link" style="color: white;font-weight:bold;">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="raw">
                <div class="sidebar show justify-content-center">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <aside class="sideNew collapse" id="sidebar">
                        <nav class="navbar navbar-dark">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="farm_dashboard.php">
                                        <i class="bi bi-window-dash"></i>
                                        <span>DASHBOARD</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="birds.php">
                                        <i class="bi bi-twitter"></i>
                                        <span>BIRDS</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="bi bi-wallet-fill"></i>
                                        <span>MISCELLANEOUS</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="feed.php" class="nav-link">
                                        <i class="bi bi-backpack4-fill"></i>
                                        <span>FEEDS</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="medicine.php" class="nav-link">
                                        <i class="bi bi-capsule-pill"></i>
                                        <span>MEDICINE</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="bi bi-cash-coin"></i>
                                        <span>ORDER</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="products.php" class="nav-link">
                                        <i class="bi bi-cart4"></i>
                                        <span>PRODUCT</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="supplier.php" class="nav-link">
                                        <i class="bi bi-person-fill-up"></i>
                                        <span>SUPPLIER</span>
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-clipboard-data-fill"></i>
                                        <span>REPORTS</span>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="farm_stock.php">Stock Report</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="farm_incomes.php">Income Report</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="farm_expenses.php">Expenses Report</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="farm_profit.php">Profit Report</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </aside>
                </div>
            <?php
        }

        public function last_part()
        {
            ?>
            </div>
        </body>

        </html>
<?php
        }
    }
?>