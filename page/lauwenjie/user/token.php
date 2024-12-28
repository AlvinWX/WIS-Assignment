<?php
include '../../../_base.php';

// ----------------------------------------------------------------------------

// Delete expired tokens
$_db->query('DELETE FROM token WHERE expire < NOW()');
$_db->query('DELETE FROM token_admin WHERE expire < NOW()');

$id = req('id');

// Validate token ID
$stm = $_db->prepare('
    SELECT member_id AS user_id, "member" AS user_type
    FROM token WHERE id = ?
    UNION
    SELECT admin_id AS user_id, "admin" AS user_type
    FROM token_admin WHERE id = ?
');
$stm->execute([$id, $id]);
$user = $stm->fetch();

if (!$user) {
    temp('info', 'Invalid token. Try again');
    redirect('/');
}

if (is_post()) {
    $password = req('password');
    $confirmPassword = req('confirm_password');

    // Validate: password and confirmation
    if ($password == '') {
        $_err['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $_err['password'] = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $_err['confirm_password'] = 'Passwords do not match.';
    }

    // Check if the new password is the same as the current password (only for admin/member)
    $stm = $_db->prepare('
        SELECT member_password FROM member WHERE member_id = ?
        UNION
        SELECT admin_password FROM admin WHERE admin_id = ?
    ');
    $stm->execute([$user->user_id, $user->user_id]);
    $current_password = $stm->fetchColumn();

    if (sha1($password) === $current_password) {
        $_err['password'] = 'New password cannot be the same as the current password.';
    }

    // If no errors, update the password
    if (empty($_err)) {
        if ($user->user_type === 'member') {
            $stm = $_db->prepare('
                UPDATE member
                SET member_password = SHA1(?)
                WHERE member_id = ?
            ');
            $stm->execute([$password, $user->user_id]);

            // Remove token after successful password update
            $stm = $_db->prepare('DELETE FROM token WHERE id = ?');
            $stm->execute([$id]);
        } elseif ($user->user_type === 'admin') {
            $stm = $_db->prepare('
                UPDATE admin
                SET admin_password = SHA1(?)
                WHERE admin_id = ?
            ');
            $stm->execute([$password, $user->user_id]);

            // Remove token after successful password update
            $stm = $_db->prepare('DELETE FROM token_admin WHERE id = ?');
            $stm->execute([$id]);
        }

        temp('info', 'Password updated successfully.');
        redirect('/login.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Reset Password';
include '../../../_head.php';
?>
<link rel="stylesheet" href="/css/wj_app.css">
<div id="info"><?= temp('info')?></div>
<div class="login-container">
    <h2>Reset Password</h2>
    <form method="post" class="form">

        <!-- New Password -->
        <div style="position: relative;">
            <label for="password">New Password</label>
            <?= html_password('password', 'maxlength="100" class="input-field" style="padding-right: 40px;"') ?>
            <img src="/images/closed-eyes.png" alt="Show Password" class="eye-icon">
            <?= err('password') ?>
        </div>

        <!-- Confirm New Password -->
        <div style="position: relative;">
            <label for="confirm_password">Confirm New Password</label>
            <?= html_password('confirm_password', 'maxlength="100" class="input-field" style="padding-right: 40px;"') ?>
            <img src="/images/closed-eyes.png" alt="Show Password" class="eye-icon">
            <?= err('confirm_password') ?>
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
