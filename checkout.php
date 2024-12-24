<?php
require '_base.php';
include '_head.php';

//Retrieve member cart
$member_id = $user->member_id; 

$get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
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
if(isset($_POST['submit'], $_POST['address'], $_POST['order_subtotal'], $_POST['tax'], $_POST['delivery_fee'], $_POST['subtotal'], $_POST['discount'], $_POST['total'], $_POST['points'])){

    //Get address value
    $paymentMethod = $_POST['submit'];
    $addressID = (int)$_POST['address'];
    $shippingAddress = $addresses[$addressID];

    $order_subtotal = (double)$_POST['order_subtotal'];
    $tax = (double)$_POST['tax'];
    $delivery_fee = (double)$_POST['delivery_fee'];
    $subtotal = (double)$_POST['subtotal'];
    $discount = (double)$_POST['discount'];
    $total = (double)$_POST['total'];
    $points = (int)$_POST['points'];


    //Insert new record into the shipping address table
    $create_shipping_address_stm = $_db -> prepare('INSERT INTO shipping_address
    (street, postcode, city, `state`) 
    VALUES (?, ?, ?, ?)');
    $create_shipping_address_stm -> execute([$shippingAddress->street, $shippingAddress->postcode, $shippingAddress->city, $shippingAddress->state]); 

    //Get the shipping address id
    $get_shipping_address_stm = $_db -> prepare('SELECT * FROM shipping_address
    WHERE street = ? AND postcode = ? AND city = ? AND `state` = ? ');
    $get_shipping_address_stm -> execute([$shippingAddress->street, $shippingAddress->postcode, $shippingAddress->city, $shippingAddress->state]); 
    $ad = $get_shipping_address_stm -> fetch();

    //Insert new record into the order table
    $create_order_stm = $_db -> prepare('INSERT INTO `order`
    (order_subtotal, tax, delivery_fee, subtotal, 
    voucher, discount_price, total, points, 
    order_date, ship_date, received_date, 
    order_status, shipping_address_id, member_id) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    // Create DateTime objects and format them to SQL datetime format
    $orderDate = (new DateTime())->format('Y-m-d H:i:s');
    $shipDate = (new DateTime('+1 day'))->format('Y-m-d 0:0:0');
    $receivedDate = (new DateTime('+3 days'))->format('Y-m-d 0:0:0');

    $create_order_stm->execute([
        $order_subtotal, $tax, $delivery_fee, $subtotal,
        'Voucher', $discount, $total, $points,
        $orderDate, $shipDate, $receivedDate,
        'Pending', $ad->shipping_address_id, $user->memberID
    ]);

    //Get the order id
    $get_order_stm = $_db -> prepare('SELECT * FROM `order` WHERE member_id = ? AND order_date = ?');
    $get_order_stm -> execute([$user->memberID, $orderDate]); 
    $id = $get_order_stm -> fetch();

    //Insert new record into the order_product table
    foreach ($cart_products as $a){
        $create_order_product_stm = $_db -> prepare('INSERT INTO order_product 
        (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)');
        $create_order_product_stm -> execute([$id->order_id, $a-> product_id, $a->price, $a->quantity]);
    }

    //Clear shopping cart
    $clear_order_cart = $_db -> prepare('DELETE FROM cart_product WHERE cart_id = ?');
    $clear_order_cart -> execute([$shoppingCart-> cart_id]); 

    //If the payment method is card
    if($paymentMethod == 'Payment Card'){

    } else{//If the payment method is Stripe
        
    }

    redirect('index.php');

} else if(isset($_POST['submit'], $_POST['order_subtotal'], $_POST['tax'], $_POST['delivery_fee'], $_POST['subtotal'], $_POST['discount'], $_POST['total'], $_POST['points'])) {
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
            <img src="page/yongqiaorou/images/<?= $s->product_cover ?>" alt="<?= $s->product_name ?>">
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
            <div hidden><?= $subtotal += $order_subtotal?></div>
        </div>
        <div class="table-header">
            <div class="header-item">SST (6 %)</div>
            <div class="header-item"></div>
            <div class="header-item"></div>
            <div class="header-item">RM <?= number_format($order_subtotal*0.06, 2) ?></div>
            <div hidden><?= $tax = $order_subtotal*0.06; $subtotal += $tax?></div>
        </div>
        <div class="table-header">
            <div class="header-item">Delivery Fees (3 %)</div>
            <div class="header-item"></div>
            <div class="header-item"></div>
            <div class="header-item">RM <?= number_format($order_subtotal*0.03, 2) ?></div>
            <div hidden><?= $delivery_fee = $order_subtotal*0.03; $subtotal += $delivery_fee?></div>
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
            <div hidden><?= $total = $subtotal ?></div>
        </div>
        <div class="amount-row">
            <span class="subtitle">Discount:</span>
            <span class="value">- RM <?= number_format(0, 2) ?></span>
            <div hidden><?= $discount = 0; $total -= $discount ?></div>
        </div>
        <div class="amount-row">
            <span class="subtitle">Total Amount to Pay:</span>
            <span class="value">RM <?= number_format($subtotal, 2) ?></span>
        </div>
        <div class="amount-row">
            <span class="subtitle">Points earned:</span>
            <span class="value"><?= floor($subtotal) ?> points</span>
            <div hidden><?= $points = floor($subtotal) ?></div>
        </div>
    </div>

    <div class="heading">
        <h1>Payment Method</h1>
    </div>

    <div class="action-button">
        <form method="post" id="payment"></form>
        <input hidden type="number" form="payment" step="0.01" value="<?= $order_subtotal ?>" name="order_subtotal">
        <input hidden type="number" form="payment" step="0.01" value="<?= $tax ?>" name="tax">
        <input hidden type="number" form="payment" step="0.01" value="<?= $delivery_fee ?>" name="delivery_fee">
        <input hidden type="number" form="payment" step="0.01" value="<?= $subtotal ?>" name="subtotal">
        <input hidden type="number" form="payment" step="0.01" value="<?= $discount?>" name="discount">
        <input hidden type="number" form="payment" step="0.01" value="<?= $total ?>" name="total">
        <input hidden type="number" form="payment" step ="1" value="<?= $points ?>" name="points">
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
