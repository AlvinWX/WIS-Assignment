<?php
require '../../_base.php';
// ----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

if (is_post()) {
    $id = req('id');

    $stm = $_db->prepare('UPDATE voucher SET voucher_status=0, voucher_last_update = ?, admin_id = ? WHERE voucher_id = ? ');
    $stm->execute([date("Y-m-d H:i:s"), $admin_id, $id]);

    temp('info', 'Voucher deleted');
}

redirect('/page/yongqiaorou/voucher.php');