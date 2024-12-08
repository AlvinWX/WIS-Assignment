<?php
require '../../../../_base.php';

if (is_post()) {
    $memberID = req('memberID');

    $stm = $_db->prepare('DELETE FROM member WHERE memberID = ?');
    $stm->execute([$memberID]);

    temp('info', 'Member deleted');
}

redirect('member_list.php');
