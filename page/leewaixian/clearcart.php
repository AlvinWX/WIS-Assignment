<?php
include '../../_base.php';

$user = $_SESSION['user'] ?? null;
$member_id = $user->member_id;
if(empty($member_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

temp('info', 'The cart has been cleared.');
$cart = req('cart_id');

$remove_product_stm = $_db -> prepare('DELETE FROM cart_product WHERE cart_id = ?');
$remove_product_stm -> execute([$cart]);

redirect('shoppingcart.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/productbox.css">
    <link rel="stylesheet" href="../../css/productsearch.css">
    <link rel="stylesheet" href="../../css/flash.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <title>Clear Cart</title>
</head>
<body>
    <div id="info"><?= temp('info')?></div>
</body>
</html>