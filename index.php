<?php
require '_base.php';

session_start();
$success = $_SESSION['flash_success'] ?? '';
$error = $_SESSION['flash_error'] ?? '';

$top_selling_arr = $_db->query('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_status=1 ORDER BY product_sold DESC LIMIT 5')->fetchAll();

// Clear flash messages after displaying them
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
include '_head.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/imageslider.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/productbox.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="js/imageslider.js" defer></script>
    <script src="js/app.js" defer></script>
    <style>
        .flash-message {
            padding: 15px;
            border-radius: 5px;       
            margin-bottom: 20px;
            text-align: center;
        }

        .flash-success {
            background-color: #d4edda;
            color: #155724;
        }

        .flash-error {
            background-color: #f8d7da;
            color: #721c24;
        }


        @import url('https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poetsen+One&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
    </style>
</head>
<body>

<?php if ($success): ?>
    <div class="flash-message flash-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="flash-message flash-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class = "slide-container">
        
        <div class="slides">
            <img src="images/temp1.png" class = "image active">
            <img src="images/temp2.png" class = "image">
            <img src="images/temp3.png" class = "image">
        </div>

        <div class="buttons">
            <span class="next">&#10095;</span>
            <span class="prev">&#10094;</span>
        </div>

        <div class="dotsContainer">
			<div class="dot active" attr='0' onclick="switchImage(this)"></div>
			<div class="dot" attr='1' onclick="switchImage(this)"></div>
			<div class="dot" attr='2' onclick="switchImage(this)"></div>
        </div>

    </div>

    <section class="products" id="products">
        <div class="heading">
            <h1>Top Selling Products</h1>
        </div>

        <div class="products-container">
        <?php foreach ($top_selling_arr as $s): ?>
            <div class="box" data-get="productinfo.php?id=<?= $s->product_id ?>">
                <img src="images/biscuit1.png" data-get="productinfo.php?id=<?= $s->product_id ?>">
                <span data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->category_name?></span>
                <h2 data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_name?></h2>
                <h3 class="price" data-get="productinfo.php?id=<?= $s->product_id ?>">RM <?= $s->product_price?></h3>
                <i class='bx bx-cart-alt' onclick="" ></i>
                <i class='bx bx-heart' ></i>
                <span class="sold" data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_sold?> sold</span>
            </div>
            <!-- <button data-get="productinfo.php?id=<?= $s->product_id ?>">Detail</button> -->

            <!-- <div class="box">
                <img src="images/biscuit1.png">
                <span>Food</span>
                <h2>Julie's Sour and Cream & Onion Sandwich</h2>
                <h3 class="price">RM 10.00</h3>
                <i class='bx bx-cart-alt' ></i>
                <i class='bx bx-heart'> </i>
                <span class="sold">4k sold</span>
            </div>

            <div class="box">
                <img src="images/biscuit1.png">
                <span>Food</span>
                <h2>Julie's Sour and Cream & Onion Sandwich</h2>
                <h3 class="price">RM 10.00</h3>
                <i class='bx bx-cart-alt' ></i>
                <i class='bx bx-heart' ></i>
                <span class="sold">4k sold</span>
            </div>

            <div class="box">
                <img src="images/biscuit1.png">
                <span>Food</span>
                <h2>Julie's Sour and Cream & Onion Sandwich</h2>
                <h3 class="price">RM 10.00</h3>
                <i class='bx bx-cart-alt' ></i>
                <i class='bx bx-heart' ></i>
                <span class="sold">4k sold</span>
            </div>

            <div class="box">
                <img src="images/biscuit1.png">
                <span>Food</span>
                <h2>Julie's Sour and Cream & Onion Sandwich</h2>
                <h3 class="price">RM 10.00</h3>
                <i class='bx bx-cart-alt' ></i>
                <i class='bx bx-heart' ></i>
                <span class="sold">4k sold</span>
            </div>

            <div class="box">
                <img src="images/biscuit1.png">
                <span>Food</span>
                <h2>Julie's Sour and Cream & Onion Sandwich</h2>
                <h3 class="price">RM 10.00</h3>
                <i class='bx bx-cart-alt' ></i>
                <i class='bx bx-heart' ></i>
                <span class="sold">4k sold</span>
            </div> -->
            <?php endforeach ?>
            </div>
            

    </section>

</body>
</html>

<?php
include '_foot.php';
?>
