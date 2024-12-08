<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

$arr = $_db->query('SELECT * FROM member')->fetchAll();

$memberName = req('memberName');
$stm = $_db->prepare('SELECT * FROM member WHERE memberName LIKE ?');
$stm->execute(["%$memberName%"]);
$arr= $stm->fetchAll();

$_title = 'Member List';
include '../../../../_head.php';
?>

<form>
    <?= html_search('memberName') ?>
    <button>Search name</button>
</form>

<p><?= count($arr) ?> member(s)</p>


<table class="table">
    <tr>
        <th>Member ID</th>
        <th>Name</th>
        <th>Date Joined</th>
        <th></th>
    </tr>

    <?php foreach ($arr as $s): ?>
        <tr>
            <td><?= $s->memberID ?></td>
            <td><?= $s->memberName ?></td>
            <td><?= $s->memberDateJoined ?></td>
            <td>
            <button data-get="member_detail.php?memberID=<?= $s->memberID ?>">View Detail</button>
            <button data-get="member_update.php?memberID=<?= $s->memberID ?>">Update</button>
            <button data-post="member_delete.php?memberID=<?= $s->memberID ?>"data-confirm>Delete</button>
            </td>
        </tr>
        <?php endforeach ?>
</table>

<br>
<button data-get="member_list.php">Back</button>

<?php
include '../../../../_foot.php';