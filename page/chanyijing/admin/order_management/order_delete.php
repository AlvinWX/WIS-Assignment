<?php
require '../../../../_base.php';

if (is_post()) {
    $order_id = req('order_id');

    $stm = $_db->prepare('DELETE FROM feedback WHERE order_id = ?');
    $stm->execute([$order_id]);

    $stm = $_db->prepare('DELETE FROM payment WHERE order_id = ?');
    $stm->execute([$order_id]);

    $stm = $_db->prepare('DELETE FROM `order` WHERE order_id = ?');
    $stm->execute([$order_id]);

    temp('info', 'Order deleted');
}

redirect('order_list.php');
?>
