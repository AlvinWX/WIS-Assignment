<link rel="stylesheet" href="/css/yj_app.css">

<?php
require '../../../../_base.php';

if (is_get()) {
    $member_id = req('member_id');

    $stm = $_db->prepare('SELECT * FROM member WHERE member_id = ?');
    $stm->execute([$member_id]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('member_list.php');
    }

    // Fetch address details
    $stm = $_db->prepare('SELECT * FROM address WHERE member_id = ?');
    $stm->execute([$member_id]);
    $address = $stm->fetch();

    if (!$address) {
        $address = (object)[
            'address_street' => '',
            'address_postcode' => '',
            'address_city' => '',
            'address_state' => '',
        ];
    }

    extract((array)$s);
    extract((array)$address);
}

if (is_post()) {
    $member_id          = req('member_id'); 
    $member_name        = req('member_name');
    $member_gender      = req('member_gender');
    $member_email       = req('member_email'); 
    $member_phone       = req('member_phone'); 
    $address_street     = req('address_street');
    $address_postcode   = req('address_postcode');
    $address_city       = req('address_city');
    $address_state      = req('address_state');

    $f = get_file('member_pic');  

    // Validation errors array
    $_err = [];

    // Validate member profile pic 
    if ($f->tmp_name) {  
        if (!str_Starts_with($f->type, 'image/')) {
            $_err['member_pic'] = 'Must be image';
        }
    }

    // Validate member name
    if ($member_name == '') {
        $_err['member_name'] = 'Required';
    } elseif (strlen($member_name) > 100) {
        $_err['member_name'] = 'Maximum length 100';
    }

    // Validate member gender
    if ($member_gender == '') {
        $_err['member_gender'] = 'Required';
    } elseif (!array_key_exists($member_gender, $_genders)) {
        $_err['member_gender'] = 'Invalid value';
    }

    // Validate member email
    if ($member_email == '') {
        $_err['member_email'] = 'Email is required.';
    } elseif (!filter_var($member_email, FILTER_VALIDATE_EMAIL)) {
        $_err['member_email'] = 'Invalid email format.';
    } elseif (strlen($member_email) > 320) {
        $_err['member_email'] = 'Maximum length 320 characters.';
    }

    // Validate member phone
    if ($member_phone == '') {
        $_err['member_phone'] = 'Phone number is required.';
    } elseif (strlen($member_phone) != 10 && strlen($member_phone) != 11) {
        $_err['member_phone'] = 'Invalid phone number.';
    } elseif ($member_phone[0] != '0') {
        $_err['member_phone'] = 'Phone number must start with 0.';
    } elseif (!ctype_digit($member_phone)) {
        $_err['member_phone'] = 'Phone number must contain only digits.';
    }

    // Validate member address
    if ($address_street == '') {
        $_err['address_street'] = 'Street is required.';
    } elseif (strlen($address_street) > 255) {
        $_err['address_street'] = 'Maximum length 255 characters.';
    }

    if ($address_postcode == '') {
        $_err['address_postcode'] = 'Postcode is required.';
    } elseif (!preg_match('/^\d{1,5}$/', $address_postcode)) {
        $_err['address_postcode'] = 'Invalid postcode format.';
    }

    if ($address_city == '') {
        $_err['address_city'] = 'City is required.';
    } elseif (strlen($address_city) > 20) {
        $_err['address_city'] = 'Maximum length 20 characters.';
    }

    if ($address_state == '') {
        $_err['address_state'] = 'State is required.';
    } elseif (strlen($address_state) > 50) {
        $_err['address_state'] = 'Maximum length 50 characters.';
    }

    // Output
    if (!$_err) {
        
        $f = isset($_FILES['member_pic']) ? $_FILES['member_pic'] : null;

        if ($f && $f['error'] == UPLOAD_ERR_OK) {
            $member_pic = uniqid() . '.jpg';
            require_once '../../../../lib/SimpleImage.php';
            $img = new SimpleImage();
            
            $img->fromFile($f['tmp_name']) 
                ->thumbnail(200, 200)
                ->toFile("../../../../images/uploads/profiles/$member_pic", 'image/jpeg');
        }

        if(!empty($member_pic)){
            $stm = $_db->prepare('UPDATE member
            SET member_profile_pic = ?, member_name = ?, member_gender = ?, member_email = ?, member_phone = ?
            WHERE member_id = ?');
            $stm->execute([$member_pic, $member_name, $member_gender, $member_email, $member_phone, $member_id]);
        } else {
            $stm = $_db->prepare('UPDATE member
            SET member_name = ?, member_gender = ?, member_email = ?, member_phone = ?
            WHERE member_id = ?');
            $stm->execute([$member_name, $member_gender, $member_email, $member_phone, $member_id]);
        }

        $stm = $_db->prepare('UPDATE address
                              SET address_street = ?, address_postcode = ?, address_city = ?, address_state = ?
                              WHERE member_id = ?');
        $stm->execute([$address_street, $address_postcode, $address_city, $address_state, $member_id]);

        temp('info', 'Member details updated successfully.');
        redirect('member_list.php');
    }
}

$_title = 'Update Member';
include '../../../../_head.php';
?>

<div class="top-heading-space">
    <h3>Update Member Details</h3>
</div>

<form method="post" class="update-form" enctype="multipart/form-data">
    <label for="member_pic">Profile Picture</label>
    <label class="upload" tabindex="0">
        <?= html_file('member_pic','image/*','hidden') ?>
        <img src="../../../../images/uploads/profiles/<?= $s->member_profile_pic ?>"/>
    </label>
    <?= err('member_pic') ?>

    <label for="member_id">Member ID</label>
    <b><?= $member_id ?></b>
    <?= err('member_id') ?>

    <label for="member_name">Name</label>
    <?= html_text('member_name', 'maxlength="100"') ?>
    <?= err('member_name') ?>

    <label for="member_gender">Gender</label>
    <?= html_radios('member_gender', $_genders, $member_gender) ?>
    <?= err('member_gender') ?>

    <label for="member_email">Email</label>
    <?= html_text('member_email') ?>
    <?= err('member_email') ?>

    <label for="member_phone">Phone</label>
    <?= html_text('member_phone') ?>
    <?= err('member_phone') ?>

    <label for="address_street">Street</label>
    <?= html_text('address_street', 'maxlength="255"', $address_street) ?>
    <?= err('address_street') ?>

    <label for="address_postcode">Postcode</label>
    <?= html_text('address_postcode', 'maxlength="5"', $address_postcode) ?>
    <?= err('address_postcode') ?>

    <label for="address_city">City</label>
    <?= html_text('address_city', 'maxlength="100"', $address_city) ?>
    <?= err('address_city') ?>

    <label for="address_state">State</label>
    <?= html_text('address_state', 'maxlength="100"', $address_state) ?>
    <?= err('address_state') ?>

    <section>
        <button data-get="member_list.php">Cancel</button>
        <button>Update</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../../../_foot.php';
