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

//Paging
$page = req('page', 1);

require_once '../../../../lib/SimplePager.php';
$p = new SimplePager("SELECT * FROM admin ORDER BY $sort $dir", [], 10, $page);
$arr = $p->result;

// $arr = $_db->query("SELECT * FROM admin ORDER BY $sort $dir")->fetchAll();

//-----------------------------------------------------------------------------

$_title = 'Admin List';
include '../../../../_head.php';
?>

<!-- Seach Bar -->
<div class="search-bar">
    <?= html_search('adminName', 'placeholder="Enter name to search"') ?>
    <?= html_select('adminTier', $_adminTiers, 'All Tiers') ?>
    <button>Search</button>
</div>

<table class="table">
    <tr>
        <th>Admin List</th>
        <td><?= $p->count ?> of <?= $p->item_count ?> admin(s)</td>
        <td>
        <td>Page <?= $p->page ?> of <?= $p->page_count ?></td>
    </tr>
    <tr>
        <?= table_headers($fields, $sort, $dir, "page=$page") ?>
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
<?= $p->html("sort=$sort&dir=$dir") ?>

<?php
include '../../../../_foot.php';