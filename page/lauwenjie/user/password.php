<?php
include '../../../_base.php';

// ----------------------------------------------------------------------------

// Authenticated users (Members only)
auth('member'); // Assume this function ensures only members can access

$user = $_SESSION['user'] ?? null;
$member_id = $user->member_id;
if(empty($member_id)){
    redirect('/login.php');
    temp('info',"Unauthorized Access");
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

    // Check if the new password is the same as the current password
    $stm = $_db->prepare('
        SELECT member_password FROM member WHERE member_id = ?
    ');
    $stm->execute([$member_id]);
    $current_password = $stm->fetchColumn();

    if (sha1($new_password) === $current_password) {
        $_err['new_password'] = 'New password cannot be the same as the current password';
    }

    // DB operation
    if (!$_err) {
        // Update member password
        $stm = $_db->prepare('
            UPDATE member
            SET member_password = SHA1(?)
            WHERE member_id = ?
        ');
        $stm->execute([$new_password, $member_id]);
        temp('info', 'Password updated');
        redirect('/');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Member | Password';
include '../../../_head.php';
?>

<div class="login-container">
        <h2>Change Password </h2>
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
