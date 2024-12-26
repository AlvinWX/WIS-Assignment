<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

if (is_post()) {
    $id         = req('id');
    $stm = $_db->prepare('UPDATE category SET category_status=1, category_last_update = ?, admin_id = ? WHERE category_id = ? ');
    $stm->execute([date("Y-m-d H:i:s"), $admin_id, $id]);

    temp('info', 'Category recovered');

    // redirect('/page/yongqiaorou/category.php');
}
// // TODO
$arr = $_db->query('SELECT * FROM category WHERE category_status=0')->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Category Recover';
include '../../_admin_head.php';
?>

<?php if(count($arr)) {?>
<p><?= count($arr) ?> record(s)</p>
<table class="table" style="margin: auto;">
    <tr>
        <th>Id</th>
        <th>Category Name</th>
        <th>Description</th>
        <th>Action</th>
    </tr>

    <?php foreach ($arr as $c): ?>
    <tr>
        <td><?= $c->category_id ?></td>
        <td><?= $c->category_name ?></td>
        <td><?= $c->category_desc ?></td>
        <td>
            <button  data-post="category_recover.php?id=<?= $c->category_id ?>"  style="width:200px">Recover Back</button>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php }else{?>
    <p class="err">No record deleted.</p>
<?php }?>

<button data-get="/page/yongqiaorou/category.php"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

<?php
include '../../_foot.php';