<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------

$fields = [
    'order_id'   => 'Order ID',
    'order_date' => 'Order Date',
    'order_total' => 'Total(RM)',
    'order_status' => 'Order Status',
];

// Retrieve search and filter parameters
$order_id = req('order_id');
$order_status = req('order_status');

// Retrieve sort parameters
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'order_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$order_id = $order_id ?: '';  
$order_status = $order_status ?: '';

// SQL query with filters and sorting
$stm = $_db->prepare('SELECT * FROM `order`
                      WHERE (order_id LIKE ? OR ? = "")
                      AND (order_status = ? OR ? = "")
                      ORDER BY ' . $sort . ' ' . $dir);

$stm->execute([$order_id, $order_id, $order_status, $order_status]);

$arr= $stm->fetchAll();

//-----------------------------------------------------------------------------

$_title = 'Order List';
include '../../../../_head.php';
?>

<!-- Seach Bar -->
<div class="search-bar">
    <form>
        <?= html_search('order_id', 'placeholder="Enter order ID to search"') ?>
        <?= html_select('order_status', $_orderStatuses, 'All Statuses') ?>
        <button>Search</button>
    </form>
</div>

<table class="table">
    <tr>
        <th>Order List</th>
        <td><td><td>
        <td><?= count($arr) ?> order(s)</td>
    </tr>
    <tr>
        <?= table_headers($fields, $sort, $dir) ?>
        <th>
    </tr>

    <?php foreach ($arr as $o): ?>
        <tr>
            <td><?= $o->order_id ?></td>
            <td><?= $o->order_date ?></td>
            <td><?= $o->order_total ?></td>
            <td><?= $o->order_status ?></td>
            <td>
            <button data-get="order_detail.php?order_id=<?= $o->order_id ?>">Order Detail</button>
            <button data-get="order_update.php?order_id=<?= $o->$order_id ?>">Update Status</button>
            <button data-post="order_delete.php?order_id=<?= $o->order_id ?>"data-confirm>Delete</button>
            </td>
        </tr>
    <?php endforeach ?>
</table>


<?php
include '../../../../_foot.php';