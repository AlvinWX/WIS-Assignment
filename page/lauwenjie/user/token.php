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

    if (empty($_err)) {
        // Update the password
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

        temp('success', 'Password updated successfully.');
        redirect('/login.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Reset Password';
include '../../../_head.php';
?>

<div class="login-container">
    <form method="post" class="form">
        <h2>Reset Password</h2>
        <label for="password">New Password</label>
        <?= html_password('password', 'maxlength="100"') ?>
        <?= err('password') ?>
        
        <label for="confirm_password">Confirm Password</label>
        <?= html_password('confirm_password', 'maxlength="100"') ?>
        <?= err('confirm_password') ?>
        <section>
            <button>Submit</button>
            <button type="reset">Reset</button>
        </section>
    </form>
</div>

<?php
include '../../../_foot.php';
?>