<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------

$fields = [
    'feedback_id' => 'Feedback ID',
    'member_id' => 'Member ID',
    'order_id' => 'Order ID',
    'product_satisfaction' => 'Product Satisfaction',
    'service_satisfaction' => 'Service Satisfaction',
    'team_satisfaction' => 'Team Satisfaction',
    'improvement_suggestions' => 'Improvement Suggestions',
    'submit_time' => 'Submission Time'
];

// Retrieve search parameters
$member_id = req('member_id');

// Retrieve sort parameters
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'feedback_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// SQL query with filters and sorting
$f_stm = $_db->prepare('SELECT * FROM feedback
                      WHERE member_id LIKE ?
                      ORDER BY ' . $sort . ' ' . $dir);

$f_stm->execute(["%$member_id%"]);
$fb = $f_stm->fetchAll();

//-----------------------------------------------------------------------------

$_title = 'Member Feedback';
include '../../../../_head.php';
?>
<link rel="stylesheet" href="/css/yj_app.css">
<div class="search-bar">
    <form>
        <?= html_search('member_id', 'placeholder="Enter member ID to search"') ?>
        <button>Search</button>
    </form>
</div>

<div class="top-heading-space">
    <h2>Member Feedback</h2>
</div>

<?php if (empty($fb)): ?>
    <p>No feedback(s) found.</p>
<?php else: ?>
    <table class="table">
        <tr>
            <td><?= count($fb) ?> feedback(s)</td>
        </tr>
        <tr>
            <?= table_headers($fields, $sort, $dir) ?>
        </tr>

        <?php foreach ($fb as $f): ?>
            <tr>
                <td><?= $f->feedback_id ?></td>
                <td><a href="../member_management/member_list.php?id=<?= $f->member_id ?>"><?= $f->member_id ?></a></td>
                <td><a href="../order_management/order_list.php?id=<?= $f->order_id ?>"><?= $f->order_id ?></a></td>
                <td><?= $f->product_satisfaction ?></td>
                <td><?= $f->service_satisfaction ?></td>
                <td><?= $f->team_satisfaction ?></td>
                <td><?= $f->improvement_suggestions ?></td>
                <td><?= $f->submit_time ?></td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>

<button data-get="member_feedback.php">All Feedback(s)</button>
<br/><br/><br/>

<?php
include '../../../../_foot.php';
?>
