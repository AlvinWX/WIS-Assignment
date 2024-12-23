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

//Retrieve shipping address
$get_address_stm = $_db -> prepare('SELECT * FROM `address` WHERE member_id = ?');
$get_address_stm -> execute([$member_id]); 
$addresses = $get_address_stm -> fetchAll();

//One of the payment method is pressed
if(isset($_POST['submit'], $_POST['address'])){

    $paymentMethod = $_POST['submit'];
    $shippingAddress = (int)$_POST['address'];

    //$create_order_stm = $_db -> prepare('');

    //If the payment method is card
    if($paymentMethod == 'Payment Card'){

    } else{//If the payment method is Stripe

    }


} else if(isset($_POST['submit'])) {
    temp('info', 'Please select a shipping address.');
}
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
        <?php $order_subtotal=0;foreach ($cart_products as $a): 
            $get_product_detail_stm = $_db->prepare('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_id = ?');
            $get_product_detail_stm -> execute([$a->product_id]);
            $s = $get_product_detail_stm -> fetch();
        ?>
        <div class="table-row">
        <div class="product-item">
            <img src="images/<?= $s->product_cover ?>" alt="<?= $s->product_name ?>">
            <div class="product-text">
                <span class="category-name"><?= $s->category_name ?></span>
                <h2 class="product-name"><?= $s->product_name ?></h2>
            </div>
        </div>

            <div class="price-item">RM <?= number_format($s->product_price, 2) ?></div>
            <div class="amount-item"><?= $a->quantity ?></div>
            <div class="total-item">RM <?= number_format($s->product_price * $a->quantity, 2);  ?></div>
            <div hidden><?= $order_subtotal += ($s->product_price * $a->quantity)?></div>
        </div>
        <?php endforeach ?>
        <div class="table-header">
            <div class="header-item">Order Subtotal</div>
            <div class="header-item"></div>
            <div class="header-item"></div>
            <div class="header-item">RM <?= number_format($order_subtotal, 2) ?></div>
        </div>
        <div class="table-header">
            <div class="header-item">SST (6%)</div>
            <div class="header-item"></div>
            <div class="header-item"></div>
            <div class="header-item">RM <?= number_format($order_subtotal*0.06, 2) ?></div>
            <div hidden><?= $subtotal += ($order_subtotal*0.06)?></div>
        </div>
        <div class="table-header">
            <div class="header-item">Delivery Fees</div>
            <div class="header-item"></div>
            <div class="header-item"></div>
            <div class="header-item">RM <?= number_format(5, 2) ?></div>
            <div hidden><?= $subtotal += $order_subtotal + 5?></div>
        </div>
        <div class="table-header">
            <div class="header-item">Subtotal</div>
            <div class="header-item"></div>
            <div class="header-item"></div>
            <div class="header-item">RM <?= number_format($subtotal, 2) ?></div>
        </div>
    </div>
</section>

<section class ="voucher-apply">
<div class="heading">
        <h1>Apply Voucher</h1>
    </div>
</section>
 
<section class ="shipping-address">
    <div class="heading">
        <h1>Choose Shipping Address</h1>
    </div>
    <div class="address-select">
        <?php $id = 0; foreach ($addresses as $s): ?>
            <div class="address-box">
                <label for="<?= $id ?>">
                    <span class="sequence"><?= $id + 1 ?></span>
                    <input type="radio" id="<?= $id ?>" name="address" form="payment" value="<?= $id ?>">
                    <div class="address-details">
                        <?= $s->street ?>, <?= $s->postcode ?>, <?= $s->city ?>, <?= $s->state ?>
                    </div>
                    <div hidden><?= $id++ ?></div>
                </label>
            </div>
        <?php endforeach ?>
    </div>
</section>

<section class ="final-total">
    <div class="heading">
        <h1>Final Amount to Pay</h1>
    </div>
    <div class="final-amount">
        <div class="amount-row">
            <span class="subtitle">Subtotal:</span>
            <span class="value">RM <?= number_format($subtotal, 2) ?></span>
        </div>
        <div class="amount-row">
            <span class="subtitle">Discount:</span>
            <span class="value">- RM <?= number_format(0, 2) ?></span>
        </div>
        <div class="amount-row">
            <span class="subtitle">Total Amount to Pay:</span>
            <span class="value">RM <?= number_format($subtotal, 2) ?></span>
        </div>
        <div class="amount-row">
            <span class="subtitle">Points earned:</span>
            <span class="value"><?= floor($subtotal) ?> points</span>
        </div>
    </div>

    <div class="heading">
        <h1>Payment Method</h1>
    </div>

    <div class="action-button">
        <form method="post" id="payment"></form>
        <input type="submit" form="payment" class="fakecard" name="submit" value="Payment Card">
        <input type="submit" form="payment" class="stripe" name="submit" value="Stripe">
        <button>Coming Soon</button>
     </div>
</section>


</body>
</html>

<?php
include '_foot.php';
?>