<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

$arr = $_db->query('SELECT * FROM admin')->fetchAll();

$adminName = req('adminName');
$stm = $_db->prepare('SELECT * FROM admin WHERE adminName LIKE ?');
$stm->execute(["%$adminName%"]);
$arr= $stm->fetchAll();

$_title = 'Admin List';
include '../../../../_head.php';
?>

<form>
    <?= html_search('adminName') ?>
    <button>Search name</button>
</form>

<p><?= count($arr) ?> admin(s)</p>

<table class="table">
    <tr>
        <th>Admin ID</th>
        <th>Name</th>
        <th>Tier</th>
        <th></th>
    </tr>

    <?php foreach ($arr as $s): ?>
        <tr>
            <td><?= $s->adminID ?></td>
            <td><?= $s->adminName ?></td>
            <td><?= $s->adminTier ?></td>
            <td>
            <button data-get="admin_detail.php?adminID=<?= $s->adminID ?>">View Detail</button>
            <button data-get="admin_update.php?adminID=<?= $s->adminID ?>">Update</button>
            <button data-post="admin_delete.php?adminID=<?= $s->adminID ?>"data-confirm>Delete</button>
            </td>
        </tr>
        <?php endforeach ?>
</table>

<?php
include '../../../../_foot.php';