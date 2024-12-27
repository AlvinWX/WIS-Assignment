<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

$id = req('id');

$stm = $_db->prepare('SELECT * FROM voucher WHERE voucher_id = ?');
$stm->execute([$id]);
$s = $stm->fetch();

if (!$s) {
    redirect('/');
}

// ----------------------------------------------------------------------------
$_title = 'Detail';
include '../../_admin_head.php';
?>

<table class="table detail" style="margin-top: 100px">
    <tr>
        <th>Id</th>
        <td><?= $s->voucher_id ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $s->voucher_name ?></td>
    </tr>
    <tr>
        <th>Description</th>
        <td><?= $s->voucher_desc ?></td>
    </tr>
    <tr>
        <th>Points to Redeem</th>
        <td><?= $s->voucher_points ?></td>
    </tr>
    <tr>
        <th>Min Spend</th>
        <td><?= $s->voucher_min_spend ?></td>
    </tr>
    <tr>
        <th>Discounts</th>
        <td><?= $s->voucher_discount ?></td>
    </tr>
    <tr>
        <th>Voucher Image</th>
        <td><img src="../../images/voucher_pic/<?= $s->voucher_img ?>"/></td>
    </tr>
</table>

<br>

<button data-get="/page/yongqiaorou/voucher.php"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

<?php
include '../../_foot.php';