<?php
require '_base.php';

include '_head.php';
?>

<title>Product Info</title>
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
                        <img src="images/biscuit1.png">
                        <img src="images/biscuit1.png">
                    </div>
                </div>
                <div class="image-select">
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
                </div>
            </div>
            <!--card right-->
            <div class = "product-content">
                <span class = "product-title">Julie's Sour and Cream & Onion Sandwich</span>

                <span class = "product-category">Category: Food</span>

                <div class= "product-price">
                    RM 10.00
                </div>

                <div class = "product-desc">
                    <span>Product information:</span>
                    <p>Indulge in the creamy and tangy delight of Julie's Sour Cream Sandwich, a delectable treat perfect for satisfying your sweet cravings. Each 280g pack features delightful sandwich biscuits generously filled with a luscious layer of smooth sour cream-flavored cream. The combination of the buttery, golden-baked biscuits and the indulgent sour cream filling creates a harmonious balance of flavors and textures. Ideal for snacking, sharing, or accompanying your favorite hot or cold beverages, Julie's Sour Cream Sandwich promises a moment of pure indulgence in every bite. Elevate your snack time with these delightful sandwich biscuits that bring together the richness of sour cream and the irresistible crunch of golden biscuits, making them a delightful addition to any occasion.</p>
                </div>

                
                <div class="purchase-info">
                    <button class="decrease" onclick="decreaseValue()">-</button>
                    <input type="number" id="spinnerValue" value="1" min="1" max="99" step="1">
                    <button class="increase" onclick="increaseValue()">+</button>
                    <button type = "button" class="add-to-cart">Add to Cart<i class='bx bx-cart-alt' ></i></button>

                </div>

            </div>

        </div>
    </div>
</body>

<?php
include '_foot.php';