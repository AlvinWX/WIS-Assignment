<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

// ----------------------------------------------------------------------------

if (is_get()) {
    $memberID = req('memberID');

    $stm = $_db->prepare('SELECT * FROM member WHERE memberID = ?');
    $stm->execute([$memberID]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('member_list.php');
    }

    // Extract the values from the result
    extract((array)$s);
}

if (is_post()) {
    // Input
    $memberID       = req('memberID'); // From hidden field or URL
    $memberName     = req('memberName');
    $memberGender   = req('memberGender');
    $memberEmail    = req('memberEmail'); // Optional
    $memberPhone    = req('memberPhone'); // Optional

    // Validation errors array
    $_err = [];

    // Validate memberName
    if ($memberName == '') {
        $_err['memberName'] = 'Required';
    } elseif (strlen($memberName) > 100) {
        $_err['memberName'] = 'Maximum length 100';
    }

    // Validate memberGender
    if ($memberGender == '') {
        $_err['memberGender'] = 'Required';
    } else if (!array_key_exists($memberGender, $_genders)) {
        $_err['name'] = 'Invalid value';
    }

    // Output
    if (!$_err) {
        $stm = $_db->prepare('UPDATE member
                              SET memberName = ?, memberGender = ?, memberEmail = ?, memberPhone = ?
                              WHERE memberID = ?');
        $stm->execute([$memberName, $memberGender, $memberEmail, $memberPhone, $memberID]);

        temp('info', 'Record updated successfully.');
        redirect('member_list.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Update Member';
include '../../../../_head.php';
?>

<form method="post" class="form">
    <label for="memberID">Member ID</label>
    <b><?= $memberID ?></b>
    <?= err('memberID') ?>

    <label for="memberName">Name</label>
    <?= html_text('memberName', 'maxlength="100"') ?>
    <?= err('memberName') ?>

    <label for="memberGender">Gender</label>
    <?= html_radios('memberGender', $_genders, $memberGender) ?>
    <?= err('memberGender') ?>

    <label for="memberEmail">Email</label>
    <?= html_text('memberEmail') ?>
    <?= err('memberEmail') ?>

    <label for="memberPhone">Phone</label>
    <?= html_text('memberPhone') ?>
    <?= err('memberPhone') ?>

    <section>
        <button data-get="member_list.php">Cancel</button>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../../../_foot.php';
