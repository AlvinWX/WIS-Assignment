<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

// ----------------------------------------------------------------------------
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (!$order_id) {
    die('Error: Order ID is required.');
}

// Fetch order details
$o_stm = $_db->prepare('SELECT * FROM `order` WHERE order_id = ?');
$o_stm->execute([$order_id]);
$o = $o_stm->fetch();

if (!$o) {
    die('Error: No order found for the given Order ID.');
}

$m = null; 

if (is_post()) {
    $email = req('email');

    $m_stm = $_db->prepare('SELECT * FROM member WHERE memberID = ?');
    $m_stm->execute([$o->memberID]);
    $m = $m_stm->fetch();

    // Fetching shipping address, order products, etc.
    $sa_stm = $_db->prepare('SELECT * FROM shipping_address WHERE shipping_address_id = ?');
    $sa_stm->execute([$o->shipping_address_id]);
    $sa = $sa_stm->fetch();

    $op_stm = $_db->prepare('SELECT * FROM order_product WHERE order_id = ?');
    $op_stm->execute([$o->order_id]);
    $order_products = $op_stm->fetchAll(PDO::FETCH_ASSOC);

    // Email subject and recipient
    $member_name = $m->memberName;
    $subject = "Receipt for Order ID #$order_id";

    // Generate HTML content for the email body
    ob_start(); 
    ?>
    <h2>Order Receipt</h2>
    <p>Dear <?= $member_name ?>,</p>
    <p>Thank you for your purchase. Below are the details of your order:</p>

    <h3>Order Information</h3>
    <table class="order-listing-table">
        <tr>
            <th>Order ID</th>
            <td><?= $o->order_id ?></td>
        </tr>
        <tr>
            <th>Order Date</th>
            <td><?= $o->order_date ?></td>
        </tr>
        <tr>
            <th>Shipping Address</th>
            <td><?= $sa->shipping_address_street . ', ' . $sa->shipping_address_postcode . ', ' . $sa->shipping_address_city . ', ' . $sa->shipping_address_state ?></td>
        </tr>
    </table>

    <h3>Products</h3>
    <table class="order-listing-table">
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price (RM)</th>
            <th>Subtotal (RM)</th>
        </tr>
        <?php foreach ($order_products as $op): ?>
            <tr>
                <td><?= $op['product_name'] ?></td>
                <td><?= $op['order_product_quantity'] ?></td>
                <td><?= number_format($op['order_product_price'], 2) ?></td>
                <td><?= number_format($op['order_product_quantity'] * $op['order_product_price'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Order Summary</h3>
    <table class="order-listing-table">
        <tr>
            <th>Subtotal (RM)</th>
            <td><?= number_format($o->order_subtotal, 2) ?></td>
        </tr>
        <tr>
            <th>Delivery Fee (RM)</th>
            <td><?= number_format($o->order_delivery_fee, 2) ?></td>
        </tr>
        <tr>
            <th>Total (RM)</th>
            <td><?= number_format($o->order_total, 2) ?></td>
        </tr>
    </table>

    <p>Best Regards,<br/>TAR GROCER Team</p>
    <?php
    $body = ob_get_clean();

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    } else if (strlen($email) > 100) {
        $_err['email'] = 'Maximum 100 characters';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }

    // Send email
    if (!$_err) {
        $m = get_mail();
        $m->addAddress($email);
        $m->Subject = $subject;
        $m->Body = $body;
        $m->isHTML(true); 
        $m->send();

        temp('info', 'Email sent');
        redirect();
    }
}

// ----------------------------------------------------------------------------

$_title = 'Send Receipt';
include '../../../../_head.php';
?>

<div class="top-heading-space">
    <h3>Send Receipt</h3>
</div>

<div class="order-card">
    <form class="form" method="post">
        <div class="email-form">
            <label for="email"><h3>Email</h3></label>
            <?= html_text('email', 'maxlength="100"') ?>
            <?= err('email') ?>
        </div>

        <div class="email-form">
            <section>
                <button>Send</button>
                <button data-get="history_detail.php?order_id=<?= $o->order_id ?>">Back</button>
            </section>
        </div>
    </form>
</div>

<?php
include '../../../../_foot.php';
?>
