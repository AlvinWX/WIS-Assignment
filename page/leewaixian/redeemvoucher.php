<?php
require '../../_base.php';
include '../../_head.php';

$fullPath = $_SERVER['REQUEST_URI'];
$_SESSION['path_details'] = $fullPath;

$page = req('page', 1);
require_once '../../lib/SimplePager.php';
$p = new SimplePager('SELECT * FROM voucher', [], 10, $page);
$arr = $p->result;

$member_id = $user->member_id; 

//Retrieve member voucher_list
$get_voucherlist_stm = $_db -> prepare('SELECT * FROM voucher_list v JOIN member m ON m.member_id = v.member_id WHERE v.member_id = ?');
$get_voucherlist_stm -> execute([$member_id]);
$voucher_list = $get_voucherlist_stm -> fetch();

//Retrieve member points
$get_updated_points_stm = $_db->prepare('SELECT member_points FROM member WHERE member_id = ?');
$get_updated_points_stm->execute([$member_id]);
$updated_member_details = $get_updated_points_stm->fetch();

$points = $updated_member_details -> member_points;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/productbox.css">
    <link rel="stylesheet" href="../../css/redeemvoucher.css">
    <link rel="stylesheet" href="../../css/flash.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="../../js/redeemvoucher.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <title>Search Product <?=$searchString ?></title>
</head>
<body>
    <div id="info"><?= temp('info')?></div>
    <div class="empty-box"></div>

    <section class="products" id="products">
        <div class="heading">
            <h1>Redeem Vouchers </h1>
        </div>

        <div class="amount">
            <i class='bx bxs-star'></i>Total points: <?= $points ?> point(s)
        </div>

        <div>
        <?= $p->count ?> of <?= $p->item_count ?> voucher(s) available to redeem |
        Page <?= $p->page ?> of <?= $p->page_count ?>
        </div>

        <div class="paging">
            <p>Select page:</p><?= $p->html() ?>
        </div>
    
        <div class="products-container">
        <?php foreach ($arr as $s): ?>
            <div class="box">
                <img src="../../images/voucher_pic/<?= $s->voucher_img ?>" data-get="voucherinfo.php?id=<?= $s->voucher_id ?>">
                <span data-get="voucherinfo.php?id=<?= $s->voucher_id ?>">Voucher</span>
                <h2 class="product-name" data-get="voucherinfo.php?id=<?= $s->product_id ?>"><?= $s->voucher_name?></h2>
                <?php 
                    $get_voucher_owned = $_db->prepare('SELECT * FROM voucher_owned WHERE voucher_list_id = ? AND voucher_discount = ? AND voucher_min_spend = ?');
                    $get_voucher_owned -> execute([$voucher_list -> voucher_list_id, $s -> voucher_discount, $s -> voucher_min_spend]);
                    $voucherFound = $get_voucher_owned -> fetch();
                    if($voucherFound != null){ ?>
                        <h2 class="owned" data-get="voucherinfo.php?id=<?= $s->product_id ?>">Owned: <?= $voucherFound -> voucher_quantity ?></h2>
                <?php  } else { ?>
                        <h2 class="owned"></h2>
                    <?php  } ?>
                <h3 class="price" data-get="voucherinfo.php?id=<?= $s->voucher_id ?>"><?= $s -> voucher_points ?> points</h3>
                <button class="redeem" onclick="confirmRedeem(<?= $points ?>,  <?= $s -> voucher_points ?>, '<?= $s->voucher_id ?>', '<?= $voucher_list -> voucher_list_id ?>', '<?= $fullPath ?>')"><i class='bx bxs-gift'></i></button>
                <!-- <a class= "add-to-cart" href="addquantity.php?id=<?= $s->voucher_id ?>"><i class='bx bxs-gift'></i></a> -->
            </div>
            <?php endforeach ?>
            </div>
            
    </section>

</body>
</html>

<?php
include '../../_foot.php';
?>