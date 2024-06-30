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
                <a class="navbar-brand" href="#" style="font-weight: bold; font-size: 20px;"><?php echo strtoupper(htmlspecialchars($farm['username'])); ?></a><br>
                <a style="font-weight: bold;"><?php echo strtoupper(htmlspecialchars($farm['address']) . " - " . htmlspecialchars($farm['city'])); ?></a>
            </div>
            <div class="raw">
                <div class="sidebar show justify-content-center">
                    <aside class="sideNew" id="sidebar">
                        <nav class="navbar navbar-dark">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="farm_dashboard.php?user_id=<?php echo htmlspecialchars($farm['user_id']); ?>">
                                        <i class="bi bi-window-dash"></i>
                                        <span>DASHBOARD</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="birds.php?user_id=<?php echo htmlspecialchars($farm['user_id']); ?>">
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
                                    <a href="#" class="nav-link">
                                        <i class="bi bi-backpack4-fill"></i>
                                        <span>FEEDS</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="medicine.php?user_id=<?php echo htmlspecialchars($farm['user_id']); ?>" class="nav-link">
                                        <i class="bi bi-capsule-pill"></i>
                                        <span>MEDICINE</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="bi bi-cart4"></i>
                                        <span>ORDER</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="bi bi-cart4"></i>
                                        <span>PRODUCT</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="supplier.php?user_id=<?php echo htmlspecialchars($farm['user_id']); ?>"  class="nav-link">
                                        <i class="bi bi-person-fill-up"></i>
                                        <span>SUPPLIER</span>
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-caret-down-fill"></i>
                                        <span>REPORTS</span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Stock Report</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Income Report</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Expenses Report</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Profit Report</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                        <div class="navbar-footer">
                            <a href="logout.php" class="nav-link">
                                <i class="bi bi-box-arrow-left"></i>
                                <span>Logout</span>
                            </a>
                        </div>
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