<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

if (is_post()) {
    $member_id = req('member_id');

    $stm = $_db->prepare('DELETE FROM member WHERE member_id = ?');
    $stm->execute([$member_id]);

    temp('info', 'Member deleted');
}

redirect('member_list.php');
