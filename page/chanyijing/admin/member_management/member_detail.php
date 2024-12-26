<?php
require '../../../../_base.php';

$member_id = req('member_id');
$stm = $_db->prepare('SELECT * FROM member WHERE member_id = ?');
$stm->execute([$member_id]);
$s = $stm->fetch();

if(!$s){
    redirect('member_list.php');
}

$addr_stm = $_db->prepare('SELECT * FROM address WHERE member_id = ?');
$addr_stm->execute([$member_id]);
$address = $addr_stm->fetch();

$_title = 'Member Detail';
include '../../../../_head.php';
?>
<link rel="stylesheet" href="/css/yj_app.css">
<div class="top-heading-space">
    <h3>Member Detail</h3>
</div>

<table class="table">
    <tr>
        <th>Profile Picture</th>
        <td class="profile-pic-container">
            <img src="../../../../images/profile_pic/<?=$s->member_profile_pic?>"></td>
        <td>
    <tr>
        <th>Member ID</th>
        <td><?= $s->member_id ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $s->member_name ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><a href="mailto:<?= $s->member_email ?>"><?= $s->member_email ?></a></td>
    </tr>
    <tr>
        <th>Phone</th>
        <td><a href="tel:<?= $s->member_phone ?>"><?= $s->member_phone ?></a></td>
    </tr>
    <tr>
        <th>Gender</th>
        <td><?= $s->member_gender ?></td>
    </tr>
    <tr>
        <th>Date Joined</th>
        <td><?= $s->member_date_joined ?></td>
    </tr>
    <tr>
    <th>Address</th>
    <td>
        <?php if ($address): ?>
            <a href="https://www.google.com/maps/search/<?= urlencode($address->address_street . ', ' . $address->address_postcode . ', ' . $address->address_city . ', ' . $address->address_state) ?>" target="_blank">
                <?= htmlspecialchars($address->address_street . ', ' . $address->address_postcode . ', ' . $address->address_city . ', ' . $address->address_state) ?>
            </a>
        <?php else: ?>
            Address not available
        <?php endif ?>
    </td>
</tr>
</table>

<br>
<button data-get="member_list.php">Back</button>

<?php
include '../../../../_foot.php';