<?php
session_start();
require 'classes/config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['role'] = 'guest';
}

$db = new Database();
$conn = $db->getConnection();
?>

<!doctype html>
<html lang="en">

<head>
    <title>MarketPlace - PoultryPro</title>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS and Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="marketplace/marketPlaceStyle.css" rel="stylesheet">
    <link rel="stylesheet" href="header.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f7f9fc;
            font-family: 'Poppins', sans-serif;
        }

        /* Carousel Styling */
        .carousel-item {
            position: relative;
            width: 100%;
            padding-bottom: 15.45%; /* Aspect ratio 596/3856 = 15.45% */
            overflow: hidden;
        }

        .carousel-item img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-caption {
            bottom: 40%;
            transform: translateY(50%);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        }

        .carousel-caption h1 {
            font-size: 2rem;
            font-weight: 600;
        }

        .carousel-caption p {
            font-size: 1.2rem;
        }

        /* Product Card Styling */
        .market-product {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background-color: #fff;
            transition: all 0.3s ease-in-out;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
        }

        .market-product:hover {
            transform: translateY(-10px);
            box-shadow: 0px 15px 25px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            border-radius: 15px 15px 0 0;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }

        .market-product:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            padding: 20px;
            text-align: center;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
            transition: color 0.3s ease;
        }

        .market-product:hover .card-title {
            color: #007bff;
        }

        .card-text {
            font-size: 1.25rem;
            font-weight: 500;
            color: #28a745;
            margin-bottom: 1rem;
        }

        .badge {
            font-size: 0.9rem;
            background-color: #28a745;
            padding: 0.5em 0.75em;
            border-radius: 5px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            font-size: 1rem;
            padding: 0.75em 1.5em;
            border-radius: 50px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0px 0px 10px rgba(0, 123, 255, 0.5);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .card-title {
                font-size: 1.25rem;
            }

            .card-text {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <!-- Carousel -->
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/slider2.jpg" class="d-block w-100" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                    <h1>Welcome to PoultryPro</h1>
                    <p>Your one-stop marketplace for all poultry needs.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/slider3.jpg" class="d-block w-100" alt="Second slide">
                <div class="carousel-caption d-none d-md-block">
                    <h1>Quality Products</h1>
                    <p>Find the best products from trusted farms.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/slider2.jpg" class="d-block w-100" alt="Third slide">
                <div class="carousel-caption d-none d-md-block">
                    <h1>Join Our Community</h1>
                    <p>Connect with farmers and suppliers worldwide.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Products Section -->
    <div class="container mt-5">
        <h2 class="text-center mb-5" style="font-weight: 600;">Latest Products</h2>
        <div class="row g-4">
            <?php
            $sql = "SELECT product_id, product_name, farm_id, unit, category_id, product_price, product_img, description FROM products ORDER BY product_id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card market-product h-100">
                    <img src="' . htmlspecialchars($row["product_img"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="card-img-top">
                    <div class="card-body d-flex flex-column text-center">
                        <h5 class="card-title">' . htmlspecialchars($row["product_name"]) . '</h5>
                        <h5 class="card-text">Rs ' . htmlspecialchars(number_format($row["product_price"], 2)) . '</h5>
                        <p class="card-text mb-4">
                            <span class="badge">Per ' . htmlspecialchars($row["unit"]) . '</span>
                        </p>
                        <a href="viewProduct.php?product_id=' . htmlspecialchars($row["product_id"]) . '&farm_id=' . htmlspecialchars($row["farm_id"]) . '" class="btn btn-primary mt-auto">Buy Now</a>
                    </div>
                </div>
            </div>';
                }
            } else {
                echo '<p class="text-center">No products found.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
