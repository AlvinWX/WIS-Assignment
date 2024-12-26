<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

//-----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$member_id = $user->member_id;

// Fetch order details 
$order_id = req('order_id');
$o_stm = $_db->prepare('SELECT * FROM `order` WHERE order_id = ?');
$o_stm->execute([$order_id]);
$o = $o_stm->fetch();

// Fetch member details
$m_stm = $_db->prepare('SELECT * FROM member WHERE member_id = ?');
$m_stm->execute([$o->member_id]);
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

if (is_post()) {
    $email = req('email');
    $html    = false;
    $subject = "Order Receipt for Order ID #$order_id";

    ob_start(); 
    ?>
        <h2>Order Receipt for Order ID #<?= $o->order_id ?></h2>
        <p>Dear <?= $m->member_name ?>,</p><br/>
        <p>Thank you for your purchase. Below are the details of your order:</p><br/>

        <br/><hr/><h3>Order Details</h3><hr/>
        <p><strong>Order ID         :</strong> <?= $o->order_id ?></p>
        <p><strong>Order Date       :</strong> <?= $o->order_date ?></p>
        <p><strong>Customer Name    :</strong> <?= $m->member_name ?></p>
        <p><strong>Shipping Address :</strong> <?= $sa->street . ', ' . $sa->postcode . ', ' . $sa->city . ', ' . $sa->state ?></p>

        <br/><hr/><h3>Product(s)</h3><hr/>
        <?php foreach ($order_products as $op): ?>
            <?php
            $prod_stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
            $prod_stm->execute([$op['product_id']]);
            $prod = $prod_stm->fetch(PDO::FETCH_ASSOC);

            $product_subtotal = $op['quantity'] * $op['price'];
            ?>
            <p><strong>Product Name :</strong> <?= $prod['product_name'] ?></p>
            
            <p><strong>Quantity     :</strong> <?= $op['quantity'] ?></p>
            <p><strong>Price        :</strong>RM <?= number_format($op['price'], 2) ?></p>
            <p><strong>Subtotal     :</strong>RM <?= number_format($product_subtotal, 2) ?></p>
            <br/><hr/>
        <?php endforeach ?>

        <h3>Payment Details</h3><hr/>
        <p><strong>Order Subtotal :</strong>RM <?= number_format($o->order_subtotal, 2) ?></p>
        <p><strong>Tax            :</strong>RM <?= number_format($o->tax, 2) ?></p>
        <p><strong>Delivery Fee   :</strong>RM <?= number_format($o->delivery_fee, 2) ?></p>
        <p><strong>Subtotal       :</strong>RM <?= number_format($o->subtotal, 2) ?></p>

        <p><strong>Voucher Applied:</strong><?= $o->voucher ?></p>
        <p><strong>Discount       :</strong> - RM <?= number_format($o->discount_price, 2) ?></p>
        <p><strong>Points Earned  :</strong><?= $o->points ?></p>
        <b><p>Order Total    :RM <?= number_format($o->total, 2) ?></p></b>
        <hr/>
        <p><strong>Payment Method :</strong> <?= $pm->name ?></p>
        <p><strong>Payment Date   :</strong> <?= $p->date ?></p>
        <b><p>Payment Amount :RM <?= number_format($p->amount, 2) ?></p></b>

        <br/><p>Best Regards,<br/><br/><strong>TAR GROCER Team</strong></p><br/>

        <br/><p>Need help? Contact us : targrocer@gmail.com </p><br/>
    
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
        $m->isHTML(true); // Send as HTML email
        $m->send();

        temp('info', 'Email sent');
        redirect();
    }
}

// ----------------------------------------------------------------------------

$_title = 'Order Receipt';
include '../../../../_head.php';
?>

<div class="top-heading-space">
    <h3>Send Receipt</h3>
</div>

<div class="order-card">
    <form class="form" method="post">
        <div class="email-form">
            <label for="email"><h3>Email</h3></label>
            <p>Please input your email, we will send the receipt to your mailbox within 24 hours.</p>
            <input type="email" name="email" maxlength="100" class="text-box" value="<?= ($m ? $m->member_email : '') ?>">
            <?= err('email') ?>
        </div>

        <div class="email-form">
            <section class="button-group">
                <button class="btn">Send</button>
            </section>
        </div>
    </form>
</div>

<br/>
<button data-get="history_detail.php?order_id=<?= $o->order_id ?>">Back</button>

<?php
include '../../../../_foot.php';