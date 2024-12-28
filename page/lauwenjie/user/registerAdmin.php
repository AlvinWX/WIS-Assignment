<?php
include '../../../_base.php';

// ----------------------------------------------------------------------------
auth('admin');
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
    else if (!is_unique($email, 'pending_members', 'member_email') && !is_unique($email, 'member', 'member_email') && !is_unique($email, 'admin', 'admin_email')) {
        $_err['email'] = 'Email already registered (in member or admin).';
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
        $photo = save_photo($f, '../../../images/uploads/profiles');

        // (2) Generate adminID (Assuming adminID is a unique value, e.g., 'AM00001')
        $stm = $_db->query('SELECT MAX(admin_id) AS maxID FROM admin');
        $result = $stm->fetch();
        $lastID = $result->maxID ?? 'AM00000';
        $newID = sprintf('AM%05d', (int)substr($lastID, 2) + 1);

        // (3) Insert user (admin)
        $stm = $_db->prepare('
        INSERT INTO admin (admin_id, admin_name, admin_password, admin_email, admin_phone, admin_gender, admin_profile_pic, admin_tier, status)
        VALUES (?, ?, SHA1(?), ?, ?, ?, ?, ?,?)
        ');
        $currentDate = date('Y-m-d');
        $stm->execute([$newID, $name, $password, $email, $phone, $gender, $photo, $adminTier, 'active']);


        temp('info', 'Record inserted');
        redirect('/page/chanyijing/admin/admin_management/admin_list.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Admin | Register';
include '../../../_head.php';
?>
<link rel="stylesheet" href="/css/wj_app.css">
<div id="info"><?= temp('info')?></div>
<div class="login-container">
    <h2>Admin Registration</h2>
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

    <div style="position: relative;">
        <label for="password">Password</label>
        <?= html_password('password', 'maxlength="100" class="input-field" style="padding-right: 40px;"') ?>
        <img src="/images/closed-eyes.png" alt="Show Password" id="togglePassword" class="eye-icon">
        <?= err('password') ?>
    </div>
    <label for="confirm">Confirm</label>
    <?= html_password('confirm', 'maxlength="100"') ?>
    <?= err('confirm') ?>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="phone">Phone</label>
    <input 
        type="text" 
        id="phone" 
        name="phone" 
        maxlength="11" 
        pattern="^01[0-9]{8,9}$" 
        placeholder="e.g., 0121231234" >
    <small id="phoneError" style="color: red; display: none;">Invalid phone number format.</small>
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

    <section>
        <button type="submit">Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>
</div>
<script>
// Toggle visibility for password
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.querySelector('[name="password"]');
        toggleVisibility(passwordInput, this);
    });

    // Toggle visibility for confirm password
    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
        const confirmPasswordInput = document.querySelector('[name="confirm"]');
        toggleVisibility(confirmPasswordInput, this);
    });

    function toggleVisibility(input, toggleIcon) {
        if (input.type === 'password') {
            input.type = 'text';
            toggleIcon.src = '/images/opened-eye.png';
        } else {
            input.type = 'password';
            toggleIcon.src = '/images/closed-eyes.png';
        }
    }
</script>
<?php
include '../../../_foot.php';
