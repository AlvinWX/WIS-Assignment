<?php

include '../../_base.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantity = $_POST['quantity'] ?? 0;
    $productId = $_POST['productId'] ?? 0;
    $cartId = $_POST['cartId'] ?? 0;

    if ($quantity > 0 && $productId > 0 && $cartId > 0) {
        $update_quantity_stm = $_db -> prepare('UPDATE cart_product SET quantity = ? WHERE cart_id = ? AND product_id = ?');
        $update_quantity_stm -> execute([$quantity, $cartId, $productId]);
    } 
}

?>