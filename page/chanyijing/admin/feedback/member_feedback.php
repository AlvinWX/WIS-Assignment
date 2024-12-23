<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------

$fields = [
    'feedback_id' => 'Feedback ID',
    'memberID' => 'Member ID',
    'product_satisfaction' => 'Product Satisfaction',
    'service_satisfaction' => 'Service Satisfaction',
    'team_satisfaction' => 'Team Satisfaction',
    'improvement_suggestions' => 'Improvement Suggestions',
    'submit_time' => 'Submission Time'
];

// Retrieve search parameters
$feedback_id = req('feedback_id');

// Retrieve sort parameters
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'feedback_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'desc';

// SQL query with filters and sorting
$f_stm = $_db->prepare('SELECT * FROM feedback
                      WHERE feedback_id LIKE ?
                      ORDER BY ' . $sort . ' ' . $dir);

$f_stm->execute(["%$feedback_id%"]);
$fb= $f_stm->fetchAll();

//-----------------------------------------------------------------------------

$_title = 'Member Feedback';
include '../../../../_head.php';
?>

<div class="search-bar">
    <form>
        <?= html_search('feedback_id', 'placeholder="Enter feedback ID to search"') ?>
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
                <td><?= $f->memberID ?></td>
                <td><?= $f->product_satisfaction ?></td>
                <td><?= $f->service_satisfaction ?></td>
                <td><?= $f->team_satisfaction ?></td>
                <td><?= $f->improvement_suggestions ?></td>
                <td><?= $f->submit_time ?></td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>

<?php
include '../../../../_foot.php';
