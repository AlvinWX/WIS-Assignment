<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------

$fields = [
    'member_id'   => 'Member ID',
    'member_name' => 'Name',
    'member_date_joined' => 'Date Joined',
    'member_gender' => 'Gender',
];

// Retrieve search and filter parameters
$member_name = req('member_name');
$member_gender = req('member_gender');

// Retrieve sort parameters
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'member_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// SQL query with filters and sorting
$m_stm = $_db->prepare('SELECT * FROM member 
                      WHERE member_name LIKE ?
                      AND (member_gender = ? OR ?)
                      ORDER BY ' . $sort . ' ' . $dir);

$m_stm->execute(["%$member_name%", $member_gender, $member_gender == null]);
$members= $m_stm->fetchAll();

//-----------------------------------------------------------------------------

$_title = 'Member List';
include '../../../../_head.php';
?>

<!-- Seach Bar -->
<div class="search-bar">
    <form>
        <?= html_search('member_name', 'placeholder="Enter name to search"') ?>
        <?= html_select('member_gender', $_genders, 'All Genders') ?>
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
                    <td><?= $m->member_id ?></td>
                    <td><?= $m->member_name ?></td>
                    <td><?= $m->member_date_joined ?></td>
                    <td><?= $m->member_gender ?></td>
                    <td>
                    <button data-get="member_detail.php?member_id=<?= $m->member_id ?>">View Detail</button>
                    <button data-get="member_update.php?member_id=<?= $m->member_id ?>" class="green-btn">Update Info</button>
                    <button data-post="member_delete.php?member_id=<?= $m->member_id ?>" data-confirm class="red-btn">Delete Member</button>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
<?php endif ?>

<button data-get="member_list.php">All Member(s)</button>
<br/><br/><br/>

<?php
include '../../../../_foot.php';