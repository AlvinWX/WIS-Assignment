<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

if (is_post()) {
    $adminID = req('adminID');

    $stm = $_db->prepare('DELETE FROM admin WHERE adminID = ?');
    $stm->execute([$adminID]);

    temp('info', 'Admin deleted');
}

redirect('admin_list.php');