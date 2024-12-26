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

    // Extract the values from the result
    extract((array)$s);
}

if (is_post()) {
    // Input
    $admin_id       = req('admin_id'); 
    $admin_name     = req('admin_name');
    $admin_gender   = req('admin_gender');
    $admin_email    = req('admin_email'); 
    $admin_phone    = req('admin_phone'); 
    // Validation errors array
    $_err = [];

    // Validate admin_name
    if ($admin_name == '') {
        $_err['admin_name'] = 'Required';
    } elseif (strlen($admin_name) > 100) {
        $_err['admin_name'] = 'Maximum length 100';
    }

    // Validate admin_gender
    if ($admin_gender == '') {
        $_err['admin_gender'] = 'Required';
    } else if (!array_key_exists($admin_gender, $_genders)) {
        $_err['name'] = 'Invalid value';
    }

    // Validate admin_email
    if ($admin_email == '') {
        $_err['admin_email'] = 'Email is required.';
    } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $_err['admin_email'] = 'Invalid email format.';
    } elseif (strlen($admin_email) > 320) {
        $_err['admin_email'] = 'Maximum length 320 characters.';
    }

    // Output
    if (!$_err) {
        $stm = $_db->prepare('UPDATE admin
                              SET admin_name = ?, admin_gender = ?, admin_email = ?, admin_phone = ?
                              WHERE admin_id = ?');
        $stm->execute([$admin_name, $admin_gender, $admin_email, $admin_phone, $admin_id]);

        temp('info', 'Record updated successfully.');
        redirect('admin_list.php');
    }
}

$_title = 'Update admin';
include '../../../../_head.php';
?>

<div class="top-heading-space">
    <h3>Update Admin Details</h3>
</div>

<form method="post" class="update-form">
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
