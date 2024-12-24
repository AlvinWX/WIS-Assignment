<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

if (is_post()) {
    $admin_id = req('admin_id');

    $stm = $_db->prepare('DELETE FROM admin WHERE admin_id = ?');
    $stm->execute([$admin_id]);

    temp('info', 'Admin deleted');
}

redirect('admin_list.php');