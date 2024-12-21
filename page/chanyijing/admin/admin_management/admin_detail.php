<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

$adminID = req('adminID');
$stm = $_db->prepare('SELECT * FROM admin WHERE adminID = ?');
$stm->execute([$adminID]);
$s = $stm->fetch();

if(!$s){
    redirect('admin_list.php');
}

$_title = 'Admin Detail';
include '../../../../_head.php';
?>

<table class="table">
    <tr>
        <th>Profile Picture</th>
        <td class="profile-pic-container">
            <img src="../../../../images/profile_pic/<?=$s->adminProfilePic?>"></td>
        <td>
    </tr>
    <tr>
        <th>Admin ID</th>
        <td><?= $s->adminID ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $s->adminName ?></td>
    </tr>
    <tr>
        <th>Tier</th>
        <td><?= $s->adminTier ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= $s->adminEmail ?></td>
    </tr>
    <tr>
        <th>Phone</th>
        <td><?= $s->adminPhone ?></td>
    </tr>
    <tr>
        <th>Gender</th>
        <td><?= $s->adminGender ?></td>
    </tr>
</table>

<br>

<button data-get="admin_list.php">Back</button>

<?php
include '../../../../_foot.php';