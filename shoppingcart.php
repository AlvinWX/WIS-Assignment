<?php
require '_base.php';
include '_head.php';

//Retrieve member cart
$get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.memberID = c.member_id WHERE c.member_id = ?');
$get_cart_stm -> execute(["MB00001"]); //HERE NEED TO CHANGE AFTERWARDS
$shoppingCart = $get_cart_stm -> fetch();



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/shoppingcart.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Shopping Cart</title>
</head>
<body>
    <section class="cart-display">
        <div class="heading">
            <h1>Your Shopping Cart</h1>
        </div>



    </section>    


</body>
</html>

<?php
include '_foot.php';
?>