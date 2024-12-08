<?php
require '../../../../_base.php';

// ----------------------------------------------------------------------------

if (is_get()) {
    $adminID = req('adminID');

    $stm = $_db->prepare('SELECT * FROM admin WHERE adminID = ?');
    $stm->execute([$adminID]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('admin_list.php');
    }

    // Extract the values from the result
    extract((array)$s);
}

if (is_post()) {
    // Input
    $adminID       = req('adminID'); // From hidden field or URL
    $adminName     = req('adminName');
    $adminGender   = req('adminGender');
    $adminEmail    = req('adminEmail'); // Optional
    $adminPhone    = req('adminPhone'); // Optional

    // Validation errors array
    $_err = [];

    // Validate adminName
    if ($adminName == '') {
        $_err['adminName'] = 'Required';
    } elseif (strlen($adminName) > 100) {
        $_err['adminName'] = 'Maximum length 100';
    }

    // Validate adminGender
    if ($adminGender == '') {
        $_err['adminGender'] = 'Required';
    } else if (!array_key_exists($adminGender, $_genders)) {
        $_err['name'] = 'Invalid value';
    }

    // Output
    if (!$_err) {
        $stm = $_db->prepare('UPDATE admin
                              SET adminName = ?, adminGender = ?, adminEmail = ?, adminPhone = ?
                              WHERE adminID = ?');
        $stm->execute([$adminName, $adminGender, $adminEmail, $adminPhone, $adminID]);

        temp('info', 'Record updated successfully.');
        redirect('admin_list.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Update admin';
include '../../../../_head.php';
?>

<form method="post" class="form">
    <label for="adminID">admin ID</label>
    <b><?= $adminID ?></b>
    <?= err('adminID') ?>

    <label for="adminName">Name</label>
    <?= html_text('adminName', 'maxlength="100"') ?>
    <?= err('adminName') ?>

    <label for="adminGender">Gender</label>
    <?= html_radios('adminGender', $_genders, $adminGender) ?>
    <?= err('adminGender') ?>

    <label for="adminEmail">Email</label>
    <?= html_text('adminEmail') ?>
    <?= err('adminEmail') ?>

    <label for="adminPhone">Phone</label>
    <?= html_text('adminPhone') ?>
    <?= err('adminPhone') ?>

    <section>
        <button data-get="admin_list.php">Cancel</button>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../../../_foot.php';
