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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="marketplace/marketPlaceStyle.css" rel="stylesheet">
    <link rel="stylesheet" href="header.css">
    <title>MarketPlace - PoultryPro</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body style="background-color:azure;">
    <?php include 'includes/header.php'; ?>
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <ol class="carousel-indicators">
            <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></li>
            <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="images/slider2.jpg" alt="First slide">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="images/slider3.jpg" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="images/slider2.jpg" alt="Third slide">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </a>
    </div>
    <div class="container mt-5" >
        <div class="row">
            <?php
            $sql = "SELECT product_id, product_name, farm_id, unit, category_id, product_price, product_img, description FROM products ORDER BY product_id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '
    <style>
    .tag-container{
    background-color: #f6f6f6 ;
     margin: 0px 15px 15px 15px;
    }
        .market-product {
            border: 2px solid #dee2e6;
            border-radius: 15px;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .market-product:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
        }
        .card-img-top {
            border-radius: 10px;
            object-fit: cover;
            width: 100%;
            height: 200px;
        }
        .card-title {
            font-family: "Georgia", serif;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .card-text {
            font-family: Tahoma, sans-serif;
            font-size: 1.4rem;
            color: #FFD700;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);
        }

        .btn-primary {
    background-color: #007bff;
    color: #fff;
    font-size: 1.1rem;
    font-weight: bold;
    border-radius: 5px;
    padding: 2px 20px;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
}


    </style>

    <div class="col-md-3 ">
        <div class="card mb-4 market-product">
            <img src="' . htmlspecialchars($row["product_img"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="card-img-top p-3">
            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center tag-container">
                <h5 class="card-title">' . htmlspecialchars($row["product_name"]) . '</h5>
                <h5 class="card-text">Rs ' . htmlspecialchars(number_format($row["product_price"], 2)) . '</h5>
                <p class="card-text">
                    <span class="badge bg-success">Per ' . htmlspecialchars($row["unit"]) . '</span>
                </p>
                <a href="viewProduct.php?product_id=' . htmlspecialchars($row["product_id"]) . '&farm_id=' . htmlspecialchars($row["farm_id"]) . '" class="btn btn-primary">Buy</a>
            </div>
        </div>
    </div>';
                }
            } else {
                echo '<p>No products found.</p>';
            }
            ?>
        </div>
    </div>
</body>

</html>