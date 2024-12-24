<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

auth('admin');
$admin_id = req('admin_id');
$stm = $_db->prepare('SELECT * FROM admin WHERE admin_id = ?');
$stm->execute([$admin_id]);
$s = $stm->fetch();

if(!$s){
    redirect('admin_list.php');
}

$_title = 'Admin Detail';
include '../../../../_head.php';
?>

<div class="top-heading-space">
    <h3>Admin Detail</h3>
</div>

<table class="table">
    <tr>
        <th>Profile Picture</th>
        <td class="profile-pic-container">
            <img src="../../../../images/profile_pic/<?=$s->admin_profile_pic?>"></td>
        <td>
    </tr>
    <tr>
        <th>Admin ID</th>
        <td><?= $s->admin_id ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $s->admin_name ?></td>
    </tr>
    <tr>
        <th>Tier</th>
        <td><?= $s->admin_tier ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><a href="mailto:<?= $s->admin_email ?>"><?= $s->admin_email ?></a></td>
    </tr>
    <tr>
        <th>Phone</th>
        <td><a href="tel:<?= $s->admin_phone ?>"><?= $s->admin_phone ?></a></td>
    </tr>
    <tr>
        <th>Gender</th>
        <td><?= $s->admin_gender ?></td>
    </tr>
</table>

<br>

<button data-get="admin_list.php">Back</button>

<?php
include '../../../../_foot.php';