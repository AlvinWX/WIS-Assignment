<?php
require '../../_base.php';
//-----------------------------------------------------------------------------
if (is_post()) {
    $id         = req('id');
    $stm = $_db->prepare('UPDATE category SET category_status=1 WHERE category_id = ? ');
    $stm->execute([$id]);

    temp('info', 'Category recovered');

    redirect('/page/yongqiaorou/category.php');
}
// // TODO
$arr = $_db->query('SELECT * FROM category WHERE category_status=0')->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Product List';
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
    <p style="color:red;">No record deleted.</p>
<?php }?>
<?php
include '../../_admin_foot.php';