<?php
require '../../_base.php';

include '../../_head.php';

$id = req('id');

$fullPath = $_SESSION['path_details'];

$stm = $_db->prepare('SELECT * FROM voucher WHERE voucher_id = ?'); 
$stm->execute([$id]);
$s = $stm->fetch();

if (!$s) { 
    redirect('/');
}

$user = $_SESSION['user'] ?? null;
$member_id = $user->member_id;
if(empty($member_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

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

<title>Redeem <?= $s -> voucher_name ?></title>
<link rel="stylesheet" href="../../css/voucherinfo.css">
<link rel="stylesheet" href="../../css/flash.css">
<link rel="stylesheet" href="../../css/imageslider.css">
<script src="../../js/redeemvoucher.js" defer></script>
<script src="../../js/imageslider2.js" defer></script>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<script>
window.onload = function() {
    document.getElementById('spinnerValue').blur();
};
</script>

<body>
<div id="info"><?= temp('info')?></div>
    <div class="card-wrapper">
        <div class="card">
            <!--card left-->
            <div class="product-images">
            <div class = "slide-container" style="width: 560px; height: 560px;">      
            <div class="slides" style="height: 100%;">
                <img src="../../images/voucher_pic/<?= $s->voucher_img ?>" alt="Voucher Image" class="image active">
            </div>

            </div>
            </div>
            <!--card right-->
            <div class = "product-content">
                <span class = "product-title"><?= $s -> voucher_name ?></span>

                <span class = "product-category">Category: Voucher</span>

                <div class= "product-price">
                    <?= $s -> voucher_points ?> Points <i class='bx bxs-star'></i>
                </div>

                <div class="purchase-info">
                <button class="remove" onclick="confirmRedeem(<?= $points ?>,  <?= $s -> voucher_points ?>, '<?= $s->voucher_id ?>', '<?= $voucher_list -> voucher_list_id ?>', '<?= $fullPath ?>')">Redeem Now</button>           
                </div>
                <div class = "product-desc">
                    <span>Voucher description:</span>
                    <p><?= $s -> voucher_desc ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="empty-box"></div>
</body>

<?php
include '../../_foot.php';