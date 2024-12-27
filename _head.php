<?php
    $user = $_SESSION['user'] ?? null; 
    if($user && $user->userType == 'member'){
        $member_id = $user->member_id; 

        //Retrieve member cart
        $get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
        $get_cart_stm -> execute([$member_id]);
        $shoppingCart = $get_cart_stm -> fetch();

        //Retrieve added to cart already items
        $get_products_stm = $_db -> prepare('SELECT * FROM cart_product WHERE cart_id = ?');
        $get_products_stm -> execute([$shoppingCart->cart_id]); 
        $cart_products = $get_products_stm -> fetchAll();
    }
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
    <script src="/js/wj_app.js"></script>
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
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>
    <header>
        <div class="home-logo">
            <a href="/">
                <img src="/images/favicon.png" alt="Clickable Image">
            </a>
        </div>
        <nav>
    <ul>
        <!-- Show Home link only if the user is not an admin -->
        <?php if (!$user || $user->userType != 'admin'): ?>
            <li><a href="/index.php">Home</a></li>
        <?php endif; ?>

        <!-- Show logout and other admin-related links only if the user is logged in -->
        <?php if ($user): ?>
            <?php if ($user->userType == 'admin'): ?>
                <li><a href="/page/yongqiaorou/product.php">Product Management</a></li>
                        <li><a href="/page/yongqiaorou/category.php">Category Management</a></li>
                        <li><a href="/page/yongqiaorou/voucher.php">Voucher Management</a></li>
                <li><a href="/page/chanyijing/admin/order_management/order_list.php">Order Listing</a></li>
                <li><a href="#">Member Management</a>
                    <div class="dropdown-content">
                        <a href="/page/chanyijing/admin/member_management/member_list.php">Member List</a>
                        <a href="/page/chanyijing/admin/feedback/member_feedback.php">Member Feedback</a>
                        <a href="/page/chanyijing/admin/admin_management/admin_list.php">Admin Management</a>
                    <?php endif ?>
                <?php else: ?>
                    <li><a href="/login.php">Login</a></li>
                <?php endif ?>
                <?php if ($user): ?>
                <?php if ($user->userType == 'member'): ?>
                        <li><a href="/page/leewaixian/productsearch.php?product_name=&category_id=&minprice=&maxprice=&sort=product_name&dir=asc">View Products</a></li>
                        <li><a href="/page/chanyijing/member/order_history/history_list.php">Order History</a></li>
                        <?php endif ?>
                        <?php endif ?>

            </ul>
        </nav>
        <div class="right-logo">
            <?php if ($user && $user->userType == 'member'): ?>
                <a href="/page/leewaixian/wishlist.php">
                    <img class="search" src="/images/love.png" alt="Wishlist" id="search-icon">
                </a>
                <a href="/page/leewaixian/shoppingcart.php">
                    <img src="/images/shopping-cart.png" alt="Shopping Cart">
                    <?php if(count($cart_products)>0): ?>
                        <span class="quantity"><?= count($cart_products) ?></span>
                    <?php endif ?>
                </a>
            <?php endif ?>
            <?php if ($user): ?>
                <div class="dropdown">
                    <img src="/images/user.png" alt="User Icon" class="user-icon">
                    <div class="dropdown-content">
                        <a href="/user/profile.php">Profile</a>
                        <a href="/logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login.php">
                    <img src="/images/user.png" alt="Login">
                </a>
            <?php endif ?>
        </div>
        <div class="search-container" id="search-container">
            <input type="text" class="search-bar" placeholder="Search...">
        </div>
    </header>
</body>
</html>
