<?php
require '_base.php';

include '_head.php';

$id = req('id');

$stm = $_db->prepare('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_id = ?'); 
$stm->execute([$id]);
$s = $stm->fetch();

if (!$s) { 
    redirect('/');
}

?>

<title><?= $s -> product_name ?></title>
<link rel="stylesheet" href="css/productinfo.css">
<script src="js/productinfo.js" defer></script>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<body>
    <div class="card-wrapper">
        <div class="card">
            <!--card left-->
            <div class="product-images">
                <div class="image-display">
                    <div class="image-showcase">
                        <img src="images/<?= $s -> product_img ?>">
                    </div>
                </div>
                <!-- <div class="image-select">
                    <div class="image-item">
                        <a href="#" data-id="1">
                            <img src="images/biscuit1.png">
                        </a>
                    </div>
                    <div class="image-item">
                        <a href="#" data-id="2">
                            <img src="images/biscuit1.png">
                        </a>
                    </div>
                </div> -->
            </div>
            <!--card right-->
            <div class = "product-content">
                <span class = "product-title"><?= $s -> product_name ?></span>

                <span class = "product-category" data-get="productsearch.php?product_name=&category_id=<?= $s->category_id ?>">Category: <?= $s -> category_name ?></span>

                <div class= "product-price">
                    RM <?= $s -> product_price ?>
                </div>

                <div class= "product-quantity">
                    <?= $s -> product_sold ?> sold || <?= $s -> product_stock ?> left
                </div>

                <div class = "product-desc">
                    <span>Product information:</span>
                    <p><?= $s -> product_desc ?></p>
                </div>

                
                <div class="purchase-info">
                    <button class="decrease" onclick="decreaseValue()">-</button>
                    <input type="number" id="spinnerValue" value="1" min="1" max="<?= $s -> product_stock ?>" step="1">
                    <button class="increase" onclick="increaseValue()">+</button>
                    <button type = "button" class="add-to-cart">Add to Cart<i class='bx bx-cart-alt' ></i></button>

                </div>

            </div>

        </div>
    </div>
</body>

<?php
include '_foot.php';