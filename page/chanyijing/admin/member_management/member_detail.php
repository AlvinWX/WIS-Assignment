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

$_title = 'MemberDetail';
include '../../../../_head.php';
?>

<table class="table detail">
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
        <td><?= $s->memberEmail ?></td>
    </tr>
    <tr>
        <th>Phone</th>
        <td><?= $s->memberPhone ?></td>
    </tr>
    <tr>
        <th>Gender</th>
        <td><?= $s->memberGender ?></td>
    </tr>
    <tr>
        <th>Date Joined</th>
        <td><?= $s->memberDateJoined ?></td>
    </tr>
</table>

<br>

<button data-get="member_list.php">Back</button>

<?php
include '../../../../_foot.php';