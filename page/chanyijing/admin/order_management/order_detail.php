<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------
// Fetch order details 
$order_id = req('order_id');
$o_stm = $_db->prepare('SELECT * FROM `order` 
                    WHERE order_id = ?');
$o_stm->execute([$order_id]);
$o = $o_stm->fetch();

if (!$o) {
    redirect('order_list.php');
}

// Fetch member details
$m_stm = $_db->prepare('SELECT * FROM member WHERE memberID = ?');
$m_stm->execute([$o->memberID]);
$m = $m_stm->fetch();

// Fetch shipping address
$sa_stm = $_db->prepare('SELECT * FROM shipping_address WHERE shipping_address_id = ?');
$sa_stm->execute([$o->shipping_address_id]);
$sa = $sa_stm->fetch();

// Fetch payment details
$p_stm = $_db->prepare('SELECT * FROM payment WHERE order_id = ?');
$p_stm->execute([$order_id]);
$p = $p_stm->fetch();

// Fetch payment method details
$pm_stm = $_db->prepare('SELECT * FROM payment_method WHERE payment_method_id = ?');
$pm_stm->execute([$p->payment_method_id]);
$pm = $pm_stm->fetch();

// Fetch order products
$op_stm = $_db->prepare('SELECT * FROM order_product WHERE order_id = ?');
$op_stm->execute([$o->order_id]);
$order_products = $op_stm->fetchAll(PDO::FETCH_ASSOC);

if(is_post()){
    $order_status = req('order_status');

    $_err = [];

    // Validate order status
    if ($order_status == '') {
        $_err['order_status'] = 'Please select an order status';
    }

    // Output
    if (!$_err) {
        if ($order_status !== $o->order_status) {
            $stm = $_db->prepare('UPDATE `order` SET order_status = ? WHERE order_id = ?');
            $stm->execute([$order_status, $order_id]);

            if (in_array($order_status, ['Shipped', 'Delivered'])) {
                $current_time = date('Y-m-d H:i:s');
                
                if ($order_status == 'Shipped') {
                    $stm = $_db->prepare('UPDATE `order` SET order_ship_date = ? WHERE order_id = ?');
                    $stm->execute([$current_time, $order_id]);
                } elseif ($order_status == 'Delivered') {
                    $stm = $_db->prepare('UPDATE `order` SET order_received_date = ? WHERE order_id = ?');
                    $stm->execute([$current_time, $order_id]);
                }
            }

            if (in_array($order_status, ['Pending', 'Packed', 'Cancelled'])) {
                $stm = $_db->prepare('UPDATE `order` SET order_ship_date = NULL, order_received_date = NULL WHERE order_id = ?');
                $stm->execute([$order_id]);
            }

            temp('info', 'Order status updated successfully.');
        } else {
            temp('info', 'No change in order status.');
        }

        redirect('order_detail.php?order_id=' . $order_id);
    }
}

//-----------------------------------------------------------------------------

$_title = 'Order Detail';
include '../../../../_head.php';
?>

<div class="top-heading-space">
    <h3>Order Details for Order ID <?= $o->order_id ?></h3>
</div>

<!-- Customer Information Table -->
<table class="order-listing-table">
    <tr>
        <th colspan="2"><h3>Customer Information</h3></th>
    </tr>
    <tr>
        <th>Member ID</th>
        <td><a href="../member_management/member_detail.php?id=<?= $m->memberID ?>"><?= $m->memberID ?></a></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $m->memberName ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><a href="mailto:<?= $m->memberEmail ?>"><?= $m->memberEmail ?></a></td>
    </tr>
    <tr>
        <th>Phone</th>
        <td><a href="tel:<?= $m->memberPhone ?>"><?= $m->memberPhone ?></a></td>
    </tr>
    <tr>
    <th>Shipping Address</th>
    <td>
        <a href="https://www.google.com/maps/search/<?= urlencode($sa->shipping_address_street . ', ' . $sa->shipping_address_postcode . ', ' . $sa->shipping_address_city . ', ' . $sa->shipping_address_state) ?>" target="_blank">
            <?= $sa->shipping_address_street . ', ' . $sa->shipping_address_postcode . ', ' . $sa->shipping_address_city . ', ' . $sa->shipping_address_state ?>
        </a>
    </td>
</tr>

</table>

<!-- Order Details Table -->
<table class="order-listing-table">
    <tr>
        <th colspan="6"><h3>Order Details</h3></th>
    </tr>
    <tr>
        <th>Product ID</th>
        <th>Product Name</th>
        <th>Product Image</th>
        <th>Quantity</th>
        <th>Price (RM)</th>
        <th>Subtotal (RM)</th>
    </tr>

    <?php foreach ($order_products as $op): ?>
        <?php
        $prod_stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
        $prod_stm->execute([$op['product_id']]);
        $prod = $prod_stm->fetch(PDO::FETCH_ASSOC);

        // Calculate the subtotal for each product
        $product_subtotal = $op['order_product_quantity'] * $op['order_product_price'];
        ?>

        <tr>
            <td><?= $prod['product_id'] ?></td>
            <td><?= $prod['product_name'] ?></td>
            <td><img src="../../../../images/<?= $prod['product_img'] ?>" alt="<?= $prod['product_name'] ?>" style="width: 150px;"></td>
            <td><?= $op['order_product_quantity'] ?></td>
            <td><?= number_format($op['order_product_price'], 2) ?></td>
            <td><?= number_format($product_subtotal, 2) ?></td>
        </tr>
    <?php endforeach ?>
</table>

<!-- Order Status -->
<form method="post">
    <table class="order-listing-table">
        <tr>
            <th colspan="3"><h3>Update Order Status</h3></th>
        </tr>
        <tr>
            <td><?= html_select('order_status', $_orderStatuses, $o->order_status); ?></td>
            <td><?= err('order_status') ?></td>
            <td><button type="submit">Update Status</button></td>
        </tr>
    </table>
</form>

<!-- Order Summary Table -->
<table class="order-listing-table">
    <tr>
        <th colspan="2"><h3>Order Summary</h3></th>
    </tr>
    <tr>
        <th>Order ID</th>
        <td><?= $o->order_id ?></td>
    </tr>
    <tr>
        <th>Order Date & Time</th>
        <td><?= $o->order_date ?></td>
    </tr>
    <tr>
        <th>Order Ship Date</th>
        <td><?= $o->order_ship_date ?></td>
    </tr>
    <tr>
        <th>Order Delivered Date</th>
        <td><?= $o->order_received_date ?></td>
    </tr>
    <tr>
        <th>Subtotal (RM)</th>
        <td><?= number_format($o->order_subtotal, 2) ?></td>
    </tr>
    <tr>
        <th>Delivery Fee (RM)</th>
        <td><?= number_format($o->order_delivery_fee, 2) ?></td>
    </tr>
    <tr>
        <th>Tax (RM)</th>
        <td><?= number_format($o->order_tax, 2) ?></td>
    </tr>
    <tr>
        <th>Voucher Applied</th>
        <td><?= $o->order_voucher ?></td>
    </tr>
    <tr>
        <th>Discount (RM)</th>
        <td><?= number_format($o->order_discount_price, 2) ?></td>
    </tr>
    <tr>
        <th>Order Total (RM)</th>
        <td><?= number_format($o->order_total, 2) ?></td>
    </tr>
</table>

<!-- Payment Details Table -->
<table class="order-listing-table">
    <tr>
        <th colspan="2"><h3>Payment Details</h3></th>
    </tr>
    <tr>
        <th>Payment ID</th>
        <td><?= $p->payment_id ?></td>
    </tr>
    <tr>
        <th>Payment Date</th>
        <td><?= $p->payment_date ?></td>
    </tr>
    <tr>
        <th>Payment Method</th>
        <td><?= $pm->payment_method_name ?></td>
    </tr>
    <tr>
        <th>Payment Amount (RM)</th>
        <td><?= number_format($p->payment_amount, 2) ?></td>
    </tr>
</table>

<br>
<button data-get="order_list.php">Back</button>

<?php
include '../../../../_foot.php';
?>