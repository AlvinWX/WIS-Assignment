<?php
require '_base.php';
include '_head.php';

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
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="css/flash.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="js/shoppingcart.js" defer></script>
    <title>Checkout</title>
</head>
<script>
window.onload = function() {
    document.getElementById('spinnerValue0').blur();
};
</script>
<body>
    <div id="info"><?= temp('info')?></div>
    <section class="cart-display">
    <div class="heading">
        <h1>Products Ordered</h1>
    </div>
    <div class="order-table">
        <div class="table-header">
            <div class="header-item">Product</div>
            <div class="header-item">Unit Price</div>
            <div class="header-item">Amount</div>
            <div class="header-item">Total</div>
        </div>
        <?php foreach ($cart_products as $a): 
            $get_product_detail_stm = $_db->prepare('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_id = ?');
            $get_product_detail_stm -> execute([$a->product_id]);
            $s = $get_product_detail_stm -> fetch();
        ?>
        <div class="table-row">
        <div class="product-item">
            <img src="images/<?= htmlspecialchars($s->product_cover) ?>" alt="<?= htmlspecialchars($s->product_name) ?>">
            <div class="product-text">
                <span class="category-name"><?= htmlspecialchars($s->category_name) ?></span>
                <h2 class="product-name"><?= htmlspecialchars($s->product_name) ?></h2>
            </div>
        </div>

            <div class="price-item">RM <?= number_format($s->product_price, 2) ?></div>
            <div class="amount-item"><?= $a->quantity ?></div>
            <div class="total-item">RM <?= number_format($s->product_price * $a->quantity, 2) ?></div>
        </div>
        <?php endforeach ?>
    </div>
</section>
 


</body>
</html>

<?php
include '_foot.php';
?>