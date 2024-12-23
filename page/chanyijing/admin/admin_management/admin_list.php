<link rel="stylesheet" href="/css/yj_app.css">

<?php
session_start();

require '../../../../_base.php';
auth('admin');

$loggedInAdminTier = $_SESSION['adminTier'] ?? 'Unknown'; 

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
$a_stm = $_db->prepare('SELECT * FROM admin 
                      WHERE adminName LIKE ?
                      AND (adminTier = ? OR ?)
                      ORDER BY ' . $sort . ' ' . $dir);
$a_stm->execute(["%$adminName%", $adminTier, $adminTier == null]);
$admins= $a_stm->fetchAll();

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

<div class="top-heading-space">
    <h2>Admin List</h2>
    <p>tier: <?= htmlspecialchars($loggedInAdminTier) ?></p>
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
                    <td><?= $a->adminID ?></td>
                    <td><?= $a->adminName ?></td>
                    <td><?= $a->adminTier ?></td>
                    <td>
                    <button data-get="admin_detail.php?adminID=<?= $a->adminID ?>">View Detail</button>
                    <button data-get="admin_update.php?adminID=<?= $a->adminID ?>">Update Info</button>
                    <?php if ($loggedInAdminTier === 'High'): ?>
                        <button data-post="admin_delete.php?adminID=<?= $a->adminID ?>" data-confirm class="delete-btn">Delete Admin</button>
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