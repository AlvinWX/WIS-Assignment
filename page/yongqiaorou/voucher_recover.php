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
    $stm = $_db->prepare('UPDATE voucher SET voucher_status=1, voucher_last_update = ?, admin_id = ? WHERE voucher_id = ? ');
    $stm->execute([date("Y-m-d H:i:s"), $admin_id, $id]);

    temp('info', 'Voucher recovered');

    redirect('/page/yongqiaorou/voucher.php');
}
// // TODO
$arr = $_db->query('SELECT * FROM voucher WHERE voucher_status=0')->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'voucher Recover';
include '../../_admin_head.php';
?>

<button data-get="/page/yongqiaorou/voucher.php"  class="back_button"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

<?php if(count($arr)) {?>
<p><?= count($arr) ?> record(s)</p>
<table class="table" style="margin: auto;">
    <tr>
        <th>Id</th>
        <th>Voucher Name</th>
        <th>Description</th>
        <th>Action</th>
    </tr>

    <?php foreach ($arr as $v): ?>
    <tr>
        <td><?= $v->voucher_id ?></td>
        <td><?= $v->voucher_name ?></td>
        <td><?= $v->voucher_desc ?></td>
        <td>
            <button  data-post="voucher_recover.php?id=<?= $v->voucher_id ?>"  style="width:200px">Recover Back</button>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php }else{?>
    <p style="color:red;">No record deleted.</p>
<?php }?>
<?php
include '../../_foot.php';