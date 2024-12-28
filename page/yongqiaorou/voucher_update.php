<?php
require '../../_base.php';
// ----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT * FROM voucher WHERE voucher_id = ?');
    $stm->execute([$id]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('/');
    }

    extract((array)$s);
}

if (is_post()) {
    $id = req('id'); // <-- From URL
    $voucher_name = req('voucher_name');
    $voucher_desc = req('voucher_desc');
    $voucher_points = req('voucher_points');
    $voucher_min_spend = req('voucher_min_spend');
    $voucher_discount = req('voucher_discount');

    // Validate name
    if ($voucher_name == '') {
        $_err['voucher_name'] = 'Required';
    } else if (strlen($voucher_name) > 100) {
        $_err['voucher_name'] = 'Maximum length 100';
    }

    // Validate desc
    if ($voucher_desc == '') {
        $_err['voucher_desc'] = 'Required';
    } else if (strlen($voucher_desc) > 1000) {
        $_err['voucher_desc'] = 'Maximum length 1000';
    }


    // Validate point
    if ($voucher_points == '') {
        $_err['voucher_points'] = 'Required';
    }

    // Validate min spend
    if ($voucher_min_spend == '') {
        $_err['voucher_min_spend'] = 'Required';
    }

    // Validate discount
    if ($voucher_discount == '') {
        $_err['voucher_discount'] = 'Required';
    }

    
    $voucher_file = isset($_FILES['voucher_img']) ? $_FILES['voucher_img'] : null;
    
    if ($voucher_file && $voucher_file['error'] == UPLOAD_ERR_OK) {
        if ($voucher_file['size'] > 1*1024*1024) {
            $_err['voucher_img'] = 'The uploaded image exceeds the size limit of 1MB.';
        } 
        elseif (!in_array(mime_content_type($voucher_file['tmp_name']), ['image/jpeg', 'image/png', 'image/gif'])) {
            $_err['voucher_img'] = 'The uploaded file is not a valid image. Only JPG, PNG, and GIF are allowed.';
        } 
        else {
            // Process the image
            $voucher_img = uniqid() . '.jpg';
    
            require_once '../../lib/SimpleImage.php';
            $img = new SimpleImage();
            $img->fromFile($voucher_file['tmp_name'])
                ->thumbnail(200, 200)
                ->toFile("../../images/voucher_pic/$voucher_img", 'image/jpeg');
        }
    } else {
        $_err['voucher_img'] = 'Voucher Image is required';
    }

    // Output
    if (!$_err) {
        if (!empty($voucher_img)) {
            $stm = $_db->prepare('UPDATE voucher
                                SET voucher_name = ?, voucher_desc = ?, voucher_points = ?, voucher_min_spend = ?, voucher_discount = ?, voucher_img = ?, voucher_last_update = ?, admin_id = ?
                                WHERE voucher_id = ?');
            $stm->execute([$voucher_name, $voucher_desc, $voucher_points, $voucher_min_spend, $voucher_discount, $voucher_img, date("Y-m-d H:i:s"), $admin_id, $id]);
        } else {
            $stm = $_db->prepare('UPDATE voucher
                                SET voucher_name = ?, voucher_desc = ?, voucher_points = ?, voucher_min_spend = ?, voucher_discount = ?, voucher_last_update = ?, admin_id = ?
                                WHERE voucher_id = ?');
            $stm->execute([$voucher_name, $voucher_desc, $voucher_points, $voucher_min_spend, $voucher_discount, date("Y-m-d H:i:s"), $admin_id, $id]);
        }
        temp('info', 'Voucher updated');
        redirect('/page/yongqiaorou/voucher.php');
    }
}

// ----------------------------------------------------------------------------
$_title = 'Update';
include '../../_admin_head.php';
?>

<button data-get="/page/yongqiaorou/voucher.php"  class="back_button"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

<form method="post" class="form" enctype="multipart/form-data">
    <label for="voucher_id">Id</label>
    <b><?= $id ?></b>
    <?= err('id') ?>

    <label for="voucher_name">Voucher Name</label>
    <?= html_text('voucher_name', 'maxlength="100"') ?>
    <?= err('voucher_name') ?>

    <label>Description</label>
    <?= html_text('voucher_desc', 'maxlength="1000"') ?>
    <?= err('voucher_desc') ?>

    <label>Points to Redeem</label>
    <?= html_number('voucher_points', 1, 100000, 0.01, '') ?>
    <?= err('voucher_points') ?>
    
    <label>Min Spend</label>
    <?= html_number('voucher_min_spend',  1,100000,0.01,'') ?>
    <?= err('voucher_min_spend') ?>
    
    <label>Discounts</label>
    <?= html_number('voucher_discount',  1,100000,0.01,'') ?>
    <?= err('voucher_discount') ?>
    
    <label for="voucher_img">Voucher Image</label>
    <label class="upload" tabindex="0">
        <?= html_file('voucher_img', 'image/*', 'hidden') ?>
        <img src="../../images/voucher_pic/<?= $voucher_img ?>" style="width: 200px; height: 200px;">
    </label>
    <?= err('voucher_img') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../_foot.php';
?>
