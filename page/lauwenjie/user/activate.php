<?php
include '../../../_base.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Step 1: Get member data from the pending_members table
    $stm = $_db->prepare('SELECT * FROM pending_members WHERE token = ?');
    $stm->execute([$token]);
    $member = $stm->fetch(PDO::FETCH_ASSOC);

    if ($member) {
        // Step 2: Generate new member ID
        $stm = $_db->query('SELECT MAX(member_id) AS maxID FROM member');
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        $lastID = $result['maxID'] ?? 'MB00000';
        $newID = sprintf('MB%05d', (int)substr($lastID, 2) + 1);

        // Step 3: Insert member data into the member table
        $stm = $_db->prepare('
            INSERT INTO member (member_id, member_name, member_password, member_email, member_phone, member_gender, member_profile_pic, member_date_joined)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stm->execute([$newID, $member['member_name'], $member['member_password'], $member['member_email'], $member['member_phone'], $member['member_gender'], $member['member_profile_pic'], $member['member_date_joined']]);

        // Step 4: Insert address data into the address table
        $stm = $_db->prepare('
            INSERT INTO address (address_id, address_street, address_postcode, address_city, address_state, member_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $newAddressID = 'AD' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);  // Generate unique address ID
        $stm->execute([
            $newAddressID, 
            $member['member_address'],  // Address street
            $member['member_postcode'], // Postcode
            $member['member_city'],     // City
            $member['member_state'],    // State
            $newID                      // The member_id from the new member record
        ]);

        // Step 5: Remove from pending_members
        $stm = $_db->prepare('DELETE FROM pending_members WHERE token = ?');
        $stm->execute([$token]);

        // Step 6: Send success message and redirect to login page
        temp('info', 'Account activated');
        redirect('/login.php');
    } else {
        // Step 7: Handle invalid token case
        temp('error', 'Invalid token');
        redirect('/user/error.php');
    }
}
