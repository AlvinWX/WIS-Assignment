<?php
require '../../_base.php';
//-----------------------------------------------------------------------------


$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

$arr = $_db->query('SELECT * FROM voucher v WHERE v.voucher_status = 1;')->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Voucher List';
include '../../_admin_head.php';
?>
<form method="GET" action="" class="search-form"  style="text-align: right; margin-bottom: 20px;">
    <select name="search_field">
        <option value="voucher_name" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'voucher_name' ? 'selected' : '' ?>>Voucher Name</option>
    </select>
    
    <input type="text" name="search" placeholder="Search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
    <button type="submit">Search</button>
</form>

<?php
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : 'voucher_name';
$search_value = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM voucher v WHERE v.voucher_status = 1 ";

if ($search_value) {
    $query .= " AND $search_field LIKE :search_value"; 
    $stmt = $_db->prepare($query);
    $stmt->bindValue(':search_value', '%' . $search_value . '%');
} else {
    $stmt = $_db->prepare($query);
}

$stmt->execute();
$arr = $stmt->fetchAll();

if(count($arr)) {?>
<p><?= count($arr) ?> record(s)</p>

<div style="text-align: center; margin-top: 20px;">
<table class="table" style="margin-left: auto; margin-right: auto;">
    <tr>
        <th>Id</th>
        <th>Voucher Name</th>
        <th>Description</th>
        <th>Points to Redeem</th>
        <th>Min Spend</th>
        <th>Discounts</th>
        <th>Voucher Image</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($arr as $v): ?>
    <tr>
        <td><?= $v->voucher_id ?></td>
        <td><?= $v->voucher_name ?></td>
        <td><?= $v->voucher_desc ?></td>
        <td><?= $v->voucher_points ?></td>
        <td><?= $v->voucher_min_spend ?></td>
        <td><?= $v->voucher_discount ?></td>
        <td><img src="../../images/voucher_pic/<?= $v->voucher_img ?>"></td>
        <td>
            <button data-get="voucher_detail.php?id=<?= $v->voucher_id ?>">Detail</button><br>
            <button data-get="voucher_update.php?id=<?= $v->voucher_id ?>">Update</button><br>
            <button data-post="voucher_delete.php?id=<?= $v->voucher_id ?> "data-confirm>Delete</button>
        </td>
    </tr>
    <?php endforeach ?>
</table>
</div> 
<?php }else{?>
    <p style="color:red;">No record found.</p>
<?php }?>
<a href="voucher_recover.php"><span id="dot" class="dot_left" style="color:rgb(245, 167, 167);"><i class="fa fa-trash" aria-hidden="true"></i></span></a>
<a href="voucher_add.php"><span id="dot" class="dot_right"><i class="fa fa-plus" aria-hidden="true"></i></span></a>

<?php
include '../../_foot.php';