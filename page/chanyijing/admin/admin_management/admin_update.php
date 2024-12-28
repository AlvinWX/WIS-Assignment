<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

if (is_get()) {
    $admin_id = req('admin_id');

    $stm = $_db->prepare('SELECT * FROM admin WHERE admin_id = ?');
    $stm->execute([$admin_id]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('admin_list.php');
    }

    extract((array)$s);
}

if (is_post()) {
    $admin_id           = req('admin_id'); 
    $admin_name         = req('admin_name');
    $admin_gender       = req('admin_gender');
    $admin_email        = req('admin_email'); 
    $admin_phone        = req('admin_phone'); 

    $f = get_file('admin_pic');

    // Validation errors array
    $_err = [];

    // Validate admin profile pic
    if ($f->tmp_name) { 
        if (!str_Starts_with($f->type, 'image/')) { 
            $_err['admin_pic'] = 'Must be image';
        } 
    }

    // Validate admin name
    if ($admin_name == '') {
        $_err['admin_name'] = 'Required';
    } elseif (strlen($admin_name) > 100) {
        $_err['admin_name'] = 'Maximum length 100';
    }

    // Validate admin gender
    if ($admin_gender == '') {
        $_err['admin_gender'] = 'Required';
    } else if (!array_key_exists($admin_gender, $_genders)) {
        $_err['name'] = 'Invalid value';
    }

    // Validate admin email
    if ($admin_email == '') {
        $_err['admin_email'] = 'Email is required.';
    } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $_err['admin_email'] = 'Invalid email format.';
    } elseif (strlen($admin_email) > 320) {
        $_err['admin_email'] = 'Maximum length 320 characters.';
    }

    // Validate admin phone
    if ($admin_phone == '') {
        $_err['admin_phone'] = 'Phone number is required.';
    } elseif (strlen($admin_phone) != 10 && strlen($admin_phone) != 11) {
        $_err['admin_phone'] = 'Invalid phone number.';
    } elseif ($admin_phone[0] != '0') {
        $_err['admin_phone'] = 'Phone number must start with 0.';
    } elseif (!ctype_digit($admin_phone)) {
        $_err['admin_phone'] = 'Phone number must contain only digits.';
    }

    // Output
    if (!$_err) {

        $f = isset($_FILES['admin_pic']) ? $_FILES['admin_pic'] : null;

        if ($f && $f['error'] == UPLOAD_ERR_OK) {
            $admin_pic = uniqid() . '.jpg';
            require_once '../../../../lib/SimpleImage.php';
            $img = new SimpleImage();
            
            $img->fromFile($f['tmp_name']) 
                ->thumbnail(200, 200)
                ->toFile("../../../../images/uploads/profiles/$admin_pic", 'image/jpeg');
        }

        if(!empty($admin_pic)){
            $stm = $_db->prepare('UPDATE admin
            SET admin_profile_pic = ?, admin_name = ?, admin_gender = ?, admin_email = ?, admin_phone = ?
            WHERE admin_id = ?');
            $stm->execute([$admin_pic, $admin_name, $admin_gender, $admin_email, $admin_phone, $admin_id]);
        } else {
            $stm = $_db->prepare('UPDATE admin
            SET admin_name = ?, admin_gender = ?, admin_email = ?, admin_phone = ?
            WHERE admin_id = ?');
            $stm->execute([$admin_name, $admin_gender, $admin_email, $admin_phone, $admin_id]);
        }

        temp('info', 'Admin details updated successfully.');
        redirect('admin_list.php');
    }
}

$_title = 'Update Admin';
include '../../../../_head.php';
?>

<div class="top-heading-space">
    <h3>Update Admin Details</h3>
</div>

<form method="post" class="update-form" enctype="multipart/form-data">
    <label for="admin_pic">Profile Picture</label>
    <label class="upload" tabindex="0">
        <?= html_file('admin_pic','image/*','hidden') ?>
        <img src="../../../../images/uploads/profiles/<?= $s->admin_profile_pic ?>"/>
    </label>
    <?= err('admin_pic') ?>

    <label for="admin_id">Admin ID</label>
    <b><?= $admin_id ?></b>
    <?= err('admin_id') ?>

    <label for="admin_name">Name</label>
    <?= html_text('admin_name', 'maxlength="100"') ?>
    <?= err('admin_name') ?>

    <label for="admin_gender">Gender</label>
    <?= html_radios('admin_gender', $_genders, $admin_gender) ?>
    <?= err('admin_gender') ?>

    <label for="admin_email">Email</label>
    <?= html_text('admin_email') ?>
    <?= err('admin_email') ?>

    <label for="admin_phone">Phone</label>
    <?= html_text('admin_phone') ?>
    <?= err('admin_phone') ?>

    <section>
        <button data-get="admin_list.php">Cancel</button>
        <button>Update</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../../../_foot.php';
