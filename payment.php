<?php
require '_base.php';
require 'lib/stripe-php-16.4.0/init.php';
include '_head.php';

//Retrieve shipping address
$get_address_stm = $_db -> prepare('SELECT * FROM `address` WHERE member_id = ?');
$get_address_stm -> execute([$member_id]); 
$addresses = $get_address_stm -> fetchAll();


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


if($_POST['address']==null){
    temp('info', 'Please select a shipping address');
    redirect('/checkout.php');
}

if($paymentMethod == "Stripe"){
    $stripe_secret_key = "sk_test_51QZYpQIDBq49aYjk5CO6Bo5LgxCOTe2P3SgDC9VXXTxkarGPq6cwkSlQqIkdU2NpvjqnoDc3k0KIPxXmNaNkoI8000nlCU5nps";

\Stripe\Stripe::setApiKey($stripe_secret_key);

$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    "success_url" => "http://localhost:8000/index.php",
    "cancel_url" => "http://localhost:8000/checkout.php",
    "line_items" => [
        [  
        "quantity" => 1,
        "price_data" => [
            "currency" => "myr",
            "unit_amount" => $total * 100,
            "product_data" => [
                "name" => "TAR Grocer"
            ],
        ],
        ]
    ]
]);


http_response_code(303);
header("Location: " . $checkout_session->url);
}



// //Retrieve member cart
// $member_id = $user->member_id; 

// $get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
// $get_cart_stm -> execute([$member_id]); 
// $shoppingCart = $get_cart_stm -> fetch();

// //Retrieve added to cart already items
// $get_products_stm = $_db -> prepare('SELECT * FROM cart_product WHERE cart_id = ?');
// $get_products_stm -> execute([$shoppingCart->cart_id]); 
// $cart_products = $get_products_stm -> fetchAll();

// //Retrieve shipping address
// $get_address_stm = $_db -> prepare('SELECT * FROM `address` WHERE member_id = ?');
// $get_address_stm -> execute([$member_id]); 
// $addresses = $get_address_stm -> fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/payment.css">
    <link rel="stylesheet" href="css/flash.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Payment</title>
</head>
<style>
.empty-box{
    margin: 30px;
    padding: 30px;
}
</style>
<body>
    <div id="info"><?= temp('info')?></div>    
    <div class="empty-box"></div>
    
    <div class="container">

        <div class="card-container">
            <div class="front">
                <div class="image">
                    <img src="images/chip.png">
                    <img src="images/visa2.png">
                </div>
                <div class="card-number-box">0000 0000 0000 0000</div>
                <div class="flexbox">
                    <div class="box">
                        <span>CARD HOLDER</span>
                        <div class="card-holder-name">YOUR NAME</div>
                    </div>
                    <div class="box">
                        <span>EXPIRES</span>
                        <div class="expiration">
                            <span class="exp-month">08</span>
                            <span class="exp-year">28</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="" id="payment">
            <div class="heading"><h2>Enter Payment Card Details</h2></div>
            <div class="inputbox">
                <span>Card Number</span>
                <input class="top" type="text" maxlength="16" class="card-number-input" name="card_number">
            </div>
            <div class="inputbox">
                <span>Card Holder Name</span>
                <input class="top" type="text" class="card-holder-input" name="card_holder_name">
            </div>
            <div class="flexbox">
                <div class="inputbox">
                    <span>Expiry Month</span>
                    <select name="" id="" class="month-input" name="expiry_month">
                        <option value="month" selected disabled>Month</option>
                        <option value="01">01</option>
                        <option value="02">02</option>
                        <option value="03">03</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>
                </div>
                <div class="inputbox">
                    <span>Expiry Year</span>
                    <select name="" id="" class="year-input" name="expiry_year">
                        <option value="year" selected disabled>Year</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                        <option value="2029">2029</option>
                        <option value="2030">2030</option>
                        <option value="2031">2031</option>
                        <option value="2032">2032</option>
                        <option value="2033">2033</option>
                        <option value="2034">2034</option>
                        <option value="2035">2035</option>
                        <option value="2036">2036</option>
                    </select>
                </div>
                <div class="inputbox">
                    <span>CVC</span>
                    <input class="bottom" type="text" maxlength="3" class="cvc-input" name="card_cvc">
                </div>
            </div>
            <div class="checkbox">
                    <label><input type="checkbox" id="true" name="save_card" value="1" checked>Save the above payment card for future payment use.</label>
            </div>
            <input type="submit" value="Pay: RM <?= sprintf('%.2f', $total) ?>" class="submit-btn">
        </form>
    </div>
</body>
</html>

<?php
include '_foot.php';
?>
