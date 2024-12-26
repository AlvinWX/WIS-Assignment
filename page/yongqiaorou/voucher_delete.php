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

    $stm = $_db->prepare('UPDATE category SET category_status=0, category_last_update = ?, admin_id = ? WHERE category_id = ? ');
    $stm->execute([, date("Y-m-d H:i:s"), $admin_id, $id]);

    temp('info', 'Category deleted');
}

redirect('/page/yongqiaorou/category.php');