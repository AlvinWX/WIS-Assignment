<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------

$fields = [
    'memberID'   => 'Member ID',
    'memberName' => 'Name',
    'memberDateJoined' => 'Date Joined',
    'memberGender' => 'Gender',
];

// Retrieve search and filter parameters
$memberName = req('memberName');
$memberGender = req('memberGender');

// Retrieve sort parameters
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'memberID';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// SQL query with filters and sorting
$m_stm = $_db->prepare('SELECT * FROM member 
                      WHERE memberName LIKE ?
                      AND (memberGender = ? OR ?)
                      ORDER BY ' . $sort . ' ' . $dir);

$m_stm->execute(["%$memberName%", $memberGender, $memberGender == null]);
$members= $m_stm->fetchAll();

//-----------------------------------------------------------------------------

$_title = 'Member List';
include '../../../../_head.php';
?>

<!-- Seach Bar -->
<div class="search-bar">
    <form>
        <?= html_search('memberName', 'placeholder="Enter name to search"') ?>
        <?= html_select('memberGender', $_genders, 'All Genders') ?>
        <button>Search</button>
    </form>
</div>

<div class="top-heading-space">
    <h2>Member List</h2>
</div>

<?php if (empty($members)): ?>
    <p>No member(s) found.</p>
<?php else: ?>
        <table class="table">
            <tr>
                <td><?= count($members) ?> member(s)</td>
                <td>
            </tr>
            <tr>
                <?= table_headers($fields, $sort, $dir) ?>
                <th>
            </tr>

            <?php foreach ($members as $m): ?>
                <tr>
                    <td><?= $m->memberID ?></td>
                    <td><?= $m->memberName ?></td>
                    <td><?= $m->memberDateJoined ?></td>
                    <td><?= $m->memberGender ?></td>
                    <td>
                    <button data-get="member_detail.php?memberID=<?= $m->memberID ?>">View Detail</button>
                    <button data-get="member_update.php?memberID=<?= $m->memberID ?>" class="green-btn">Update Info</button>
                    <button data-post="member_delete.php?memberID=<?= $m->memberID ?>" data-confirm class="red-btn">Delete Member</button>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
<?php endif ?>

<button data-get="member_list.php">All Member(s)</button>
<br/><br/><br/>

<?php
include '../../../../_foot.php';