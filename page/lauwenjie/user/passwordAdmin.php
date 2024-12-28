<?php
include '../../../_base.php';

// ----------------------------------------------------------------------------

// Authenticated users (Admins only)
auth('admin'); // Assume this function ensures only admins can access

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

if (is_post()) {

    $new_password = req('new_password');
    $confirm      = req('confirm');


    // Validate: new_password
    if ($new_password == '') {
        $_err['new_password'] = 'Required';
    }
    else if (strlen($new_password) < 5 || strlen($new_password) > 100) {
        $_err['new_password'] = 'Between 5-100 characters';
    }

    // Validate: confirm
    if (!$confirm) {
        $_err['confirm'] = 'Required';
    }
    else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
        $_err['confirm'] = 'Between 5-100 characters';
    }
    else if ($confirm != $new_password) {
        $_err['confirm'] = 'Not matched';
    }

    // DB operation
    if (!$_err) {
        // Update admin password
        $stm = $_db->prepare('
            UPDATE admin
            SET admin_password = SHA1(?)
            WHERE admin_id = ?
        ');
        $stm->execute([$new_password, $admin_id]);

        temp('info', 'Password updated');
        redirect('/');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Admin | Password';
include '../../../_head.php';
?>
<link rel="stylesheet" href="/css/wj_app.css">
<div class="login-container">
    <h2>Change Password</h2>
    <form method="post" class="form">

        <!-- New Password -->
        <div style="position: relative;">
            <label for="new_password">New Password</label>
            <?= html_password('new_password', 'maxlength="100" class="input-field" style="padding-right: 40px;"') ?>
            <img src="/images/closed-eyes.png" alt="Show Password" id="togglePassword" class="eye-icon">
            <?= err('new_password') ?>
        </div>

        <!-- Confirm New Password -->
        <div style="position: relative;">
            <label for="confirm">Confirm New Password</label>
            <?= html_password('confirm', 'maxlength="100" class="input-field" style="padding-right: 40px;"') ?>
            <img src="/images/closed-eyes.png" alt="Show Password" id="togglePassword" class="eye-icon">
            <?= err('confirm') ?>
        </div>

        <section>
            <button class="login-btn">Submit</button>
            <button type="reset" class="login-btn">Reset</button>
        </section>
    </form>
</div>

<script>
    // Toggle password visibility
    document.querySelectorAll('.eye-icon').forEach(item => {
        item.addEventListener('click', function() {
            const passwordField = this.previousElementSibling;
            if (passwordField.type === "password") {
                passwordField.type = "text";
                this.src = "/images/opened-eye.png"; // Change icon to open eye
            } else {
                passwordField.type = "password";
                this.src = "/images/closed-eyes.png"; // Change icon to closed eye
            }
        });
    });
</script>

<?php
include '../../../_foot.php';
?>
