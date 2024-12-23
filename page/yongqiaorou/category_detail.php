<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

$id = req('id');

$stm = $_db->prepare('SELECT * FROM category WHERE category_id = ?');
$stm->execute([$id]);
$s = $stm->fetch();

if (!$s) {
    redirect('/');
}

// ----------------------------------------------------------------------------
$_title = 'Detail';
include '../../_admin_head.php';
?>

<table class="table detail" style="margin-top: 100px">
    <tr>
        <th>Id</th>
        <td><?= $s->category_id ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $s->category_name ?></td>
    </tr>
    <tr>
        <th>Description</th>
        <td><?= $s->category_desc ?></td>
    </tr>
</table>

<br>

<button data-get="/page/yongqiaorou/category.php"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

<?php
include '../../_admin_foot.php';