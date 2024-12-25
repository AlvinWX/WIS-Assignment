<?php

require '_base.php';
require 'lib/stripe-php-16.4.0/init.php';
include '_head.php';

$member_id = $user->member_id; 

//Retrieve shipping address
$get_address_stm = $_db -> prepare('SELECT * FROM `address` WHERE member_id = ?');
$get_address_stm -> execute([$member_id]); 
$addresses = $get_address_stm -> fetchAll();


//Checkout form
$paymentMethod = $_POST['payment_method'];
$addressID = (int)$_POST['address'];
$shippingAddress = $addresses[$addressID];

$order_subtotal = (double)$_POST['order_subtotal'];
$tax = (double)$_POST['tax'];
$delivery_fee = (double)$_POST['delivery_fee'];
$subtotal = (double)$_POST['subtotal'];
$discount = (double)$_POST['discount'];
$total = (double)$_POST['total'];
$points = (int)$_POST['points'];

//Payment form
$card_number = $_POST['card_number'];
$card_holder_name = $_POST['card_holder_name'];
$expiry_month = $_POST['expiry_month'];
$expiry_year = $_POST['expiry_year'];
$card_cvc = $_POST['card_cvc'];
$save_card = $_POST['save_card'];


if($_POST['address']==null){
    temp('info', 'Please select a shipping address');
    redirect('/checkout.php');
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

    //Get the order id
    $get_order_stm = $_db -> prepare('SELECT * FROM `order` WHERE member_id = ? AND order_date = ?');
    $get_order_stm -> execute([$user->member_id, $orderDate]); 
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

    //Add points

    //Change stock and sold

    //Store payment card
    
    redirect('index.php');


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    
</body>
</html>