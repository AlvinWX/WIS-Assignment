<?php
include '../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email    = req('email');
    $password = req('password');
    $confirm  = req('confirm');
    $name     = req('name');
    $phone    = req('phone');
    $gender   = req('gender');
    $adminTier = req('adminTier'); // Get adminTier value from the form
    $f        = get_file('photo');

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
    else if (!is_unique($email, 'admin', 'adminEmail')) {
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

    // Validate gender
    if (!$gender || !in_array($gender, ['Male', 'Female', 'Other'])) {
        $_err['gender'] = 'Invalid gender';
    }

    // Validate phone
    if (!$phone) {
        $_err['phone'] = 'Required';
    } else if (!preg_match('/^(01)[0-9]{8,9}$/', $phone)) {
        $_err['phone'] = 'Invalid Malaysian phone number. Must start with "01" and contain 10 or 11 digits.';
    }

    // Validate: adminTier
    if (!$adminTier || !in_array($adminTier, ['High', 'Low'])) {
        $_err['adminTier'] = 'Invalid tier. Must be High or Low.';
    }

    // DB operation
    if (!$_err) {
        // (1) Save photo
        $photo = save_photo($f, '../uploads/profiles');

        // (2) Generate adminID (Assuming adminID is a unique value, e.g., 'AM00001')
        $stm = $_db->query('SELECT MAX(adminID) AS maxID FROM admin');
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        $lastID = $result['maxID'] ?? 'AM00000';
        $newID = sprintf('AM%05d', (int)substr($lastID, 2) + 1);

        // (3) Insert user (admin)
        $stm = $_db->prepare('
        INSERT INTO admin (adminID, adminName, adminPassword, adminEmail, adminPhone, adminGender, adminProfilePic, adminTier)
        VALUES (?, ?, SHA1(?), ?, ?, ?, ?, ?)
        ');
        $currentDate = date('Y-m-d');
        $stm->execute([$newID, $name, $password, $email, $phone, $gender, $photo, $adminTier]);


        temp('info', 'Record inserted');
        redirect('/login.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Admin | Register';
include '../_head.php';
?>
<style>
    form {
        margin-top: 200px;
    }
</style>
<form method="post" class="form" enctype="multipart/form-data">
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

    <label for="adminTier">Admin Tier</label>
    <select name="adminTier" id="adminTier">
        <option value="">Select Admin Tier</option>
        <option value="High">High</option>
        <option value="Low">Low</option>
    </select>
    <?= err('adminTier') ?>

    <label for="photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/images/photo.jpg">
    </label>
    <?= err('photo') ?>

    <section>
        <button type="submit">Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';
