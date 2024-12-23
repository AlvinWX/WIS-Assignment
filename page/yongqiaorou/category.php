<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

$arr = $_db->query('SELECT * FROM category c WHERE c.category_status = 1;')->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Category List';
include '../../_admin_head.php';
?>
<form method="GET" action="" class="search-form"  style="text-align: right; margin-bottom: 20px;">
    <select name="search_field">
        <option value="category_name" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'category_name' ? 'selected' : '' ?>>Category Name</option>
    </select>
    
    <input type="text" name="search" placeholder="Search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
    <button type="submit">Search</button>
</form>

<?php
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : 'category_name';
$search_value = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM category c WHERE c.category_status = 1 ";

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
        <th>Category Name</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($arr as $c): ?>
    <tr>
        <td><?= $c->category_id ?></td>
        <td><?= $c->category_name ?></td>
        <td><?= $c->category_desc ?></td>
        <td>
            <button data-get="category_detail.php?id=<?= $c->category_id ?>">Detail</button><br>
            <button data-get="category_update.php?id=<?= $c->category_id ?>">Update</button><br>
            <button data-post="category_delete.php?id=<?= $c->category_id ?> "data-confirm>Delete</button>
        </td>
    </tr>
    <?php endforeach ?>
</table>
</div> 
<?php }else{?>
    <p style="color:red;">No record found.</p>
<?php }?>
<a href="category_recover.php"><span id="dot" class="dot_left" style="color:rgb(245, 167, 167);"><i class="fa fa-trash" aria-hidden="true"></i></span></a>
<a href="category_add.php"><span id="dot" class="dot_right"><i class="fa fa-plus" aria-hidden="true"></i></span></a>

<?php
include '../../_admin_foot.php';