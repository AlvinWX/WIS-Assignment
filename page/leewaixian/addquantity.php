<?php
require '../../_base.php';
include '../../_head.php';

$productID = req('id');

$fullPath = $_SESSION['path_details'];

if(str_contains($fullPath, 'productsearch.php')){
    $add_quantity_details = $_SESSION['add_quantity_details'];
    $productName = $add_quantity_details['product_name'];
    $productCategory = $add_quantity_details['product_category'];
    $minPrice = $add_quantity_details['min_price'];
    $maxPrice = $add_quantity_details['max_price'];
    $sort = $add_quantity_details['sort'];
    $dir = $add_quantity_details['dir'];
    $fullPath = "productsearch.php?product_name={$productName}&category_id={$productCategory}&minprice={$minPrice}&maxprice={$maxPrice}&sort={$sort}&dir={$dir}&page=$page";
} else if(str_contains($fullPath, 'productsearch.php')){
    $add_quantity_details = $_SESSION['add_quantity_details'];
    $sort = $add_quantity_details['sort'];
    $dir = $add_quantity_details['dir'];
    $fullPath = "wishlist.php?sort={$sort}&dir={$dir}&page=$page";
}


//Retrieve member cart
$member_id = $user->member_id; 

$get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
$get_cart_stm -> execute([$member_id]);
$shoppingCart = $get_cart_stm -> fetch();

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
    redirect($fullPath);
} else if($check_result->quantity >= $productFound->product_stock) {
    //If the product is add to cart before (but the selected quantity >= stock)
    temp('info', 'The product quantity cannot greater than the current product stock.');
    redirect($fullPath);
} else{
    //If the product is add to cart before
    $update_quantity_stm = $_db -> prepare('UPDATE cart_product SET quantity = ? WHERE cart_id = ? AND product_id = ?');
    $update_quantity_stm -> execute([$check_result->quantity + 1, $shoppingCart->cart_id, $productID]);
    temp('info', 'Item added to cart.');
    redirect($fullPath);
}

?>