<?php

    //Retrieve member cart
    $get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.memberID = c.member_id WHERE c.member_id = ?');
    $get_cart_stm -> execute(["MB00001"]); //HERE NEED TO CHANGE AFTERWARDS
    $shoppingCart = $get_cart_stm -> fetch();

    //Retrieve added to cart already items
    $get_products_stm = $_db -> prepare('SELECT * FROM cart_product WHERE cart_id = ?');
    $get_products_stm -> execute([$shoppingCart->cart_id]); 
    $cart_products = $get_products_stm -> fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title><?= $_title ?? 'Untitled' ?></title> -->
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/css/wj_css.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat+Underline:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Ubuntu+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
</head>

<style>
.right-logo .quantity{
    background: #dc4c32;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    font-size: 12px;
    font-weight: 800;
    position: absolute;
    right: 0; 
    top: 0; 
    transform: translate(-50%, -25%); 
    padding: 5px;
    width: 18px; 
    height: 18px; 
}

.right-logo a {
    position: relative;
}
</style>

<body>
    

    <header>
        <div class="home-logo">
            <a href="/">
                <img src="/images/favicon.png" alt="Clickable Image">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <!-- Dropdown Menu -->
                <li>
                    <a href="#">More</a>
                    <div class="dropdown-content">
                        <a href="#">XXXXX</a>
                        <a href="/page/chanyijing/admin/member_management/member_list.php">Member Management</a>
                        <a href="/page/chanyijing/admin/admin_management/admin_list.php">Admin Management</a>
                    </div>
                </li>
            </ul>
        </nav>
        <div class="right-logo">
            
            <a href="/productsearch.php?product_name=&category_id=&minprice=&maxprice=&sort=product_name&dir=asc">
                <img class="search" src="/images/search.png" alt="Search Icon" id="search-icon">
            </a>

            <a href="/shoppingcart.php">
                <img src="/images/shopping-cart.png" alt="Shopping Cart">
                <span class="quantity"><?= count($cart_products) ?></span>
            </a>

            <a href="/login.php">
                <img src="/images/user.png" alt="Clickable Image">
            </a>
        </div>
        <div class="search-container" id="search-container">
            <input type="text" class="search-bar" placeholder="Search...">
        </div>
    </header>

    <!-- <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>
    </main> -->
</body>
</html>
