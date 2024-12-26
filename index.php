<?php
require '_base.php';
include '_head.php';


auth('member');
$success = $_SESSION['flash_success'] ?? '';
$error = $_SESSION['flash_error'] ?? '';

$top_selling_arr = $_db->query('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_status=1 AND product_stock > 0 ORDER BY product_sold desc LIMIT 5')->fetchAll();

$lowest_price_arr = $_db->query('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_status=1 AND product_stock > 0 ORDER BY product_price asc LIMIT 5')->fetchAll();

// Clear flash messages after displaying them
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

//CART
$member_id = $user->member_id; 

$check_cart_exists_stm = $_db -> prepare('SELECT COUNT(*) FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
$check_cart_exists_stm -> execute([$member_id]); 

if($check_cart_exists_stm -> fetchColumn() == 0){
    /* If the member first time go into the cart page (The member don't have the cart before) */
    $create_cart_stm = $_db -> prepare('INSERT INTO cart (member_id) VALUES (?)');
    $create_cart_stm  -> execute([$member_id]); 
} else{
    /* The member have the cart before */
    $get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
    $get_cart_stm -> execute([$member_id]);
    $shoppingCart = $get_cart_stm -> fetch();
}

//If add to cart button is pressed
if(isset($_POST['add-to-cart'], $_POST['product_id'])){
    
    $productID = $_POST['product_id'];

    //Retrieve product details
    $find_product_stm = $_db -> prepare('SELECT * FROM product WHERE product_id = ?');
    $find_product_stm -> execute([$productID]);
    $productFound = $find_product_stm -> fetch();

    //Check the product is add to cart before
    $check_record_stm = $_db -> prepare('SELECT * FROM cart_product WHERE product_id = ? AND cart_id = ?');
    $check_record_stm -> execute([$productID, $shoppingCart->cart_id]);
    $check_result = $check_record_stm -> fetch();

    if($check_result == null){
        //If no entry before
        $add_product_stm = $_db -> prepare('INSERT INTO cart_product (cart_id, product_id, price, quantity) VALUES (?, ?, ?, ?)');
        $add_product_stm -> execute([$shoppingCart->cart_id, $productID, $productFound-> product_price, 1]); //HERE NEED TO CHANGE AFTERWARDS
        temp('info', 'Item added to cart.');
        redirect("index.php");
    } else if($check_result->quantity >= $productFound->product_stock) {
        //If the product is add to cart before (but the selected quantity >= stock)
        temp('info', 'The product quantity cannot greater than the current product stock.');
        redirect("index.php");
    } else{
        //If the product is add to cart before
        $update_quantity_stm = $_db -> prepare('UPDATE cart_product SET quantity = ? WHERE cart_id = ? AND product_id = ?');
        $update_quantity_stm -> execute([$check_result->quantity + 1, $shoppingCart->cart_id, $productID]);
        temp('info', 'Item added to cart.');
        redirect("index.php");
    }
    
}


$fullPath = $_SERVER['REQUEST_URI'];

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
        <?php foreach ($top_selling_arr as $s): ?>
            <div class="box">
                <img src="page/yongqiaorou/images/<?= $s->product_cover ?>" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>">
                <span data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>"><?= $s->category_name?></span>
                <h2 class="product-name" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>"><?= $s->product_name?></h2>
                <?php 
                    $get_cart_product = $_db->prepare('SELECT * FROM cart_product WHERE product_id = ? AND cart_id = ?');
                    $get_cart_product -> execute([$s->product_id, $shoppingCart->cart_id]);
                    $cartProductFound = $get_cart_product -> fetch();
                    if($cartProductFound != null){ ?>
                        <h2 class="selected" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>">Selected: <?= $cartProductFound->quantity?></h2>
                <?php  } else { ?>
                        <h2 class="selected"></h2>
                    <?php  }  ?>
                <h3 class="price" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>">RM <?= sprintf('%.2f', $s->product_price)?></h3>
                <form method="post">
                    <input hidden type="text" name="product_id" value="<?= $s->product_id ?>">
                    <input type="submit" name="add-to-cart" class= "add-to-cart" value="+">
                <i class='bx bx-heart' ></i></form>
                <span class="sold" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>"><?= $s->product_sold?> sold || <?= $s->product_stock?> left</span>
            </div>
            <?php endforeach ?>
        </div>

        <div class="heading">
            <h1>Lowest Price Products</h1>
        </div>

        <div class="products-container">
        <?php foreach ($lowest_price_arr as $s): ?>
            <div class="box">
                <img src="page/yongqiaorou/images/<?= $s->product_cover ?>" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>">
                <span data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>"><?= $s->category_name?></span>
                <h2 class="product-name" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>"><?= $s->product_name?></h2>
                <?php 
                    $get_cart_product = $_db->prepare('SELECT * FROM cart_product WHERE product_id = ? AND cart_id = ?');
                    $get_cart_product -> execute([$s->product_id, $shoppingCart->cart_id]);
                    $cartProductFound = $get_cart_product -> fetch();
                    if($cartProductFound != null){ ?>
                        <h2 class="selected" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>">Selected: <?= $cartProductFound->quantity?></h2>
                    <?php  } else { ?>
                        <h2 class="selected"></h2>
                <?php  }  ?>
                <h3 class="price" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>">RM <?= sprintf('%.2f', $s->product_price)?></h3>
                <form method="post">
                    <input hidden type="text" name="product_id" value="<?= $s->product_id ?>">
                    <input type="submit" name="add-to-cart" class= "add-to-cart" value="+">
                <i class='bx bx-heart' ></i></form>
                <span class="sold" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>"><?= $s->product_sold?> sold || <?= $s->product_stock?> left</span>
            </div>
            <?php endforeach ?>
        </div>
            
    </section>

</body>
</html>

<?php
include '_foot.php';
?>
