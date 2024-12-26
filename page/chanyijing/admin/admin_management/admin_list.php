<link rel="stylesheet" href="/css/yj_app.css">

<?php
session_start();

require '../../../../_base.php';
auth('admin');

$loggedInAdminTier = $_SESSION['admin_tier'] ?? 'Unknown'; 

//-----------------------------------------------------------------------------

$fields = [
    'admin_id'   => 'Admin ID',
    'admin_name' => 'Name',
    'admin_tier' => 'Tier'
];

// Retrieve search and filter parameters
$admin_name = req('admin_name');
$admin_tier = req('admin_tier');

// Retrieve sort parameters
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'admin_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// SQL query with filters and sorting
$a_stm = $_db->prepare('SELECT * FROM admin 
                      WHERE admin_name LIKE ?
                      AND (admin_tier = ? OR ?)
                      ORDER BY ' . $sort . ' ' . $dir);
                      
$a_stm->execute(["%$admin_name%", $admin_tier, $admin_tier == null]);
$admins= $a_stm->fetchAll();

//-----------------------------------------------------------------------------

$_title = 'Admin List';
include '../../../../_head.php';

?>

<!-- Seach Bar -->
<div class="search-bar">
    <form>
        <?= html_search('admin_name', 'placeholder="Enter name to search"') ?>
        <?= html_select('admin_tier', $_adminTiers, 'All Tiers') ?>
    <button>Search</button>
    </form>
</div>

<div class="top-heading-space">
    <h2>Admin List</h2>
</div>

<?php if (empty($admins)): ?>
    <p>No admin(s) found.</p>
    <?php else: ?>
        <table class="table">
            <tr>
                <td><?= count($admins) ?> admin(s)</td>
            </tr>
            <tr>
                <?= table_headers($fields, $sort, $dir) ?>
                <th>
            </tr>

            <?php foreach ($admins as $a): ?>
                <tr>
                    <td><?= $a->admin_id ?></td>
                    <td><?= $a->admin_name ?></td>
                    <td><?= $a->admin_tier ?></td>
                    <td>
                    <button data-get="admin_detail.php?admin_id=<?= $a->admin_id ?>">View Detail</button>
                    <button data-get="admin_update.php?admin_id=<?= $a->admin_id ?>" class="green-btn">Update Info</button>
                    <?php if ($loggedInAdminTier === 'High'): ?>
                        <button data-post="admin_delete.php?admin_id=<?= $a->admin_id ?>" data-confirm class="red-btn">Delete Admin</button>
                    <?php endif ?>

                    </td>
                </tr>
            <?php endforeach ?>
        </table>
<?php endif ?>

<button data-get="admin_list.php">All Admin(s)</button>
<br/><br/><br/>

<?php
include '../../../../_foot.php';