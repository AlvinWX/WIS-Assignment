<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
$id = req('id');

$stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
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
        <td><?= $s->product_id ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $s->product_name ?></td>
    </tr>
    <tr>
        <th>Description</th>
        <td><?= $s->product_desc ?></td>
    </tr>
    <tr>
        <th>Price</th>
        <td><?= $s->product_price ?></td>
    </tr>
    <tr>
        <th>Stock Left</th>
        <td><?= $s->product_stock ?></td>
    </tr>
</table>

<br>

<button data-get="/page/yongqiaorou/product.php"  class="back_button"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

<?php
include '../../_admin_foot.php';