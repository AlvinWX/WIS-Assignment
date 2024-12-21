<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------

$fields = [
    'memberID'   => 'Member ID',
    'memberName' => 'Name',
    'memberDateJoined' => 'Date Joined',
    'memberGender' => 'Gender'
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
$stm = $_db->prepare('SELECT * FROM member 
                      WHERE memberName LIKE ?
                      AND (memberGender = ? OR ?)
                      ORDER BY ' . $sort . ' ' . $dir);

$stm->execute(["%$memberName%", $memberGender, $memberGender == null]);

//Paging
$page = req('page', 1);

require_once '../../../../lib/SimplePager.php';
$p = new SimplePager("SELECT * FROM member ORDER BY $sort $dir", [], 10, $page);
$arr = $p->result;

// $arr = $_db->query("SELECT * FROM member ORDER BY $sort $dir")->fetchAll();

//-----------------------------------------------------------------------------

$_title = 'Member List';
include '../../../../_head.php';
?>

<div class="search-bar">
    <?= html_search('memberName', 'placeholder="Enter name to search"') ?>
    <?= html_select('memberGender', $_genders, 'All Genders') ?>
    <button>Search</button>
</div>

<table class="table">
    <tr>
        <th>Member List</th>
        <td><?= $p->count ?> of <?= $p->item_count ?> member(s)</td>
        <td><td>
        <td>Page <?= $p->page ?> of <?= $p->page_count ?></td>
    </tr>
    <tr>
        <?= table_headers($fields, $sort, $dir, "page=$page") ?>
        <th>
    </tr>

    <?php foreach ($arr as $s): ?>
        <tr>
            <td><?= $s->memberID ?></td>
            <td><?= $s->memberName ?></td>
            <td><?= $s->memberDateJoined ?></td>
            <td><?= $s->memberGender ?></td>
            <td>
            <button data-get="member_detail.php?memberID=<?= $s->memberID ?>">View Detail</button>
            <button data-get="member_update.php?memberID=<?= $s->memberID ?>">Update</button>
            <button data-post="member_delete.php?memberID=<?= $s->memberID ?>"data-confirm>Delete</button>
            </td>
        </tr>
    <?php endforeach ?>
</table>

<button data-get="member_list.php">All Member(s)</button>
<br/><br/><br/>
<?= $p->html("sort=$sort&dir=$dir") ?>

<?php
include '../../../../_foot.php';