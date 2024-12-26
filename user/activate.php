<?php
include '../_base.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $stm = $_db->prepare('SELECT * FROM pending_members WHERE token = ?');
    $stm->execute([$token]);
    $member = $stm->fetch(PDO::FETCH_ASSOC);

    if ($member) {
        // Generate new member ID
        $stm = $_db->query('SELECT MAX(member_id) AS maxID FROM member');
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        $lastID = $result['maxID'] ?? 'MB00000';
        $newID = sprintf('MB%05d', (int)substr($lastID, 2) + 1);

        // Insert into member table
        $stm = $_db->prepare('
            INSERT INTO member (member_id, member_name, member_password, member_email, member_phone, member_gender, member_profile_pic, member_date_joined)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stm->execute([$newID, $member['member_name'], $member['member_password'], $member['member_email'], $member['member_phone'], $member['member_gender'], $member['member_profile_pic'], $member['member_date_joined']]);

        // Remove from pending_members
        $stm = $_db->prepare('DELETE FROM pending_members WHERE token = ?');
        $stm->execute([$token]);

        temp('info', 'Account activated');
        redirect('/login.php');
    } else {
        temp('error', 'Invalid token');
        redirect('/user/error.php');
    }
}