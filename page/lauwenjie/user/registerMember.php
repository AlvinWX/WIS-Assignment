<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php
include '../../../_base.php';

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

    // reCAPTCHA v2 Validation
    $recaptchaResponse = req('g-recaptcha-response'); // Use 'g-recaptcha-response' for v2
    $recaptchaSecret = '6Ld8E6YqAAAAAAjbJFpnbTw7gYkixRRKAomZOe2M'; // Replace with your actual secret key
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
    $responseKeys = json_decode($response, true);

    // Check reCAPTCHA validation
    if (!$responseKeys['success']) {
        $_err['recaptcha'] = 'Please verify you are not a robot.';
    }
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
    else if (!is_unique($email, 'pending_members', 'member_email') && !is_unique($email, 'member', 'member_email')) {
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
        $_err['phone'] = 'Phone number is required.';
    } else if (!preg_match('/^(01)[0-9]{8,9}$/', $phone)) {
        $_err['phone'] = 'Invalid Malaysian phone number. Must start with "01" and contain 10 or 11 digits.';
    } else if (!is_unique($phone, 'pending_members', 'member_phone') && !is_unique($phone, 'member', 'member_phone')) {
        $_err['phone'] = 'Phone number already registered.';
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
        $photo = save_photo($f, '../uploads/profiles');

        $stm = $_db->query('SELECT MAX(member_id) AS maxID FROM pending_members');
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        $lastID = $result['maxID'] ?? 'PM00000';
        $newID = sprintf('PM%05d', (int)substr($lastID, 2) + 1);

        $token = sha1(uniqid() . rand());
        $currentDate = date('Y-m-d');

        try {
            $stm = $_db->prepare('
                INSERT INTO pending_members (member_id, member_name, member_password, member_email, member_phone, member_gender, member_profile_pic, member_date_joined, token)
                VALUES (?, ?, SHA1(?), ?, ?, ?, ?, ?, ?)
            ');
            $stm->execute([$newID, $name, $password, $email, $phone, $gender, $photo, $currentDate, $token]);

            $url = base("user/activate.php?token=$token");
            $m = get_mail();
            $m->addAddress($email, $name);
            $m->addEmbeddedImage("../uploads/profiles/$photo", 'photo');
            $m->isHTML(true);
            $m->Subject = 'Activate your account';
            $m->Body = "
                <img src='cid:photo' style='width: 200px; height: 200px; border: 1px solid #333'>
                <p>Dear $name,</p>
                <h1 style='color: red'>Activate Your Account</h1>
                <p>Please click <a href='$url'>here</a> to activate your account.</p>
                <p>From, 😺 Admin</p>
            ";

            if ($m->send()) {
                echo 'Activation link sent to your email.';
            } else {
                $_err['email'] = 'Failed to send email.';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_err['email'] = 'Email already registered.';
            } else {
                throw $e;
            }
        }

        redirect('/login.php');
    }
}  

// ----------------------------------------------------------------------------

$_title = 'User | Register Member';
include '../../../_head.php';
?>

<div class="register-container">
<h2>Register as Member</h2>
<form method="post" class="form" enctype="multipart/form-data">

    <label for="photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('photo','image/*','hidden') ?>
        <img src="/images/photo.jpg" id="photoPreview">
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

    <div style="position: relative;">
        <label for="confirm">Confirm Password</label>
        <?= html_password('confirm', 'maxlength="100" class="input-field" style="padding-right: 40px;"') ?>
        <img src="/images/closed-eyes.png" alt="Show Password" id="toggleConfirmPassword" class="eye-icon">
        <?= err('confirm') ?>
    </div>

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
        placeholder="e.g., 0121231234" 
        required
        oninput="validatePhone()">
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

    <!-- Visible reCAPTCHA Checkbox -->
    <div class="g-recaptcha" data-sitekey="6Ld8E6YqAAAAAK1TJ3ULHmONjjqPNp-_10Le6k9k"></div>
    <?= err('recaptcha') ?>
</br>
    <section>
        <button type="submit" class="register-btn">Submit</button>
        <button type="reset" class="register-btn">Reset</button>
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