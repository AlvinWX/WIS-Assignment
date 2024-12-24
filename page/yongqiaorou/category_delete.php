<?php
require '../../_base.php';
// ----------------------------------------------------------------------------

// $user = $_SESSION['user'] ?? null;
// $admin_id = $user->admin_id;

if (is_post()) {
    $id = req('id');

    $stm = $_db->prepare('UPDATE category SET category_status=0 WHERE category_id = ? ');
    $stm->execute([$id]);

    temp('info', 'Category deleted');
}

redirect('/page/yongqiaorou/category.php');