<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------
$user = $_SESSION['user'] ?? null;
$memberID = $user->memberID;

// Retrieve search and filter parameters
$order_id = req('order_id');
$order_status = req('order_status');

// SQL query with filters and sorting
$o_stm = $_db->prepare('SELECT * FROM `order`
                      WHERE memberID = ?
                      AND (order_id = ? OR ? = "")
                      AND (order_status = ? OR ? = "")');
$o_stm->execute([$memberID, $order_id, $order_id, $order_status, $order_status]);

// Fetch all orders
$orders = $o_stm->fetchAll(PDO::FETCH_ASSOC);

// Prepare statement for fetching order products
$op_stm = $_db->prepare('SELECT * FROM order_product WHERE order_id = ?');

// Prepare statement for fetching product details
$prod_stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');


//-----------------------------------------------------------------------------

$_title = 'Order History';
include '../../../../_head.php';
?>

<!-- Search Bar -->
<div class="search-bar">
    <form>
        <?= html_search('order_id', 'placeholder="Enter order ID to search"') ?>
        <?= html_select('order_status', $_orderStatuses, 'All Statuses') ?>
        <button>Search</button>
    </form>
</div>

<div class="top-heading-space">
    <h2>Order History</h2>
</div>

<?php if (empty($orders)): ?>
    <p>No orders found.</p>
<?php else: ?>
    <?php foreach ($orders as $o): ?>
        <table class="table">
            <tr>
                <th colspan="8">Order ID: <?= $o['order_id'] ?></th>
            </tr>
            <tr>
                <td>Order Date: </td>
                <td><?= $o['order_date'] ?></td>
                <td colspan="4"></td>
                <td>Order Status: </td>
                <td><?= $o['order_status'] ?></td>
            </tr>

            <?php
            // Fetch order products for this order
            $op_stm->execute([$o['order_id']]);
            $order_products = $op_stm->fetchAll(PDO::FETCH_ASSOC);
            foreach ($order_products as $op):
                // Fetch product details
                $prod_stm->execute([$op['product_id']]);
                $prod = $prod_stm->fetch(PDO::FETCH_ASSOC);

                $product_subtotal = $op['order_product_quantity'] * $op['order_product_price'];
            ?>
                <tr>
                    <td><img src="../../../../images/<?= $prod['product_img'] ?>" alt="<?= $prod['product_name'] ?>" style="width: 100px;"></td>
                    <td><?= $prod['product_name'] ?></td>
                    <td>Quantity: </td>
                    <td><?= $op['order_product_quantity'] ?></td>
                    <td>Price: </td>
                    <td>RM <?= number_format($op['order_product_price'], 2) ?></td>
                    <td>Subtotal: </td>
                    <td>RM <?= number_format($product_subtotal, 2) ?></td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <td colspan="6"></td>
                <td>Order Total: </td>
                <td>RM <?= number_format($o['order_total'], 2) ?></td>
            </tr>
            <tr>
                <td colspan="7">
                <td><button data-get="history_detail.php?order_id=<?= $o['order_id'] ?>">View More</button></td>
            </tr>
        </table>
    <?php endforeach ?>
<?php endif ?>

<button data-get="history_list.php">All Order(s)</button>

<?php
include '../../../../_foot.php';
?>
