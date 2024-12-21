<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------

$order_id = req('order_id');
$o_stm = $_db->prepare('SELECT * FROM `order` WHERE order_id = ?');
$o_stm->execute([$order_id]);
$o = $o_stm->fetch();

if(!$o){
    redirect('order_list.php');
}

$m_stm = $_db->prepare('SELECT * FROM member WHERE memberID = ?');
$m_stm->execute([$o->memberID]);
$m = $m_stm->fetch();

$sa_stm = $_db->prepare('SELECT * FROM shipping_address WHERE shipping_address_id = ?');
$sa_stm->execute([$o->shipping_address_id]);
$sa = $sa_stm->fetch();

$p_stm = $_db->prepare('SELECT * FROM payment WHERE order_id = ?');
$p_stm->execute([$order_id]);
$p = $p_stm->fetch();

$pm_stm = $_db->prepare('SELECT * FROM payment_method WHERE payment_method_id = ?');
$pm_stm->execute([$p->payment_method_id]);
$pm = $pm_stm->fetch();

//havent finish yet
$fields = [
    'product_id'   => 'Product ID',
    'product_name' => 'Name',
    'product_img' => 'Image',
    'order_product_quantity' => 'Qty',
    'order_product_price' => 'Price (RM)'
];

//-----------------------------------------------------------------------------

$_title = 'Order Detail';
include '../../../../_head.php';
?>

<table class="order-listing-table">
    <tr>
        <th>Customer Information</th>
        <th>
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
        <td><?= $sa->shipping_address_street . ', ' . $sa->shipping_address_postcode . ', ' . $sa->shipping_address_city . ', ' . $sa->shipping_address_state ?></td>
    </tr>
</table>

<!-- order details here -->


<table class="order-listing-table">
    <tr>
        <th>Order Summary</th>
        <th>
    </tr>
    <tr>
        <th>Order ID</th>
        <td><?= $o->order_id ?></td>
    </tr>
    <tr>
        <th>Order Datetime</th>
        <td><?= $o->order_date ?></td>
    </tr>
    <tr>
        <th>Subtotal (RM)</th>
        <td><?= $o->order_subtotal ?></td>
    </tr>
    <tr>
        <th>Delivery Fee (RM)</th>
        <td><?= $o->order_delivery_fee ?></td>
    </tr>
    <tr>
        <th>Tax (RM)</th>
        <td><?= $o->order_tax ?></td>
    </tr>
    <tr>
        <th>Voucher Applied</th>
        <td><?= $o->order_voucher ?></td>
    </tr>
    <tr>
        <th>Discount (RM)</th>
        <td><?= $o->order_discount_price ?></td>
    </tr>
    <tr>
        <th>Order Total (RM)</th>
        <td><?= $o->order_total ?></td>
    </tr>
</table>

<table class="order-listing-table">
    <tr>
        <th>Payment Details</th>
        <th>
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
        <td><?= $p->payment_amount ?></td>
    </tr>
</table>


<br>
<button data-get="order_list.php">Back</button>

<?php
include '../../../../_foot.php';