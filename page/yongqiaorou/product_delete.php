<?php
require '../../_base.php';
// ----------------------------------------------------------------------------

if (is_post()) {
    $id = req('id');

    $stm = $_db->prepare('UPDATE product SET product_status=0 WHERE product_id = ? ');
    $stm->execute([$id]);

    temp('info', 'Product deleted');
}

redirect('/page/yongqiaorou/product.php');