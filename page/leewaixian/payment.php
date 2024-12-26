<?php
require '../../_base.php';
require '../../lib/stripe-php-16.4.0/init.php';
include '../../_head.php';

$member_id = $user->member_id; 

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

//Retrive payment card details
$get_payment_card_stm = $_db -> prepare('SELECT * FROM payment_card WHERE member_id = ?');
$get_payment_card_stm -> execute([$member_id]); 
$payment_card = $get_payment_card_stm -> fetch();


if($_POST['address']==null){
    temp('info', 'Please select a shipping address');
    redirect('page/leewaixian/checkout.php');
}

$_SESSION['order_details'] = [
    'payment_method' => $_POST['submit'],
    'delivery_address' => $shippingAddress,
    'order_subtotal' => (double)$_POST['order_subtotal'],
    'tax' => (double)$_POST['tax'],
    'delivery_fee' => (double)$_POST['delivery_fee'],
    'subtotal' => (double)$_POST['subtotal'],
    'discount' => (double)$_POST['discount'],
    'total' => (double)$_POST['total'],
    'points' => (int)$_POST['points']
];


if($paymentMethod == "Stripe"){
    $stripe_secret_key = "sk_test_51QZYpQIDBq49aYjk5CO6Bo5LgxCOTe2P3SgDC9VXXTxkarGPq6cwkSlQqIkdU2NpvjqnoDc3k0KIPxXmNaNkoI8000nlCU5nps";

\Stripe\Stripe::setApiKey($stripe_secret_key);

$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    "success_url" => "http://localhost:8000/page/leewaixian/processing.php",
    "cancel_url" => "http://localhost:8000/page/leewaixian/checkout.php",
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/payment.css">
    <link rel="stylesheet" href="../../css/flash.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="../../js/payment.js" defer></script>
    <title>Payment</title>
</head>
<style>
.empty-box{
    margin: 0px;
    padding: 35px;
}
</style>



<body>
    <div id="info"><?= temp('info')?></div>    
    <div class="empty-box"></div>
    
    <div class="container">
    <div class="empty-box"></div>
        <div class="card-container">
            <div class="front">
                <div class="image">
                    <img src="../../images/chip.png">
                    <img src="../../images/visa2.png">
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

        <?php

            $card_number_value = "";
            $card_holder_name_value = "";
            $expiry_month_value = "";
            $expiry_year_value = "";
            $card_cvc_value = "";

            if($payment_card != null){
                $card_number_value = $payment_card -> card_number;
                $card_holder_name_value = $payment_card -> card_holder_name;
                $expiry_month_value = $payment_card -> expiry_month;
                $expiry_year_value = $payment_card -> expiry_year;
                $card_cvc_value = $payment_card -> card_cvc;
            }

        ?>

        <form method="post" action="processing.php" id="payment">
            <div class="heading"><h2>Enter Payment Card Details</h2></div>
            <div class="inputbox">
                <span>Card Number</span>
                <input class="top" id="card_number" type="text" maxlength="16" class="card-number-input" name="card_number" placeholder="Enter the 16-digits card" value="<?= $card_number_value ?>">
            </div>
            <div class="inputbox">
                <span>Card Holder Name</span>
                <input class="top" id="card_holder_name" type="text" class="card-holder-input" name="card_holder_name" placeholder="Enter the card holder name" value="<?= $card_holder_name_value ?>">
            </div>
            <div class="flexbox">
                <div class="inputbox">
                    <span>Expiry Month (MM)</span>
                    <input class="bottom" type="text" maxlength="2" class="month-input" id="expiry_month" name="expiry_month" placeholder="Enter month" value="<?= $expiry_month_value ?>">
                </div>
                <div class="inputbox">
                    <span>Expiry Year (YYYY)</span>
                    <input class="bottom" type="text" maxlength="4" class="year-input" id="expiry_year" name="expiry_year" placeholder="Enter year" value="<?= $expiry_year_value ?>">
                </div>
                <div class="inputbox">
                    <span>CVC</span>
                    <input class="bottom" type="text" maxlength="3" class="cvc-input" id="card_cvc" name="card_cvc" placeholder="Enter the 3-digits CVC" value="<?= $card_cvc_value ?>">
                </div>
            </div>
            <div class="checkbox">
                    <label><input type="checkbox" id="save_card" name="save_card" value="1" checked>Save the above payment card for future payment use.</label>
            </div>
            <input type="submit" value="Pay: RM <?= sprintf('%.2f', $total) ?>" class="submit-btn">
        </form>
    </div>
</body>
</html>

<?php
include '../../_foot.php';
?>
