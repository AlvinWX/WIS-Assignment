<?php
require '_base.php';
include '_head.php';

auth();
$user = $_SESSION['user'] ?? null; 
$success = $_SESSION['flash_success'] ?? '';
$error = $_SESSION['flash_error'] ?? '';

$top_selling_arr = $_db->query('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_status=1 AND product_stock > 0 ORDER BY product_sold desc LIMIT 5')->fetchAll();
$lowest_price_arr = $_db->query('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_status=1 AND product_stock > 0 ORDER BY product_price asc LIMIT 5')->fetchAll();
$user = $_SESSION['user'] ?? null;
$member_id = $user->member_id;
$success = "";
$error = "";

if(!empty($member_id)){
    $success = $_SESSION['flash_success'] ?? '';
    $error = $_SESSION['flash_error'] ?? '';

// Clear flash messages after displaying them
unset($_SESSION['flash_success'], $_SESSION['flash_error']);


//CART
$check_cart_exists_stm = $_db -> prepare('SELECT COUNT(*) FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
$check_cart_exists_stm -> execute([$member_id]); 

if($check_cart_exists_stm -> fetchColumn() == 0){
    /* If the member first time go into the page (The member don't have the cart before) */
    $create_cart_stm = $_db -> prepare('INSERT INTO cart (member_id) VALUES (?)');
    $create_cart_stm  -> execute([$member_id]); 
}

/* The member have the cart before */
$get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
$get_cart_stm -> execute([$member_id]);
$shoppingCart = $get_cart_stm -> fetch();

//WISHLIST
$check_wishlist_exists_stm = $_db -> prepare('SELECT COUNT(*) FROM wishlist w JOIN member m ON m.member_id = w.member_id WHERE w.member_id = ?');
$check_wishlist_exists_stm -> execute([$member_id]);

if($check_wishlist_exists_stm -> fetchColumn() == 0){
    /* If the member first time go into the page (The member don't have the wishlist before) */
    $create_wishlist_stm = $_db -> prepare('INSERT INTO wishlist (member_id) VALUES (?)');
    $create_wishlist_stm -> execute([$member_id]);
} 

/* The member have the wishlist before */
$get_wishlist_stm = $_db -> prepare('SELECT * FROM wishlist w JOIN member m ON m.member_id = w.member_id WHERE w.member_id = ?');
$get_wishlist_stm -> execute([$member_id]);
$wishlist = $get_wishlist_stm -> fetch();

//VOUCHER LIST
$check_voucher_list_exists_stm = $_db -> prepare('SELECT COUNT(*) FROM voucher_list v JOIN member m ON m.member_id = v.member_id WHERE v.member_id = ?');
$check_voucher_list_exists_stm -> execute([$member_id]);

if($check_voucher_list_exists_stm -> fetchColumn() == 0){
    /* If the member first time go into the page (The member don't have the voucher list before) */
    $create_voucherlist_stm = $_db -> prepare('INSERT INTO voucher_list (member_id) VALUES (?)');
    $create_voucherlist_stm -> execute([$member_id]);
}

$fullPath = $_SERVER['REQUEST_URI'];

$_SESSION['path_details'] = $fullPath;

}

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
    <link rel="stylesheet" href="css/flash.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="js/imageslider.js" defer></script>
    <script src="js/wishlist.js" defer></script>
    <script src="js/app.js" defer></script>
    <style>
        .empty-box{
            margin: 35px;
            padding: 35px;
        }
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

<div id="info"><?= temp('info')?></div>

<?php if ($success): ?>
    <div class="flash-message flash-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="flash-message flash-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="empty-box"></div>
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
        <?php $wishlistId = $member_id!=null?$wishlist->wishlist_id:null; ?>
        <?php foreach ($top_selling_arr as $s): ?>
            <div class="box">
                <img src="images/product_pic/<?= $s->product_cover ?>" data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>">
                <span data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>"><?= $s->category_name?></span>
                <h2 class="product-name" data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_name?></h2>
                <?php 
                    if(!empty($member_id)){
                        $get_cart_product = $_db->prepare('SELECT * FROM cart_product WHERE product_id = ? AND cart_id = ?');
                        $get_cart_product -> execute([$s->product_id, $shoppingCart->cart_id]);
                        $cartProductFound = $get_cart_product -> fetch();
                        if($cartProductFound != null){ ?>
                            <h2 class="selected" data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>">Selected: <?= $cartProductFound->quantity?></h2>
                    <?php  } else { ?>
                            <h2 class="selected"></h2>
                        <?php  }
                    }?>
                    
                    <h3 class="price" data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>">RM <?= sprintf('%.2f', $s->product_price) ?></h3>
                    <a class= "add-to-cart" href="page/leewaixian/addquantity.php?id=<?= $s->product_id ?>"><i class="bx bx-cart-alt"></i></a>
                    <?php
                    if(!empty($member_id)){
                        $check_wishlist_stm = $_db->prepare('SELECT COUNT(*) FROM wishlist_product WHERE wishlist_id = ? AND product_id = ?');
                        $check_wishlist_stm->execute([$wishlist->wishlist_id, $s->product_id]);
                        $isWished = $check_wishlist_stm->fetchColumn() == 0 ? false : true;
                    }
                    ?>
                    <svg class='bx bx-heart' viewBox='0 0 24 24' width='24' height='24' onclick="updateWishlist('<?= $s->product_id ?>', '<?= $isWished ? 'remove' : 'add' ?>', '<?= $wishlistId ?>' , this)">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="<?= $isWished ? '#ff007f' : 'none' ?>" stroke="#ff007f" stroke-width="2"/>
                    </svg>
                <span class="sold" data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_sold?> sold || <?= $s->product_stock?> left</span>
            </div>
            <?php endforeach ?>
        </div>

        <div class="heading">
            <h1>Lowest Price Products</h1>
        </div>

        <div class="products-container">
        <?php foreach ($lowest_price_arr as $s): ?>
            <div class="box">
                <img src="images/product_pic/<?= $s->product_cover ?>" data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>">
                <span data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>"><?= $s->category_name?></span>
                <h2 class="product-name" data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_name?></h2>
                <?php 
                    if(!empty($member_id)){
                        $get_cart_product = $_db->prepare('SELECT * FROM cart_product WHERE product_id = ? AND cart_id = ?');
                        $get_cart_product -> execute([$s->product_id, $shoppingCart->cart_id]);
                        $cartProductFound = $get_cart_product -> fetch();
                        if($cartProductFound != null){ ?>
                            <h2 class="selected" data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>">Selected: <?= $cartProductFound->quantity?></h2>
                    <?php  } else { ?>
                            <h2 class="selected"></h2>
                        <?php  }
                    }?>
                    
                    <h3 class="price" data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>">RM <?= sprintf('%.2f', $s->product_price) ?></h3>
                    <a class= "add-to-cart" href="page/leewaixian/addquantity.php?id=<?= $s->product_id ?>"><i class="bx bx-cart-alt"></i></a>
                    <?php
                    if(!empty($member_id)){
                        $check_wishlist_stm = $_db->prepare('SELECT COUNT(*) FROM wishlist_product WHERE wishlist_id = ? AND product_id = ?');
                        $check_wishlist_stm->execute([$wishlist->wishlist_id, $s->product_id]);
                        $isWished = $check_wishlist_stm->fetchColumn() == 0 ? false : true;
                    }
                    ?>
                    <svg class='bx bx-heart' viewBox='0 0 24 24' width='24' height='24' onclick="updateWishlist('<?= $s->product_id ?>', '<?= $isWished ? 'remove' : 'add' ?>', '<?= $wishlistId ?>' , this)">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="<?= $isWished ? '#ff007f' : 'none' ?>" stroke="#ff007f" stroke-width="2"/>
                    </svg>
                <span class="sold" data-get="page/leewaixian/productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_sold?> sold || <?= $s->product_stock?> left</span>
            </div>
            <?php endforeach ?>

        </div>
            
    </section>

</body>
</html>

<?php
include '_foot.php';
?>
