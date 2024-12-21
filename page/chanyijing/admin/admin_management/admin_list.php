<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------

$fields = [
    'adminID'   => 'Admin ID',
    'adminName' => 'Name',
    'adminTier' => 'Tier'
];

// Retrieve search and filter parameters
$adminName = req('adminName');
$adminTier = req('adminTier');

// Retrieve sort parameters
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'adminID';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// SQL query with filters and sorting
$stm = $_db->prepare('SELECT * FROM admin 
                      WHERE adminName LIKE ?
                      AND (adminTier = ? OR ?)
                      ORDER BY ' . $sort . ' ' . $dir);
$stm->execute(["%$adminName%", $adminTier, $adminTier == null]);
$arr= $stm->fetchAll();

//-----------------------------------------------------------------------------

$_title = 'Admin List';
include '../../../../_head.php';
?>

<!-- Seach Bar -->
<div class="search-bar">
    <form>
        <?= html_search('adminName', 'placeholder="Enter name to search"') ?>
        <?= html_select('adminTier', $_adminTiers, 'All Tiers') ?>
    <button>Search</button>
    </form>
</div>

<table class="table">
    <tr>
        <th>Admin List</th>
        <td><td>
        <td><?= count($arr) ?> admin(s)</td>
    </tr>
    <tr>
        <?= table_headers($fields, $sort, $dir) ?>
        <th>
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

<button data-get="admin_list.php">All Admin(s)</button>
<br/><br/><br/>

<?php
include '../../../../_foot.php';