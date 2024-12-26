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

    $stm = $_db->prepare('UPDATE product SET product_status=0, admin_id = ?, product_last_update = ? WHERE product_id = ? ');
    $stm->execute([$admin_id, date("Y-m-d H:i:s"), $id]);

    temp('info', 'Product deleted');
}

redirect('/page/yongqiaorou/product.php');