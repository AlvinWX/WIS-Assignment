<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

if (is_get()) {
    $memberID = req('memberID');

    $stm = $_db->prepare('SELECT * FROM member WHERE memberID = ?');
    $stm->execute([$memberID]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('member_list.php');
    }

    // Fetch address details
    $stm = $_db->prepare('SELECT * FROM address WHERE memberID = ?');
    $stm->execute([$memberID]);
    $address = $stm->fetch();

    if (!$address) {
        $address = (object)[
            'addressStreet' => '',
            'addressPostcode' => '',
            'addressCity' => '',
            'addressState' => '',
        ];
    }

    // Extract the values from the result
    extract((array)$s);
    extract((array)$address);
}

if (is_post()) {
    // Input
    $memberID       = req('memberID'); // From hidden field or URL
    $memberName     = req('memberName');
    $memberGender   = req('memberGender');
    $memberEmail    = req('memberEmail'); // Optional
    $memberPhone    = req('memberPhone'); // Optional
    $addressStreet  = req('addressStreet');
    $addressPostcode= req('addressPostcode');
    $addressCity    = req('addressCity');
    $addressState   = req('addressState');

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

    // Validate address fields
    if ($addressStreet == '') {
        $_err['addressStreet'] = 'Required';
    } elseif (strlen($addressStreet) > 255) {
        $_err['addressStreet'] = 'Maximum length 255';
    }

    if ($addressPostcode == '') {
        $_err['addressPostcode'] = 'Required';
    } elseif (strlen($addressPostcode) > 5) {
        $_err['addressPostcode'] = 'Maximum length for postcode is 5';
    }

    if ($addressCity == '') {
        $_err['addressCity'] = 'Required';
    } elseif (strlen($addressCity) > 100) {
        $_err['addressCity'] = 'Maximum length 100';
    }

    if ($addressState == '') {
        $_err['addressState'] = 'Required';
    } elseif (strlen($memberName) > 100) {
        $_err['addressState'] = 'Maximum length 100';
    }

    // Output
    if (!$_err) {
        $stm = $_db->prepare('UPDATE member
                              SET memberName = ?, memberGender = ?, memberEmail = ?, memberPhone = ?
                              WHERE memberID = ?');
        $stm->execute([$memberName, $memberGender, $memberEmail, $memberPhone, $memberID]);

        $stm = $_db->prepare('UPDATE address
                              SET addressStreet = ?, addressPostcode = ?, addressCity = ?, addressState = ?
                            WHERE memberID = ?');
        $stm->execute([$addressStreet, $addressPostcode, $addressCity, $addressState, $memberID]);

        
        temp('info', 'Record updated successfully.');
        redirect('member_list.php');
    }
}

$_title = 'Update Member';
include '../../../../_head.php';
?>

<div class="top-heading-space">
    <h3>Update Member Details</h3>
</div>

<form method="post" class="update-form">
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

    <label for="addressStreet">Street</label>
    <?= html_text('addressStreet', 'maxlength="255"', $addressStreet) ?>
    <?= err('address') ?>

    <label for="addressPostcode">Postcode</label>
    <?= html_text('addressPostcode', 'maxlength="5"', $addressPostcode) ?>
    <?= err('address') ?>

    <label for="addressCity">City</label>
    <?= html_text('addressCity', 'maxlength="100"', $addressCity) ?>
    <?= err('address') ?>

    <label for="addressState">State</label>
    <?= html_text('addressState', 'maxlength="100"', $addressState) ?>
    <?= err('address') ?>

    <section>
        <button data-get="member_list.php">Cancel</button>
        <button>Update</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../../../_foot.php';
