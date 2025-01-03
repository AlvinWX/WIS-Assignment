<?php
require '../../_base.php';
include '../../_head.php';

$user = $_SESSION['user'] ?? null;
$member_id = $user->member_id;
if(empty($member_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

// Get the discount from the session
$discount = $_SESSION['discount'] ?? 0; 

//Retrieve member cart
$get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
$get_cart_stm -> execute([$member_id]); 
$shoppingCart = $get_cart_stm -> fetch();

//Retrieve added to cart already items
$get_products_stm = $_db -> prepare('SELECT * FROM cart_product c JOIN product p ON p.product_id = c.product_id WHERE cart_id = ? AND product_status = 1 AND product_stock > 0');
$get_products_stm -> execute([$shoppingCart->cart_id]); 
$cart_products = $get_products_stm -> fetchAll();

//Retrieve shipping address
$get_address_stm = $_db -> prepare('SELECT * FROM `address` WHERE member_id = ?');
$get_address_stm -> execute([$member_id]); 
$addresses = $get_address_stm -> fetchAll();

//Retrieve member voucher list
$get_voucherlist_stm = $_db -> prepare('SELECT * FROM voucher_list v JOIN member m ON m.member_id = v.member_id WHERE v.member_id = ?');
$get_voucherlist_stm -> execute([$member_id]);
$voucher_list = $get_voucherlist_stm -> fetch();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/checkout.css">
    <link rel="stylesheet" href="../../css/flash.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="../../js/checkout.js" defer></script>
    <title>Checkout</title>
</head>
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
            <div class="header-item">Subtotal</div>
        </div>
        <?php $order_subtotal=0;foreach ($cart_products as $a): 
            $get_product_detail_stm = $_db->prepare('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_id = ?');
            $get_product_detail_stm -> execute([$a->product_id]);
            $s = $get_product_detail_stm -> fetch();
        ?>
        <div class="table-row">
        <div class="product-item">
            <img src="../../images/product_pic/<?= $s->product_cover ?>" alt="<?= $s->product_name ?>">
            <div class="product-text">
                <span class="category-name"><?= $s->category_name ?></span>
                <h2 class="product-name"><?= $s->product_name ?></h2>
            </div>
        </div>

            <div class="price-item">RM <?= sprintf("%.2f", $s->product_price)  ?></div>
            <div class="amount-item"><?= $a->quantity ?></div>
            <div class="total-item">RM <?= sprintf("%.2f", $s->product_price * $a->quantity, 2);  ?></div>
            <div hidden><?= $order_subtotal += ($s->product_price * $a->quantity)?></div>
        </div>
        <?php endforeach ?>
        <div class="table-header">
            <div class="header-item">Order Subtotal</div>
            <div class="header-item"></div>
            <div class="header-item"></div>
            <div class="header-item">RM <?= sprintf("%.2f", $order_subtotal)  ?></div>
            <div hidden><?= $subtotal += $order_subtotal?></div>
        </div>
        <div class="table-header">
            <div class="header-item">SST (6 %)</div>
            <div class="header-item"></div>
            <div class="header-item"></div>
            <div class="header-item">RM <?= sprintf("%.2f", $order_subtotal*0.06)  ?></div>
            <div hidden><?= $tax = sprintf("%.2f", $order_subtotal*0.06); $subtotal += $tax?></div>
        </div>
        <div class="table-header">
            <div class="header-item">Delivery Fees (3 %)</div>
            <div class="header-item"></div>
            <div class="header-item"></div>
            <div class="header-item">RM <?= sprintf("%.2f", $order_subtotal*0.03)  ?></div>
            <div hidden><?= $delivery_fee = sprintf("%.2f", $order_subtotal*0.03); $subtotal += $delivery_fee?></div>
        </div>
        <div class="table-header">
            <div class="header-item">Subtotal</div>
            <div class="header-item"></div>
            <div class="header-item"></div>
            <div class="header-item">RM <?= sprintf("%.2f", $subtotal)  ?></div>
            <div hidden><?= $subtotal = sprintf("%.2f", $subtotal)  ?></div>
        </div>
    </div>
</section>

<?php
//Retrieve member owned vouchers (but eligible to use only)
$get_voucher_owned = $_db->prepare('SELECT * FROM voucher_owned WHERE voucher_list_id = ? AND voucher_min_spend <= ?');
$get_voucher_owned -> execute([$voucher_list -> voucher_list_id, $subtotal]);
$voucherFound = $get_voucher_owned -> fetchAll();
?>

<section class ="voucher-apply">
<div class="heading">
        <h1>Apply Voucher</h1>
    </div>
    
    <div class="select-voucher">
    <div id="selectfield">
        <p id="selecttext">No Vouchers Applied</p>
        <img src="../../images/arrow.png" alt="arrow" id="arrowicon">
    </div>
    <ul id="list" class="hide">
        <li class="options">
            <img class="novoucher" src="../../images/novouchers.png">
            <p class="voucher-option" data-discount="0" data-id="0">No Vouchers Applied</p>
        </li>
        <?php foreach($voucherFound as $s): ?>
        <li class="options">
            <img class="withvoucher" src="../../images/voucher_pic/<?= $s->voucher_img ?>">
            <p class="voucher-option" data-discount="<?= $s->voucher_discount ?>" data-id="<?= $s -> voucher_owned_id ?>"><strong><?= $s->voucher_name ?></strong> (Min spend. RM <?= $s -> voucher_min_spend ?>) <div>Owned: <?= $s -> voucher_quantity ?></div></p>
        </li>
        <?php endforeach ?>
    </ul>
    <input type="text" hidden id="voucherinput" form="payment" name="voucher_name" value="No Vouchers Applied">
    <input type="number" hidden id="voucherdiscount" form="payment" name="discount_price" value="0">
    <input type="number"hidden id="voucherownedid" form="payment" name="voucher_owned_id" value="0">
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
                    <input type="radio" class="address-select" id="<?= $id ?>" name="address" form="payment" value="<?= $id ?>">
                    <div class="address-details">
                        <?= $s->address_street ?>, <?= $s->address_postcode ?>, <?= $s->address_city ?>, <?= $s->address_state ?>
                    </div>
                    <div hidden><?= $id++ ?></div>
                </label>
            </div>
        <?php endforeach ?>
    </div>
</section>

<?php
    $discount = 0;
?>

<section class ="final-total">
    <div class="heading">
        <h1>Final Amount to Pay</h1>
    </div>
    <div class="final-amount">
        <div class="amount-row">
            <span class="subtitle">Subtotal:</span>
            <span class="value" id="subtotal">RM <?= sprintf("%.2f", $subtotal)  ?></span>
            <div hidden><?= $total = $subtotal ?></div>
        </div>
        <div class="amount-row">
            <span class="subtitle">Discount:</span>
            <span class="value" id="discount">- RM <?= sprintf("%.2f", $discount)  ?></span>
            <div hidden><?= $total -= $discount ?></div>
        </div>
        <div class="amount-row">
            <span class="subtitle">Total Amount to Pay:</span>
            <span class="value" id="finaltotal">RM <?= sprintf("%.2f", $total)  ?></span>
        </div>
        <div class="amount-row">
            <span class="subtitle">Points earned:</span>
            <span class="value" id="points"><?= floor($total) ?> points</span>
            <div hidden><?= $points = floor($total) ?></div>
        </div>
    </div>

    <div class="heading">
        <h1>Payment Method</h1>
    </div>

    <div class="action-button">
        <form method="post" id="payment" action="payment.php"></form>
        <input hidden type="number" form="payment" step="0.01" value="<?= $order_subtotal ?>" name="order_subtotal">
        <input hidden type="number" form="payment" step="0.01" value="<?= $tax ?>" name="tax">
        <input hidden type="number" form="payment" step="0.01" value="<?= $delivery_fee ?>" name="delivery_fee">
        <input hidden type="number" form="payment" step="0.01" value="<?= $subtotal ?>" name="subtotal">
        <input hidden type="number" id="formdiscount" form="payment" step="0.01" value="<?= $discount?>" name="discount">
        <input hidden type="number" id="formtotal" form="payment" step="0.01" value="<?= $total ?>" name="total">
        <input hidden type="number" id="formpoints" form="payment" step ="1" value="<?= $points ?>" name="points">
        <input type="submit" form="payment" class="fakecard" name="submit" value="Payment Card">
        <input type="submit" form="payment" class="stripe" name="submit" value="Stripe">
        <button>Coming Soon</button>
     </div>
</section>


</body>
</html>

<?php
include '../../_foot.php';
?>
