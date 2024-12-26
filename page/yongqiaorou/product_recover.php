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
    $stm = $_db->prepare('UPDATE product SET product_status=1, admin_id = ?, product_last_update = ? WHERE product_id = ? ');
    $stm->execute([$admin_id, date("Y-m-d H:i:s"), $id]);

    temp('info', 'Product recovered');

    redirect('/page/yongqiaorou/product.php');
}
// // TODO
$arr = $_db->query('SELECT * FROM product WHERE product_status=0')->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Product Recover';
include '../../_admin_head.php';
?>

<?php if(count($arr)) {?>
<p><?= count($arr) ?> record(s)</p>
<table class="table">
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock Left</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($arr as $s): ?>
    <tr>
        <td><?= $s->product_id ?></td>
        <td><?= $s->product_name ?></td>
        <td><?= $s->product_desc ?></td>
        <td><?= $s->product_price ?></td>
        <td><?= $s->product_stock ?></td>
        <td>
            <!-- TODO -->
            <button  data-post="product_recover.php?id=<?= $s->product_id ?>" style="width:200px;">Recover Back</button>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php }else{?>
    <p style="color:red;">No record deleted.</p>
<?php }?>

<button data-get="/page/yongqiaorou/product.php"  class="back_button"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>
<?php
include '../../_admin_foot.php';