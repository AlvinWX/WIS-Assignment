<?php
include '../../_base.php';

temp('info', 'Voucher redeemed successfully.');
$id = req('id');
$voucher_list_id = req('voucher_list_id');
$page = req('page');

//Get member id
$get_member_id_stm = $_db -> prepare('SELECT * FROM voucher_list v JOIN member m ON m.member_id = v.member_id WHERE voucher_list_id = ?');
$get_member_id_stm -> execute([$voucher_list_id]);
$member_details = $get_member_id_stm -> fetch();

//Get voucher details from voucher table
$get_voucher_details_stm = $_db -> prepare('SELECT * FROM voucher WHERE voucher_id = ?');
$get_voucher_details_stm -> execute([$id]);
$voucher_details = $get_voucher_details_stm -> fetch();

//Get member owned vouchers from voucher_owned table
$get_voucher_owned_stm = $_db -> prepare('SELECT * FROM voucher_owned WHERE voucher_list_id = ? AND voucher_discount = ? AND voucher_min_spend = ?');
$get_voucher_owned_stm -> execute([$voucher_list_id, $voucher_details -> voucher_discount, $voucher_details -> voucher_min_spend]);
$vouchers_owned = $get_voucher_owned_stm -> fetch();

if($vouchers_owned == null){
    //The member first time redeem this voucher
    $add_voucher_stm = $_db -> prepare('INSERT INTO voucher_owned 
    (voucher_name, voucher_discount, voucher_min_spend, voucher_desc, voucher_img, voucher_quantity, voucher_list_id)
    VALUES(?, ?, ?, ?, ?, ?, ?)');
    $add_voucher_stm -> execute([$voucher_details -> voucher_name, $voucher_details -> voucher_discount,
    $voucher_details -> voucher_min_spend, $voucher_details -> voucher_desc, 
    $voucher_details -> voucher_img, 1, $voucher_list_id]);
} else{
    //The member redeem the voucher before
    $update_voucher_quantity_stm = $_db -> prepare('UPDATE voucher_owned SET voucher_quantity = voucher_quantity + 1 WHERE voucher_list_id = ? AND voucher_discount = ? AND voucher_min_spend = ?');
    $update_voucher_quantity_stm -> execute([$voucher_list_id, $voucher_details -> voucher_discount, $voucher_details -> voucher_min_spend]);
}

//Deduct member points after redeemed

$newPoints = ($member_details -> member_points) - ($voucher_details -> voucher_points);

//Update member points
$deduct_member_points_stm = $_db -> prepare('UPDATE member SET member_points =  ? WHERE member_id = ?');
$deduct_member_points_stm -> execute([$newPoints, $member_details -> member_id]);

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

    <title>Redeem Vouchers</title>
</head>
<body>
    <div id="info"><?= temp('info')?></div>
</body>
</html>