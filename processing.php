<?php

require '_base.php';
require 'lib/stripe-php-16.4.0/init.php';
include '_head.php';

$member_id = $user->member_id; 

$order_details = $_SESSION['order_details'];

//Checkout form
$paymentMethod = $order_details['payment_method'];
$shippingAddress = $order_details['delivery_address'];

if($shippingAddress==null){
    temp('info', 'Please select a shipping address');
    redirect('/checkout.php');
}

$paidDate = (new DateTime())->format('Y-m-d H:i:s');

$order_subtotal = $order_details['order_subtotal'];
$tax = $order_details['tax'];
$delivery_fee = $order_details['delivery_fee'];
$subtotal = $order_details['subtotal'];
$discount = $order_details['discount'];
$total = $order_details['total'];
$points = $order_details['points'];

if($paymentMethod=="Payment Card"){
    //Payment form
    $card_number = $_POST['card_number'];
    $card_holder_name = $_POST['card_holder_name'];
    $expiry_month = $_POST['expiry_month'];
    $expiry_year = $_POST['expiry_year'];
    $card_cvc = $_POST['card_cvc'];
    $save_card = $_POST['save_card'];

    if($save_card != 1){
        $save_card = 0;
    }

    //Get the payment card details
    $get_payment_card_stm = $_db -> prepare('SELECT * FROM payment_card WHERE member_id = ?');
    $get_payment_card_stm -> execute([$member_id]); 
    $payment_card = $get_payment_card_stm -> fetch();

    //Store payment card (if needed)
    if($save_card == 1){
        
        if($payment_card == null){
            //The user first time add the card
            $add_payment_card_stm = $_db -> prepare('INSERT INTO payment_card
            (card_number, card_holder_name, expiry_month, expiry_year, card_cvc, member_id)
            VALUES (?, ?, ?, ?, ?, ?)');
            $add_payment_card_stm -> execute([$card_number, $card_holder_name, $expiry_month, $expiry_year, $card_cvc, $user->member_id]);
        } else{
            //The user add the card before
            $update_payment_card_stm = $_db -> prepare('UPDATE payment_card
            SET card_number = ?, card_holder_name = ?, expiry_month = ?, expiry_year = ?, card_cvc = ?
            WHERE member_id = ?');
            $update_payment_card_stm -> execute([$card_number, $card_holder_name, $expiry_month, $expiry_year, $card_cvc, $user->member_id]);
        }    

    } else{
        
        //The user don't want to store the payment card
        if($payment_card != null){
            //Clear the payment card in the database if user added before
            $remove_payment_card_stm = $_db -> prepare('DELETE FROM payment_card WHERE member_id = ?');
            $remove_payment_card_stm -> execute([$user->member_id]);
        }

        //No action needed to do if the user didn't added before

    }

}

//Insert new record into the shipping address table
$create_shipping_address_stm = $_db -> prepare('INSERT INTO shipping_address
(street, postcode, city, `state`) 
VALUES (?, ?, ?, ?)');
$create_shipping_address_stm -> execute([$shippingAddress->address_street, $shippingAddress->address_postcode, $shippingAddress->address_city, $shippingAddress->address_state]); 

//Get the shipping address id
$get_shipping_address_stm = $_db -> prepare('SELECT * FROM shipping_address
WHERE street = ? AND postcode = ? AND city = ? AND `state` = ? ');
$get_shipping_address_stm -> execute([$shippingAddress->address_street, $shippingAddress->address_postcode, $shippingAddress->address_city, $shippingAddress->address_state]); 
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
    'Pending', $ad->shipping_address_id, $user->member_id
]);

//Add member points
$add_points_stm = $_db -> prepare(('UPDATE member
SET member_points = member_points + ?
WHERE member_id = ?'));
$add_points_stm -> execute([$points, $user->member_id]);

//Get the order id
$get_order_stm = $_db -> prepare('SELECT * FROM `order` WHERE member_id = ? AND order_date = ?');
$get_order_stm -> execute([$user->member_id, $orderDate]); 
$id = $get_order_stm -> fetch();

//Upload payment details
$payment_method_id = 0;
if($paymentMethod=="Payment Card"){
    $payment_method_id = 1;
} else {
    $payment_method_id = 2;
}

$add_payment_stm = $_db -> prepare('INSERT INTO payment
(`date`, amount, order_id, payment_method_id)
VALUES (?, ?, ?, ?)');
$add_payment_stm -> execute([$orderDate, $total, $id->order_id, $payment_method_id]);

//Insert new record into the order_product table
foreach ($cart_products as $a){
    $create_order_product_stm = $_db -> prepare('INSERT INTO order_product 
    (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)');
    $create_order_product_stm -> execute([$id->order_id, $a-> product_id, $a->price, $a->quantity]);

    //Change stock and sold quantity
    $update_product_quantity_stm = $_db -> prepare('UPDATE product 
    SET product_stock = product_stock - ?, product_sold = product_sold + ?
    WHERE product_id = ?');
    $update_product_quantity_stm -> execute([$a->quantity, $a->quantity, $a->product_id]);
}


//Clear shopping cart
$clear_order_cart = $_db -> prepare('DELETE FROM cart_product WHERE cart_id = ?');
$clear_order_cart -> execute([$shoppingCart-> cart_id]); 

$_SESSION['success_details'] = [
    'amount_paid' => $total,
    'order_id' => $id->order_id,
    'order_date' => $orderDate,
    'ship_date' => $shipDate,
    'received_date' => $receivedDate,
    'points' => $points
];
    
redirect('success.php');


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment processing</title>
</head>

<body>
<div id="info"><?= temp('info')?></div>
</body>
</html>