<?php
include '../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email    = req('email');
    $password = req('password');
    $confirm  = req('confirm');
    $name     = req('name');
    $phone  = req('phone');
    $gender = req('gender');
    $f = get_file('photo');
    $address  = req('address');
$postcode = req('postcode');
$city     = req('city');
$state    = req('state');
    

    // Validate: email
    if (!$email) {
        $_err['email'] = 'Required';
    }
    else if (strlen($email) > 100) {
        $_err['email'] = 'Maximum 100 characters';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else if (!is_unique($email, 'member', 'memberEmail')) {
        $_err['email'] = 'Duplicated';
    }

    // Validate: password
    if (!$password) {
        $_err['password'] = 'Required';
    }
    else if (strlen($password) < 5 || strlen($password) > 100) {
        $_err['password'] = 'Between 5-100 characters';
    }

    // Validate: confirm
    if (!$confirm) {
        $_err['confirm'] = 'Required';
    }
    else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
        $_err['confirm'] = 'Between 5-100 characters';
    }
    else if ($confirm != $password) {
        $_err['confirm'] = 'Not matched';
    }

    // Validate: name
    if (!$name) {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    // Validate: photo (file)
    if (!$f) {
        $_err['photo'] = 'Required';
    }
    else if (!str_starts_with($f->type, 'image/')) {
        $_err['photo'] = 'Must be image';
    }
    else if ($f->size > 1 * 1024 * 1024) {
        $_err['photo'] = 'Maximum 1MB';
    }

    //validate gender
    if (!$gender || !in_array($gender, ['Male', 'Female', 'Other'])) {
        $_err['gender'] = 'Invalid gender';
    }

    // validate phone
    if (!$phone) {
        $_err['phone'] = 'Required';
    } else if (!preg_match('/^(01)[0-9]{8,9}$/', $phone)) {
        $_err['phone'] = 'Invalid Malaysian phone number. Must start with "01" and contain 10 or 11 digits.';
    }
    // Validate: address
if (!$address) {
    $_err['address'] = 'Required';
}
else if (strlen($address) > 255) {
    $_err['address'] = 'Maximum 255 characters';
}

// Validate: postcode
if (!$postcode) {
    $_err['postcode'] = 'Required';
} else if (!preg_match('/^[0-9]{5,10}$/', $postcode)) {
    $_err['postcode'] = 'Invalid postcode';
}

// Validate: city
if (!$city) {
    $_err['city'] = 'Required';
} else if (strlen($city) > 100) {
    $_err['city'] = 'Maximum 100 characters';
}

// Validate: state
if (!$state) {
    $_err['state'] = 'Required';
} else if (strlen($state) > 100) {
    $_err['state'] = 'Maximum 100 characters';
}

    // DB operation
    if (!$_err) {
            // (1) Save photo
    $photo = save_photo($f, '../uploads/profiles');

    // (2) Generate memberID (Assuming memberID is a unique value, e.g., 'MB00001')
    $stm = $_db->query('SELECT MAX(memberID) AS maxID FROM member');
    $result = $stm->fetch(PDO::FETCH_ASSOC);
    $lastID = $result['maxID'] ?? 'MB00000';
    $newID = sprintf('MB%05d', (int)substr($lastID, 2) + 1);

    // (3) Insert user (member)
    $stm = $_db->prepare('
        INSERT INTO member (memberID, memberName, memberPassword, memberEmail, memberPhone, memberGender, memberProfilePic, memberDateJoined)
        VALUES (?, ?, SHA1(?), ?, ?, ?, ?, ?)
    ');
    $currentDate = date('Y-m-d');
    $stm->execute([$newID, $name, $password, $email, $phone, $gender, $photo, $currentDate]);

// Generate a unique address ID
$addressID = sprintf('AD%05d', (int)substr($lastID, 2) + 1);

// Check if the addressID already exists in the database
$checkStm = $_db->prepare('SELECT address_id FROM address WHERE address_id = ?');
$checkStm->execute([$addressID]);

// If the addressID exists, regenerate it
while ($checkStm->fetch()) {
    // Increment the address ID
    $addressID = sprintf('AD%05d', (int)substr($addressID, 2) + 1);
    // Re-run the check
    $checkStm->execute([$addressID]);
}

// Now perform the insert
$stm = $_db->prepare('
    INSERT INTO address (address_id, street, postcode, city, state, member_id)
    VALUES (?, ?, ?, ?, ?, ?)
');
$stm->execute([$addressID, $address, $postcode, $city, $state, $newID]);

temp('info', 'Record inserted');
redirect('/login.php');
    }}

// ----------------------------------------------------------------------------

$_title = 'User | Register Member';
include '../_head.php';
?>
<div class="register-container">
<h2>Register as Member</h2>
<form method="post" class="form" enctype="multipart/form-data">
    
    <label for="photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/images/photo.jpg">
    </label>
    <?= err('photo') ?> 

    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>

    <label for="password">Password</label>
    <?= html_password('password', 'maxlength="100"') ?>
    <?= err('password') ?>

    <label for="confirm">Confirm</label>
    <?= html_password('confirm', 'maxlength="100"') ?>
    <?= err('confirm') ?>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="phone">Phone</label>
    <?= html_text('phone', 'maxlength="15" pattern="[0-9+()-]{10,15}" placeholder="Enter phone number"') ?>
    <?= err('phone') ?>

    <label for="gender">Gender</label>
    <select name="gender" id="gender">
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select>
    <?= err('gender') ?>

    <label for="address">Street Address</label>
    <?= html_text('address', 'maxlength="255"') ?>
    <?= err('address') ?>

    <label for="postcode">Postcode</label>
    <?= html_text('postcode', 'maxlength="10" pattern="[0-9]{5,10}"') ?>
    <?= err('postcode') ?>

    <label for="city">City</label>
    <?= html_text('city', 'maxlength="100"') ?>
    <?= err('city') ?>

    <label for="state">State</label>
    <?= html_text('state', 'maxlength="100"') ?>
    <?= err('state') ?>

    <section>
        <button type="submit" class="register-btn">Submit</button>
        <button type="reset" class="register-btn">Reset</button>
    </section>
</form>
</div>

<?php
include '../_foot.php';