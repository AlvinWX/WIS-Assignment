<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

$memberID = req('memberID');
$stm = $_db->prepare('SELECT * FROM member WHERE memberID = ?');
$stm->execute([$memberID]);
$s = $stm->fetch();

if(!$s){
    redirect('member_list.php');
}

$addr_stm = $_db->prepare('SELECT * FROM address WHERE memberID = ?');
$addr_stm->execute([$memberID]);
$address = $addr_stm->fetch();

$_title = 'Member Detail';
include '../../../../_head.php';
?>

<div class="top-heading-space">
    <h3>Member Detail</h3>
</div>

<table class="table">
    <tr>
        <th>Profile Picture</th>
        <td class="profile-pic-container">
            <img src="../../../../images/profile_pic/<?=$s->memberProfilePic?>"></td>
        <td>
    <tr>
        <th>Member ID</th>
        <td><?= $s->memberID ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $s->memberName ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><a href="mailto:<?= $s->memberEmail ?>"><?= $s->memberEmail ?></a></td>
    </tr>
    <tr>
        <th>Phone</th>
        <td><a href="tel:<?= $s->memberPhone ?>"><?= $s->memberPhone ?></a></td>
    </tr>
    <tr>
        <th>Gender</th>
        <td><?= $s->memberGender ?></td>
    </tr>
    <tr>
        <th>Date Joined</th>
        <td><?= $s->memberDateJoined ?></td>
    </tr>
    <tr>
    <th>Address</th>
    <td>
        <a href="https://www.google.com/maps/search/<?= urlencode($address->addressStreet . ', ' . $address->addressPostcode . ', ' . $address->addressCity . ', ' . $address->addressState) ?>" target="_blank">
            <?= $address->addressStreet . ', ' . $address->addressPostcode . ', ' . $address->addressCity . ', ' . $address->addressState ?>
        </a>
    </td>
</tr>
</table>

<br>
<button data-get="member_list.php">Back</button>

<?php
include '../../../../_foot.php';