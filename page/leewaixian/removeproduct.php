<?php
include '../../_base.php';

temp('info', 'Item removed form cart.');
$id = req('id');
$cart = req('cart_id');
$page = req('page');

$remove_product_stm = $_db -> prepare('DELETE FROM cart_product WHERE product_id = ? AND cart_id = ?');
$remove_product_stm -> execute([$id, $cart]);

redirect($page);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <title>Remove Product</title>
</head>
<body>
    <div id="info"><?= temp('info')?></div>
</body>
</html>