<?php
require '_base.php';

$_title = 'Index';
include '_head.php';
?>



<title>Welcome to TAR Grocer</title>
<link rel="stylesheet" href="css/imageslider.css">
<link rel="stylesheet" href="css/home.css">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<script src="js/imageslider.js" defer></script>
<body>
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
            <a href="productdetail.php">GO!!!</a>
            <h1>Top Selling Products</h1>
        </div>

        <div class="products-container">
            <div class="box">
                <img src="images/biscuit1.png">
                <span>Food</span>
                <h2>Julie's Sour and Cream & Onion Sandwich</h2>
                <h3 class="price">10.00 <span>/packet</span></h3>
                <i class='bx bx-cart-alt'></i>
                <i class='bx bx-heart' ></i>
                <span class="sold">4k sold</span>
            </div>

            <div class="box">
                <img src="images/biscuit1.png">
                <span>Food</span>
                <h2>Julie's Sour and Cream & Onion Sandwich</h2>
                <h3 class="price">10.00 <span>/packet</span></h3>
                <i class='bx bx-cart-alt' ></i>
                <i class='bx bx-heart'> </i>
                <span class="sold">4k sold</span>
            </div>

            <div class="box">
                <img src="images/biscuit1.png">
                <span>Food</span>
                <h2>Julie's Sour and Cream & Onion Sandwich</h2>
                <h3 class="price">10.00 <span>/packet</span></h3>
                <i class='bx bx-cart-alt' ></i>
                <i class='bx bx-heart' ></i>
                <span class="sold">4k sold</span>
            </div>

            <div class="box">
                <img src="images/biscuit1.png">
                <span>Food</span>
                <h2>Julie's Sour and Cream & Onion Sandwich</h2>
                <h3 class="price">10.00 <span>/packet</span></h3>
                <i class='bx bx-cart-alt' ></i>
                <i class='bx bx-heart' ></i>
                <span class="sold">4k sold</span>
            </div>

            <div class="box">
                <img src="images/biscuit1.png">
                <span>Food</span>
                <h2>Julie's Sour and Cream & Onion Sandwich</h2>
                <h3 class="price">10.00 <span>/packet</span></h3>
                <i class='bx bx-cart-alt' ></i>
                <i class='bx bx-heart' ></i>
                <span class="sold">4k sold</span>
            </div>

            </div>

    </section>

</body>

<?php
include '_foot.php';