<?php
require '_base.php';
include '_head.php';

//Retrieve member cart
$member_id = $user->memberID; 

$get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.memberID = c.member_id WHERE c.member_id = ?');
$get_cart_stm -> execute([$member_id]); 
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
    <link rel="stylesheet" href="css/shoppingcart.css">
    <link rel="stylesheet" href="css/flash.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="js/shoppingcart.js" defer></script>
    <title>My Shopping Cart</title>
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
            <h1>My Shopping Cart</h1>
            <P>Total <?= count($cart_products) ?> product(s) added to the cart. </P>
        </div>

        <?php if(count($cart_products)==0){ ?>
            <h2 class="no-products">No products added.</h2>
        <?php } $i = 0; $subtotal=0; ?>

        <?php foreach ($cart_products as $a): 
                $value_id = "spinnerValue{$i}"; $price_id = "multipliedPrice{$i}";
                $get_product_detail_stm = $_db->prepare('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_id = ?');
                $get_product_detail_stm -> execute([$a->product_id]);
                $s = $get_product_detail_stm -> fetch();
            ?>
            <div class="box">
                <div class="product-image"><img src="page/yongqiaorou/images/<?= $s->product_cover ?>"></div>
                <div class="product-content">
                    <span><?= $s->category_name?></span>
                    <h2 class="product-name"><?= $s->product_name?></h2>
                    <span class="sold"><?= $s->product_sold?> sold || <?= $s->product_stock?> left</span>
                    <div class="purchase-info">
                        <button class="remove" onclick="confirmDelete('<?= $s->product_id ?>', '<?= $a->cart_id ?>', 'cart')">Remove</button>
                        <button class="decrease" onclick="decreaseValue(<?= $i ?>); updatePrice(<?= $s->product_price ?>, <?= $i ?>)">-</button>
                        <form method="post" id="quantitySelect"><input disabled type="number" name="quantity" id="<?= $value_id ?>" value="<?= $a->quantity ?>" min="1" max="<?= $s -> product_stock ?>" step="1" data-product-id="<?= $s->product_id ?>" 
                        data-cart-id="<?= $a->cart_id ?>"></form>
                        <button class="increase" onclick="increaseValue(<?= $i ?>); updatePrice(<?= $s->product_price ?>, <?= $i ?>)">+</button>
                        <input form="quantitySelect" hidden type="text" name="product_id" value="<?= $s->product_id ?>">
                        <div hidden><?= $i++;$subtotal+=  $s->product_price * $a -> quantity?></div>
                    </div>
                </div>
                <div class="multipliedPrice" id="<?= $price_id ?>"><h3 class="price">RM <?= sprintf('%.2f', $s->product_price * $a -> quantity)?></h3></div>
            </div>
            <?php endforeach ?>

            <?php if(count($cart_products)>0){ ?>
                <div class="cart-subtotal">
                    <h1 class="subtitle">Cart subtotal:</h1>
                    <h3 class="price">RM <?= sprintf('%.2f', $subtotal) ?></h3>
                </div>
            
                <div class="action-button">
                    <button class="clear" onclick="confirmClearCart('<?= $a->cart_id ?>')">Clear Cart</button>
                    <button class="checkout" onclick="window.location.href='/checkout.php';">Checkout</button>
                </div>
            <?php } $i = 0; ?>

    </section>    


</body>
</html>

<?php
include '_foot.php';
?>