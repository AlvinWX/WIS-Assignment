<?php
require '../../_base.php';
// ----------------------------------------------------------------------------

if (is_post()) {
    $id = req('id');

    $stm = $_db->prepare('UPDATE category SET category_status=0 WHERE category_id = ? ');
    $stm->execute([$id]);

    temp('info', 'Category deleted');
}

redirect('/page/yongqiaorou/category.php');